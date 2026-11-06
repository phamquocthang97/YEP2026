<?php

/**
 * Tải danh sách khách kèm xác nhận tham dự dạng CSV, cùng quyền xem với admin.php (?token=).
 * Gồm khách data.json và khách tự đăng ký có email ngoài data.json (RsvpLog::entries()).
 */

require dirname(__DIR__) . '/bootstrap.php';

use App\Rsvp\RsvpLog;

header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex, nofollow');

if (!isAdminToken($_GET['token'] ?? null)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403 Forbidden');
}

/**
 * Ô bắt đầu bằng = + - @ (hoặc tab, CR) bị Excel hiểu là công thức => thêm ' phía trước.
 * Tên khách tự đăng ký do người ngoài nhập nên không được tin.
 */
function csvCell(string $value): string
{
    return $value !== '' && str_contains("=+-@\t\r", $value[0]) ? "'{$value}" : $value;
}

try {
    $entries = rsvpLog()->entries();
    $listed = guestList()->all();
} catch (RuntimeException $e) {
    error_log('admin-export.php: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Không đọc được dữ liệu khách: ' . $e->getMessage());
}

$attendanceLabels = [
    RsvpLog::ATTENDING => 'Tham gia',
    RsvpLog::DECLINED  => 'Không tham gia',
];

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="yep2026-khach-moi-' . date('Ymd-Hi') . '.csv"');
header('Cache-Control: no-store');

$out = fopen('php://output', 'w');
// BOM để Excel nhận đúng UTF-8 (tên tiếng Việt)
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, [
    'uuid',
    'Nguồn',
    'Họ tên',
    'Email',
    'Mã Lucky Draw',
    'Số bàn',
    'Xác nhận',
    'SubmittedAt',
    'UpdatedAt',
], escape: '');

foreach ($entries as $uuid => $entry) {
    $guest = $entry['guest'];
    $rsvp = $entry['rsvp'];

    fputcsv($out, array_map('csvCell', [
        $uuid,
        isset($listed[$uuid]) ? 'Danh sách' : 'Tự đăng ký',
        stringField($guest, 'name'),
        $guest['email'],
        stringField($guest, 'luckyDrawCode'),
        stringField($guest, 'tableNumber'),
        $rsvp === null ? 'Chưa phản hồi' : $attendanceLabels[$rsvp['attendance']],
        formatTimestamp($rsvp['submittedAt'] ?? null, 'Y-m-d H:i:s'),
        formatTimestamp($rsvp['updatedAt'] ?? null, 'Y-m-d H:i:s'),
    ]), escape: '');
}

fclose($out);
