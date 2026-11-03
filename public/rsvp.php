<?php

require dirname(__DIR__) . '/bootstrap.php';

$event = loadEvent();
$employee = findInvitee($_GET['id'] ?? null);
$wantsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

// Xác nhận tham dự nằm trong trang chính: mở thẳng rsvp.php thì về đúng section
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $query = $employee !== null ? inviteQuery((string) employeeUuid($employee)) : '';
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
$attendingDraft = is_string($attending) ? $attending : null;
$errors = [];

$renderRsvpCard = fn (
    ?array $rsvp,
    array $rsvpErrors,
    ?string $rsvpDraft
): string => renderPartial('rsvp-card.php', [
    'event' => $event,
    'employee' => $employee,
    'rsvp' => $rsvp,
    'rsvpErrors' => $rsvpErrors,
    'rsvpDraft' => $rsvpDraft,
]);

if (!in_array($attending, ['yes', 'no'], true)) {
    $errors['attending'] = 'Vui lòng chọn bạn có tham gia được hay không.';
}

$rsvp = null;

if ($errors === []) {
    $rsvp = [
        'name'      => stringField($employee, 'name'),
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
        $overlays = $showTicket
            ? trim(renderPartial('ticket-overlays.php', ['employee' => $employee]))
            : '';

        respondJson(200, [
            'ok'           => true,
            'attending'    => $showTicket,
            'rsvpHtml'     => $renderRsvpCard($rsvp, [], null),
            'ticketHtml'   => renderPartial('ticket-slot.php', [
                'event' => $event, 'employee' => $employee, 'showTicket' => $showTicket,
            ]),
            'overlaysHtml' => $overlays !== '' ? $overlays : null,
            'ticketScript' => $canDownload ? asset('js/ticket.js') : null,
        ]);
    }

    // PRG: tải lại trang không gửi trùng phản hồi
    $query = inviteQuery((string) employeeUuid($employee));
    $anchor = $showTicket ? '#ve' : '#xac-nhan';

    header('Location: ./' . $query . $anchor, true, 303);
    exit;
}

// Lỗi: giữ phản hồi hiệu lực hiện có để form hiện đúng trạng thái
$latestRsvp = latestRsvp($employee);

if ($wantsJson) {
    respondJson(422, [
        'ok'       => false,
        'errors'   => $errors,
        'rsvpHtml' => $renderRsvpCard($latestRsvp, $errors, $attendingDraft),
    ]);
}

http_response_code(422);

$recap = loadConfig('recap');
$rsvp = $latestRsvp;
$showTicket = ($rsvp['attending'] ?? false) === true;
$rsvpErrors = $errors;
$rsvpDraft = $attendingDraft;
$opened = true;

require BASE_PATH . '/views/invite.php';
