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
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value) === 1;
}

/**
 * uuid hợp lệ của nhân viên, null khi chưa điền hoặc sai định dạng.
 */
function employeeUuid(array $employee): ?string
{
    $uuid = trim((string) ($employee['uuid'] ?? ''));

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
 * Người nhận của link công khai (?id=). Sai kiểu, sai định dạng, không tìm thấy hoặc trùng uuid => null.
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
        $value = strtolower(trim((string) ($employee[$field] ?? '')));

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
 * Link thiệp mời cá nhân gửi trong email.
 */
function inviteUrl(string $uuid): string
{
    return appUrl() . '/?id=' . rawurlencode($uuid);
}

/**
 * Thông tin sự kiện từ config/event.php, kèm các phần ngày tháng tính sẵn (thứ tính từ date).
 */
function loadEvent(): array
{
    $event = require BASE_PATH . '/config/event.php';
    $date = new DateTimeImmutable($event['date']);
    $weekdays = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];

    return $event + [
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

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

loadEnv(BASE_PATH . '/.env');

date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Asia/Ho_Chi_Minh');
