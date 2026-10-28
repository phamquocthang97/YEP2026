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
