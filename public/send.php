<?php

require dirname(__DIR__) . '/bootstrap.php';

use App\Mail\Mailer;
use App\Mail\MailTemplate;
use App\Mail\SentLog;

/**
 * Trả JSON và kết thúc request.
 */
function respond(int $status, array $data): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Endpoint gửi được tới email bất kỳ => chỉ cho phép gọi từ localhost.
 */
function ensureLocalRequest(): void
{
    if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
        respond(403, ['ok' => false, 'error' => 'Chỉ cho phép truy cập từ localhost']);
    }
}

function ensurePostRequest(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respond(405, ['ok' => false, 'error' => 'Method không hợp lệ']);
    }
}

/**
 * Lấy email từ request, chuẩn hoá lowercase và kiểm tra hợp lệ.
 */
function readEmailInput(): string
{
    $email = strtolower(trim($_POST['email'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(422, ['ok' => false, 'error' => 'Email không hợp lệ']);
    }

    return $email;
}

/**
 * true khi người dùng đã xác nhận gửi lại email đã gửi trước đó.
 */
function isForceResend(): bool
{
    return ($_POST['force'] ?? '') === '1';
}

/**
 * Chặn gửi trùng, trừ khi người dùng đã xác nhận gửi lại.
 */
function ensureNotSentYet(SentLog $sentLog, string $email, bool $force): void
{
    $sentAt = $sentLog->all()[$email] ?? null;

    if ($sentAt !== null && !$force) {
        respond(409, ['ok' => false, 'error' => 'Email đã được gửi trước đó', 'sent_at' => $sentAt]);
    }
}

/**
 * Tạo subject + body từ template. Email ngoài data.json thì dùng chính email làm tên.
 *
 * @return array{subject: string, body: string}
 */
function buildMail(string $email): array
{
    $employee = findEmployee($email) ?? [];

    return MailTemplate::fromFile(BASE_PATH . '/config/template.php')->render([
        'name'     => $employee['name'] ?? $email,
        'code'     => $employee['code'] ?? '',
        'position' => $employee['position'] ?? '',
        'email'    => $email,
    ]);
}

/**
 * Gửi email qua SMTP. Chế độ test: chuyển về MAIL_TEST_TO và ghi email gốc vào subject.
 * Gửi lỗi thì trả 500 kèm lý do.
 */
function sendMail(string $email, array $mail): void
{
    $recipient = $email;
    $subject = $mail['subject'];

    if (isMailTestMode()) {
        $recipient = mailTestRecipient();
        $subject = "[TEST -> {$email}] {$subject}";
    }

    $mailer = new Mailer(require BASE_PATH . '/config/mail.php');

    if (!$mailer->send($recipient, $subject, $mail['body'])) {
        respond(500, ['ok' => false, 'error' => $mailer->getLastError() ?? 'Gửi email thất bại']);
    }
}

header('Content-Type: application/json; charset=utf-8');

ensureLocalRequest();
ensurePostRequest();

$email = readEmailInput();
$sentLog = new SentLog(STORAGE_PATH . '/sent.json');

ensureNotSentYet($sentLog, $email, isForceResend());
sendMail($email, buildMail($email));

// Chế độ test không đánh dấu đã gửi
if (isMailTestMode()) {
    respond(200, ['ok' => true, 'test' => true]);
}

respond(200, ['ok' => true, 'sent_at' => $sentLog->markSent($email)]);
