<?php

require __DIR__ . '/vendor/autoload.php';

const BASE_PATH = __DIR__;
const STORAGE_PATH = __DIR__ . '/storage';

/**
 * Nạp file .env vào getenv(), không ghi đè biến môi trường đã có sẵn.
 */
function loadEnv(string $file): void
{
    if (!is_file($file)) {
        return;
    }

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");

        if (getenv($key) === false) {
            putenv("{$key}={$value}");
        }
    }
}

/**
 * Mảng trả về từ config/{name}.php.
 */
function loadConfig(string $name): array
{
    return require BASE_PATH . "/config/{$name}.php";
}

/**
 * Giá trị chuỗi đã trim của một field, thiếu field => ''.
 */
function stringField(array $data, string $key): string
{
    return trim((string) ($data[$key] ?? ''));
}

function guestList(): \App\Rsvp\GuestList
{
    return new \App\Rsvp\GuestList(STORAGE_PATH . '/data.json');
}

/**
 * Khách trong data.json theo email (dùng cho trang gửi thư mời): [email => khách kèm uuid].
 */
function loadEmployees(): array
{
    $employees = [];

    foreach (guestList()->all() as $guest) {
        $employees[$guest['email']] ??= $guest;
    }

    return $employees;
}

function findEmployee(string $email): ?array
{
    return guestList()->findByEmail(normalizeEmail($email));
}

/**
 * Chuẩn hoá email để so sánh/lưu: bỏ khoảng trắng đầu cuối, chữ thường.
 */
function normalizeEmail(mixed $value): string
{
    return is_string($value) ? strtolower(trim($value)) : '';
}

/**
 * Email người dùng nhập, đã chuẩn hoá; sai kiểu hoặc sai định dạng => null.
 */
function emailInput(mixed $value): ?string
{
    $email = normalizeEmail($value);

    return strlen($email) <= 254 && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
}

function isValidUuid(string $value): bool
{
    $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    return preg_match($pattern, $value) === 1;
}

/**
 * uuid hợp lệ của nhân viên, null khi chưa điền hoặc sai định dạng.
 */
function employeeUuid(array $employee): ?string
{
    $uuid = stringField($employee, 'uuid');

    return isValidUuid($uuid) ? strtolower($uuid) : null;
}

/**
 * Khách của link công khai (?id=) kèm RSVP: ['uuid', 'guest', 'rsvp' => record|null].
 * Sai kiểu, sai định dạng hoặc không có ở data.json lẫn rsvp.json => null.
 * Không đọc được dữ liệu => throw (caller báo lỗi tạm thời, không báo link sai).
 */
function findInvitation(mixed $id): ?array
{
    if (!is_string($id) || !isValidUuid(trim($id))) {
        return null;
    }

    return rsvpLog()->findByUuid(trim($id));
}

/**
 * Giá trị bị trùng của một field: ['giá trị' => [email, ...]], bỏ qua giá trị rỗng.
 */
function findDuplicateValues(array $employees, string $field): array
{
    $groups = [];

    foreach ($employees as $email => $employee) {
        $value = strtolower(stringField($employee, $field));

        if ($value !== '') {
            $groups[$value][] = $email;
        }
    }

    return array_filter($groups, fn (array $emails) => count($emails) > 1);
}

/**
 * ADMIN_TOKEN chưa cấu hình => luôn từ chối.
 */
function isAdminToken(mixed $token): bool
{
    $expected = getenv('ADMIN_TOKEN') ?: '';

    return $expected !== '' && is_string($token) && hash_equals($expected, $token);
}

function appUrl(): string
{
    return rtrim(getenv('APP_URL') ?: '', '/');
}

/**
 * Query string của link mời cá nhân: ?id={uuid}.
 */
function inviteQuery(string $uuid): string
{
    return '?id=' . rawurlencode($uuid);
}

/**
 * Link thiệp mời cá nhân gửi trong email.
 */
function inviteUrl(string $uuid): string
{
    return appUrl() . '/' . inviteQuery($uuid);
}

/**
 * Đường dẫn file trong public/ kèm ?v= theo thời điểm sửa file để trình duyệt không dùng cache cũ.
 */
function asset(string $path): string
{
    return $path . '?v=' . filemtime(BASE_PATH . '/public/' . $path);
}

function rsvpLog(): \App\Rsvp\RsvpLog
{
    return new \App\Rsvp\RsvpLog(STORAGE_PATH . '/rsvp.json', guestList());
}

