<?php

/**
 * Admin đổi trạng thái hoặc xoá xác nhận tham dự của một khách (gọi từ js/admin-rsvp.js).
 * POST: token, uuid (uuid của dòng trên admin.php), action = attendance|delete,
 *       attendance (khi action = attendance).
 * Trả JSON {ok, html, error?}: html là phần RSVP mới của row theo cột
 * {status, time, actions}, null khi khách không còn trong danh sách (khách tự đăng ký vừa bị xoá).
 */

require dirname(__DIR__) . '/bootstrap.php';

use App\Rsvp\RsvpLog;

const ACTIONS = ['attendance', 'delete'];

header('X-Robots-Tag: noindex, nofollow');

ensurePostRequest();

if (!isAdminToken($_POST['token'] ?? null)) {
    respondJson(403, ['ok' => false, 'error' => 'Không có quyền sửa xác nhận tham dự']);
}

$uuid = is_string($_POST['uuid'] ?? null) ? strtolower(trim($_POST['uuid'])) : '';
$action = $_POST['action'] ?? null;
$attendance = $_POST['attendance'] ?? null;

if (
    !isValidUuid($uuid)
    || !in_array($action, ACTIONS, true)
    || ($action === 'attendance' && !in_array($attendance, [RsvpLog::ATTENDING, RsvpLog::DECLINED], true))
) {
    respondJson(422, ['ok' => false, 'error' => 'Yêu cầu không hợp lệ']);
}

/**
 * Phần RSVP hiện tại của row theo cột (views/partials/admin-rsvp.php),
 * null khi khách không còn trong danh sách.
 *
 * @return array{status: string, time: string, actions: string}|null
 */
function rsvpRowHtml(?array $entry): ?array
{
    if ($entry === null) {
        return null;
    }

    $html = [];

    foreach (['status', 'time', 'actions'] as $slot) {
        $html[$slot] = trim(renderPartial('admin-rsvp.php', ['entry' => $entry, 'slot' => $slot]));
    }

    return $html;
}

try {
    $log = rsvpLog();
    $entry = $log->entries()[$uuid] ?? null;

    if ($entry === null) {
        respondJson(404, ['ok' => false, 'error' => 'Không tìm thấy khách, hãy tải lại trang', 'html' => null]);
    }

    $recordUuid = $entry['recordUuid'];
    $before = $recordUuid === null ? null : match ($action) {
        'attendance' => $log->changeAttendance($recordUuid, $attendance),
        'delete'     => $log->delete($recordUuid)['attendance'] ?? null,
    };

    $entry = $log->entries()[$uuid] ?? null;

    if ($before === null) {
        // Khách chưa xác nhận hoặc record vừa bị xoá ở nơi khác: trả trạng thái hiện tại
        respondJson(409, [
            'ok'    => false,
            'error' => 'Khách chưa có xác nhận tham dự để sửa',
            'html'  => rsvpRowHtml($entry),
        ]);
    }

    $after = $action === 'delete' ? 'deleted' : $attendance;

    if ($before !== $after) {
        error_log("admin-rsvp: record {$recordUuid} (khách {$uuid}) {$before} -> {$after}");
    }
} catch (RuntimeException $e) {
    error_log('admin-rsvp.php: ' . $e->getMessage());
    respondJson(500, ['ok' => false, 'error' => 'Không lưu được xác nhận: ' . $e->getMessage()]);
}

respondJson(200, ['ok' => true, 'html' => rsvpRowHtml($entry)]);
