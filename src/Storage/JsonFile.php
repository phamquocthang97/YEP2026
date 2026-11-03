<?php

namespace App\Storage;

use RuntimeException;

/**
 * File JSON lưu trạng thái của ứng dụng.
 * Đọc/khoá/ghi lỗi thì throw kèm nhãn để biết file nào hỏng.
 */
class JsonFile
{
    /**
     * @param string $label tên file trong thông báo lỗi, ví dụ 'nhật ký gửi'
     */
    public function __construct(
        private string $path,
        private string $label
    ) {
    }

    /**
     * Chưa có file hoặc file rỗng => [].
     */
    public function read(): array
    {
        if (!is_file($this->path)) {
            return [];
        }

        $content = file_get_contents($this->path);

        if ($content === false) {
            throw new RuntimeException(
                "Không đọc được {$this->label}: {$this->path}"
            );
        }

        if (trim($content) === '') {
            return [];
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new RuntimeException(
                ucfirst($this->label)
                . " bị hỏng ({$this->path}): "
                . json_last_error_msg()
            );
        }

        return $data;
    }

    public function ensureDirectory(): void
    {
        $dir = dirname($this->path);

        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException(
                "Không tạo được thư mục {$this->label}: {$dir}"
            );
        }
    }

    public function ensureWritable(): void
    {
        $this->ensureDirectory();

        $dirWritable = is_writable(dirname($this->path));
        $fileWritable = !is_file($this->path) || is_writable($this->path);

        if (!$dirWritable || !$fileWritable) {
            throw new RuntimeException(
                "Không có quyền ghi {$this->label}: {$this->path}"
            );
        }
    }

    /**
     * Đọc - sửa - ghi trong lock riêng để các request chạy lần lượt,
     * không ghi đè lẫn nhau.
     *
     * @param callable(array): array $modify nhận dữ liệu hiện tại, trả về dữ liệu mới
     */
    public function update(callable $modify): void
    {
        $this->ensureDirectory();

        $lock = fopen($this->path . '.lock', 'c');

        if ($lock === false || !flock($lock, LOCK_EX)) {
            throw new RuntimeException(
                "Không khoá được {$this->label}: {$this->path}"
            );
        }

        try {
            $this->writeAtomic($modify($this->read()));
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Ghi ra file tạm rồi rename, nên file thật luôn là bản cũ hoặc
     * bản mới đầy đủ, không bị ghi dở.
     */
    private function writeAtomic(array $data): void
    {
        $tmp = $this->path . '.tmp';
        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );

        $written = @file_put_contents($tmp, $json) === strlen($json);

        if (!$written || !@rename($tmp, $this->path)) {
            @unlink($tmp);

            throw new RuntimeException(
                "Không ghi được {$this->label}: {$this->path}"
            );
        }
    }
}
