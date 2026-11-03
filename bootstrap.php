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

function loadEmployees(): array
{
    $data = json_decode(file_get_contents(STORAGE_PATH . '/data.json'), true);

    if (!is_array($data)) {
        throw new RuntimeException('data.json không hợp lệ: ' . json_last_error_msg());
    }

    return array_change_key_case($data, CASE_LOWER);
}

function findEmployee(string $email): ?array
{
    return loadEmployees()[strtolower($email)] ?? null;
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
 * Tìm nhân viên theo uuid trong link mời, kèm key email.
 * Trùng uuid => throw để không hiển thị vé của người khác.
 */
function findEmployeeByUuid(string $uuid): ?array
{
    if (!isValidUuid($uuid)) {
        return null;
    }

    $matches = [];

    foreach (loadEmployees() as $email => $employee) {
        if (employeeUuid($employee) === strtolower($uuid)) {
            $matches[] = $employee + ['email' => $email];
        }
    }

    if (count($matches) > 1) {
        throw new RuntimeException("uuid {$uuid} bị trùng ở " . count($matches) . ' nhân viên');
    }

    return $matches[0] ?? null;
}

/**
 * Người nhận của link công khai (?id=).
 * Sai kiểu, sai định dạng, không tìm thấy hoặc trùng uuid => null.
 */
function findInvitee(mixed $id): ?array
{
    if (!is_string($id)) {
        return null;
    }

    try {
        return findEmployeeByUuid(trim($id));
    } catch (RuntimeException $e) {
        error_log('findInvitee: ' . $e->getMessage());

        return null;
    }
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
    return new \App\Rsvp\RsvpLog(STORAGE_PATH . '/rsvp.json');
}

function sentLog(): \App\Mail\SentLog
{
    return new \App\Mail\SentLog(STORAGE_PATH . '/sent.json');
}

/**
 * Phản hồi mới nhất của nhân viên.
 * Chưa có, chưa có uuid hoặc không đọc được log => null (vé ẩn, hiện form).
 */
function latestRsvp(array $employee): ?array
{
    $uuid = employeeUuid($employee);

    if ($uuid === null) {
        return null;
    }

    try {
        return rsvpLog()->latestFor($uuid);
    } catch (RuntimeException $e) {
        error_log('latestRsvp: ' . $e->getMessage());

        return null;
    }
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

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

loadEnv(BASE_PATH . '/.env');

date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Asia/Ho_Chi_Minh');
