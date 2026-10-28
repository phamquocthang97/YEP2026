<?php

namespace App\Mail;

use RuntimeException;

/**
 * Lưu các email đã gửi thành công vào file JSON: { "email": "thời gian gửi" }.
 * Không đọc/ghi được nhật ký thì throw để dừng gửi
 */
class SentLog
{
    public function __construct(private string $file)
    {
    }

    public function all(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        $content = file_get_contents($this->file);

        if ($content === false) {
            throw new RuntimeException("Không đọc được nhật ký gửi: {$this->file}");
        }

        return $this->decode($content);
    }

    /**
     * Kiểm tra có ghi được nhật ký không. Gọi trước khi gửi email để không gửi rồi mới phát hiện lỗi.
     */
    public function ensureWritable(): void
    {
        $dir = dirname($this->file);

        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException("Không tạo được thư mục nhật ký: {$dir}");
        }

        if (!is_writable($dir) || (is_file($this->file) && !is_writable($this->file))) {
            throw new RuntimeException("Không có quyền ghi nhật ký gửi: {$this->file}");
        }
    }

    public function markSent(string $email): string
    {
        $this->ensureWritable();

        // Lock riêng để các request đọc - sửa - ghi lần lượt, không ghi đè lẫn nhau
        $lock = fopen($this->file . '.lock', 'c');

        if ($lock === false || !flock($lock, LOCK_EX)) {
            throw new RuntimeException("Không khoá được nhật ký gửi: {$this->file}");
        }

        try {
            $data = $this->all();
            $sentAt = date('Y-m-d H:i:s');
            $data[$email] = $sentAt;
            $this->writeAtomic($data);

            return $sentAt;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function decode(string $content): array
    {
        if (trim($content) === '') {
            return [];
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new RuntimeException(
                "Nhật ký gửi bị hỏng ({$this->file}): " . json_last_error_msg()
            );
        }

        return $data;
    }

    /**
     * Ghi ra file tạm rồi rename => file thật luôn là bản cũ hoặc bản mới đầy đủ, không bị ghi dở.
     */
    private function writeAtomic(array $data): void
    {
        $tmp = $this->file . '.tmp';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        if (@file_put_contents($tmp, $json) !== strlen($json) || !@rename($tmp, $this->file)) {
            @unlink($tmp);

            throw new RuntimeException("Không ghi được nhật ký gửi: {$this->file}");
        }
    }
}
