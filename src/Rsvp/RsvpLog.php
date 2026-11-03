<?php

namespace App\Rsvp;

use App\Storage\JsonFile;

/**
 * Lưu phản hồi xác nhận tham dự vào file JSON, dạng danh sách:
 * [{ name, attending, email, uuid, submitted_at }].
 * Mỗi lần gửi thêm một dòng mới, không ghi đè phản hồi trước đó.
 */
class RsvpLog
{
    private JsonFile $file;

    public function __construct(string $path)
    {
        $this->file = new JsonFile($path, 'danh sách phản hồi');
    }

    public function all(): array
    {
        return $this->file->read();
    }

    /**
     * Phản hồi hiệu lực của một uuid: dòng mới nhất khớp uuid
     * (không phân biệt hoa thường), không có thì null.
     */
    public function latestFor(string $uuid): ?array
    {
        $uuid = strtolower($uuid);

        foreach (array_reverse($this->all()) as $entry) {
            if (strtolower((string) ($entry['uuid'] ?? '')) === $uuid) {
                return $entry;
            }
        }

        return null;
    }

    public function add(array $entry): void
    {
        $this->file->update(function (array $entries) use ($entry): array {
            $entries[] = $entry + ['submitted_at' => date('Y-m-d H:i:s')];

            return $entries;
        });
    }
}
