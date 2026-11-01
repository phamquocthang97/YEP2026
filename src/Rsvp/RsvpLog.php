<?php

namespace App\Rsvp;

use RuntimeException;

/**
 * Lưu phản hồi xác nhận tham dự vào file JSON: danh sách [{ name, attending, email, uuid, submitted_at }].
 * Mỗi lần gửi thêm một dòng mới, không ghi đè phản hồi trước đó.
 */
class RsvpLog
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
            throw new RuntimeException("Không đọc được danh sách phản hồi: {$this->file}");
        }

        if (trim($content) === '') {
            return [];
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new RuntimeException("Danh sách phản hồi bị hỏng ({$this->file}): " . json_last_error_msg());
        }

        return $data;
    }

    public function add(array $entry): void
    {
        $dir = dirname($this->file);

        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException("Không tạo được thư mục lưu phản hồi: {$dir}");
        }

        // Lock riêng để các request đọc - sửa - ghi lần lượt, không ghi đè lẫn nhau
        $lock = fopen($this->file . '.lock', 'c');

        if ($lock === false || !flock($lock, LOCK_EX)) {
            throw new RuntimeException("Không khoá được danh sách phản hồi: {$this->file}");
        }

        try {
            $data = $this->all();
            $data[] = $entry + ['submitted_at' => date('Y-m-d H:i:s')];
            $this->writeAtomic($data);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
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

            throw new RuntimeException("Không ghi được danh sách phản hồi: {$this->file}");
        }
    }
}
