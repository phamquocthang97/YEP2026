<?php

require dirname(__DIR__) . '/bootstrap.php';

$event = loadEvent();
$employee = findInvitee($_GET['id'] ?? null);
$wantsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

function respondJson(int $status, array $payload): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    exit;
}

// Xác nhận tham dự nằm trong trang chính: mở thẳng rsvp.php thì về đúng section
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $query = $employee !== null ? '?id=' . rawurlencode((string) employeeUuid($employee)) : '';
    header('Location: ./' . $query . '#xac-nhan', true, 302);
    exit;
}

// Chỉ ghi phản hồi cho link mời hợp lệ: không có người nhận thì không biết ai trả lời
if ($employee === null) {
    if ($wantsJson) {
        respondJson(404, ['ok' => false]);
    }

    http_response_code(404);
    require BASE_PATH . '/views/invalid.php';
    exit;
}

$attending = $_POST['attending'] ?? null;
$errors = [];

if (!in_array($attending, ['yes', 'no'], true)) {
    $errors['attending'] = 'Vui lòng chọn bạn có tham gia được hay không.';
}

$rsvp = null;

if ($errors === []) {
    $rsvp = [
        'name'      => trim((string) ($employee['name'] ?? '')),
        'attending' => $attending === 'yes',
        'email'     => $employee['email'] ?? null,
        'uuid'      => employeeUuid($employee),
    ];

    try {
        rsvpLog()->add($rsvp);
    } catch (RuntimeException $e) {
        error_log('rsvp.php: ' . $e->getMessage());
        $errors['form'] = 'Chưa lưu được phản hồi, vui lòng thử lại sau ít phút.';
    }
}

if ($errors === []) {
    $showTicket = $rsvp['attending'];

    if ($wantsJson) {
        $canDownload = $showTicket && canDownloadTicket($employee);
        $overlays = $showTicket ? trim(renderPartial('ticket-overlays.php', ['employee' => $employee])) : '';

        respondJson(200, [
            'ok'           => true,
            'attending'    => $showTicket,
            'rsvpHtml'     => renderPartial('rsvp-card.php', [
                'event' => $event, 'employee' => $employee, 'rsvp' => $rsvp, 'rsvpErrors' => [], 'rsvpDraft' => null,
            ]),
            'ticketHtml'   => renderPartial('ticket-slot.php', [
                'event' => $event, 'employee' => $employee, 'showTicket' => $showTicket,
            ]),
            'overlaysHtml' => $overlays !== '' ? $overlays : null,
            'ticketScript' => $canDownload ? 'js/ticket.js?v=' . filemtime(BASE_PATH . '/public/js/ticket.js') : null,
        ]);
    }

    // PRG: tải lại trang không gửi trùng phản hồi
    header('Location: ./?id=' . rawurlencode((string) employeeUuid($employee)) . ($showTicket ? '#ve' : '#xac-nhan'), true, 303);
    exit;
}

// Lỗi: giữ phản hồi hiệu lực hiện có để form hiện đúng trạng thái
$current = latestRsvp($employee);

if ($wantsJson) {
    respondJson(422, [
        'ok'       => false,
        'errors'   => $errors,
        'rsvpHtml' => renderPartial('rsvp-card.php', [
            'event' => $event, 'employee' => $employee, 'rsvp' => $current, 'rsvpErrors' => $errors, 'rsvpDraft' => is_string($attending) ? $attending : null,
        ]),
    ]);
}

http_response_code(422);

$recap = require BASE_PATH . '/config/recap.php';
$rsvp = $current;
$showTicket = ($rsvp['attending'] ?? false) === true;
$rsvpErrors = $errors;
$rsvpDraft = is_string($attending) ? $attending : null;
$opened = true;

require BASE_PATH . '/views/invite.php';