function sentLog(): \App\Mail\SentLog
{
    return new \App\Mail\SentLog(STORAGE_PATH . '/sent.json');
}

function isAttending(?array $rsvp): bool
{
    return ($rsvp['attendance'] ?? null) === \App\Rsvp\RsvpLog::ATTENDING;
}

/**
 * Vé chỉ tải được khi đã có mã Lucky Draw và số bàn.
 */
function canDownloadTicket(array $employee): bool
{
    return stringField($employee, 'luckyDrawCode') !== ''
        && stringField($employee, 'tableNumber') !== '';
}

/**
 * Dựng một partial trong views/partials/ thành chuỗi (dùng cho phản hồi JSON và các chỗ gọi lại).
 */
function renderPartial(string $file, array $vars = []): string
{
    extract($vars, EXTR_SKIP);
    ob_start();

    try {
        require BASE_PATH . '/views/partials/' . $file;
    } finally {
        $output = ob_get_clean();
    }

    return $output;
}

/**
 * Phần vé trong phản hồi JSON (RSVP, tra cứu vé): slot #ve, toast và script tải vé.
 * Không tham gia => slot chỉ còn seam, không có mã Lucky Draw, số bàn hay nút tải vé.
 */
function ticketPayload(array $event, ?array $guest, bool $showTicket): array
{
    $showTicket = $showTicket && $guest !== null;
    $overlays = $showTicket
        ? trim(renderPartial('ticket-overlays.php', ['employee' => $guest]))
        : '';

    return [
        'attending'    => $showTicket,
        'ticketHtml'   => renderPartial('ticket-slot.php', [
            'event' => $event, 'employee' => $guest, 'showTicket' => $showTicket,
        ]),
        'overlaysHtml' => $overlays !== '' ? $overlays : null,
        'ticketScript' => $showTicket && canDownloadTicket($guest) ? asset('js/ticket.js') : null,
    ];
}

/**
 * Độ dài tối đa của họ tên khách tự đăng ký (form và kiểm tra phía server).
 */
const NAME_MAX_LENGTH = 100;

/**
 * Thông tin sự kiện từ config/event.php, kèm các phần ngày tháng tính sẵn (thứ tính từ date).
 */
function loadEvent(): array
{
    $event = loadConfig('event');
    $date = new DateTimeImmutable($event['date']);
    $weekdays = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];

    return $event + [
        'title'     => "{$event['name']} {$date->format('Y')}",
        'weekday'   => $weekdays[(int) $date->format('w')],
        'day'       => $date->format('d'),
        'month'     => $date->format('n'),
        'year'      => $date->format('Y'),
        'date_text' => $date->format('d/m/Y'),
    ];
}

/**
 * Email nhận khi chạy chế độ test (MAIL_TEST_TO), rỗng nếu đang gửi thật.
 */
function mailTestRecipient(): string
{
    return getenv('MAIL_TEST_TO') ?: '';
}

function isMailTestMode(): bool
{
    return mailTestRecipient() !== '';
}

/**
 * Ghi JSON ra response; header đã gửi (lỗi giữa chừng) thì chỉ ghi nội dung.
 */
function sendJson(int $status, array $payload): void
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function respondJson(int $status, array $payload): never
{
    sendJson($status, $payload);
    exit;
}

/**
 * Nội dung hiển thị trong config/messages.php theo khoá 'nhóm.khoá', thay {placeholder} bằng $replace.
 * Thiếu khoá => trả lại chính khoá (ghi log) để dễ phát hiện khi sửa config.
 */
function message(string $key, array $replace = []): string
{
    static $messages = null;
    $messages ??= loadConfig('messages');
    $value = $messages;

    foreach (explode('.', $key) as $part) {
        $value = is_array($value) ? ($value[$part] ?? null) : null;
    }

    if (!is_string($value)) {
        error_log("message: thiếu khoá {$key} trong config/messages.php");

        return $key;
    }

    $pairs = [];

    foreach ($replace as $name => $replacement) {
        $pairs['{' . $name . '}'] = (string) $replacement;
    }

    return strtr($value, $pairs);
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Đường dẫn nền là video (mp4/webm) => render <video>, ngược lại dùng background-image.
 */
function isVideoAsset(?string $path): bool
{
    $ext = strtolower(pathinfo((string) parse_url((string) $path, PHP_URL_PATH), PATHINFO_EXTENSION));

    return in_array($ext, ['mp4', 'webm'], true);
}

loadEnv(BASE_PATH . '/.env');

date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Asia/Ho_Chi_Minh');
