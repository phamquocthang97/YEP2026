<?php

namespace App\Rsvp;

use App\Storage\JsonFile;
use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;

/**
 * Xác nhận tham dự (storage/rsvp.json), mỗi uuid đúng một record:
 * - Khách có trong data.json: { guestType: "0", attendance, submittedAt, updatedAt? }
 * - Khách tự đăng ký:        { guestType: "1", name, email, attendance, submittedAt, updatedAt? }
 * Khách không tự ghi đè được xác nhận đã gửi; chỉ admin đổi trạng thái (kèm updatedAt)
 * hoặc xoá record (public/admin-rsvp.php).
 *
 * Kết quả tra cứu trả về dạng ['guest' => thông tin khách/vé, 'rsvp' => record|null].
 */
class RsvpLog
{
    public const ATTENDING = 'attending';
    public const DECLINED = 'declined';

    public const GUEST_LISTED = '0';
    public const GUEST_SELF_REGISTERED = '1';

    private const TIMEZONE = 'Asia/Ho_Chi_Minh';

    private JsonFile $file;

    public function __construct(
        string $path,
        private GuestList $guests
    ) {
        $this->file = new JsonFile($path, 'danh sách RSVP');
    }

    /**
     * Link mời ?id=: khách trong data.json, hoặc khách tự đăng ký đã có record.
     * Không thuộc nguồn nào => null (link không hợp lệ).
     */
    public function findByUuid(string $uuid): ?array
    {
        $uuid = strtolower(trim($uuid));
        $guest = $this->guests->findByUuid($uuid);

        if ($guest !== null) {
            return $this->resolveEmail($this->records(), $guest['email']);
        }

        $records = $this->records();
        $record = $records[$uuid] ?? null;

        if ($record === null || $record['guestType'] !== self::GUEST_SELF_REGISTERED) {
            return null;
        }

        // Qua email để khách tự đăng ký mà sau đó được thêm vào data.json vẫn thấy vé chính thức
        return $this->resolveEmail($records, normalizeEmail((string) $record['email']));
    }

    /**
     * Tra cứu vé theo email: null khi email không thuộc khách nào.
     *
     * @param string $email đã chuẩn hoá bằng normalizeEmail()
     */
    public function findByEmail(string $email): ?array
    {
        $found = $this->resolveEmail($this->records(), $email);

        return $found['uuid'] !== null ? $found : null;
    }

    /**
     * Ghi xác nhận lần đầu. Kiểm tra email, xác định uuid/guestType và thêm record
     * nằm trong cùng một exclusive lock, nên request đồng thời không tạo trùng.
     * Email đã có RSVP => trả record cũ, không ghi.
     *
     * @param string $email đã chuẩn hoá bằng normalizeEmail()
     * @param string $name tên khách tự nhập, chỉ dùng cho khách tự đăng ký
     * @return array{uuid: string, guest: array, rsvp: array, created: bool}
     */
    public function submit(string $email, string $name, string $attendance): array
    {
        $this->assertAttendance($attendance);

        return $this->file->update(function (array &$records) use ($email, $name, $attendance): array {
            $this->assertValid($records);
            $found = $this->resolveEmail($records, $email);

            if ($found['rsvp'] !== null) {
                return $found + ['created' => false];
            }

            $submittedAt = $this->now();

            if ($found['uuid'] !== null) {
                // Khách có sẵn: chỉ lưu xác nhận, thông tin khách vẫn lấy từ data.json
                $uuid = $found['uuid'];
                $record = [
                    'guestType'   => self::GUEST_LISTED,
                    'attendance'  => $attendance,
                    'submittedAt' => $submittedAt,
                ];
            } else {
                $uuid = $this->newUuid($records);
                $record = [
                    'guestType'   => self::GUEST_SELF_REGISTERED,
                    'name'        => $name,
                    'email'       => $email,
                    'attendance'  => $attendance,
                    'submittedAt' => $submittedAt,
                ];
            }

            $records[$uuid] = $record;

            return $this->result($uuid, $record, $found['guest']) + ['created' => true];
        });
    }

    /**
     * Danh sách cho trang admin, theo uuid hiển thị: khách data.json (thứ tự trong file)
     * rồi tới khách tự đăng ký có email ngoài data.json.
     * Cùng thứ tự ưu tiên với resolveEmail(), nhưng mỗi uuid data.json tự tìm record của mình
     * nên data.json trùng email không throw (admin.php đã cảnh báo riêng).
     *
     * @return array<string, array{uuid: string, guest: array, rsvp: ?array, recordUuid: ?string}>
     *         recordUuid: key của record trong rsvp.json, khác uuid khi khách tự đăng ký
     *         rồi mới được thêm vào data.json; null khi chưa xác nhận
     */
    public function entries(): array
    {
        $records = $this->records();
        $entries = [];
        $listedEmails = [];

        foreach ($this->guests->all() as $uuid => $guest) {
            $listedEmails[$guest['email']] = true;
            $recordUuid = isset($records[$uuid])
                ? $uuid
                : $this->findSelfRegistered($records, $guest['email']);

            $entries[$uuid] = [
                'uuid'       => $uuid,
                'guest'      => $guest,
                'rsvp'       => $recordUuid !== null ? $records[$recordUuid] : null,
                'recordUuid' => $recordUuid,
            ];
        }

        foreach ($records as $uuid => $record) {
            $isUnlisted = $record['guestType'] === self::GUEST_SELF_REGISTERED
                && !isset($listedEmails[normalizeEmail((string) $record['email'])]);

            if ($isUnlisted && !isset($entries[$uuid])) {
                $entries[$uuid] = $this->result($uuid, $record, null) + ['recordUuid' => $uuid];
            }
        }

        return $entries;
    }

    /**
     * Admin đổi xác nhận của một record, kèm updatedAt. Trạng thái không đổi => không ghi.
     *
     * @return string|null trạng thái trước khi đổi, null khi record không còn
     */
    public function changeAttendance(string $recordUuid, string $attendance): ?string
    {
        $this->assertAttendance($attendance);

        return $this->file->update(function (array &$records) use ($recordUuid, $attendance): ?string {
            $this->assertValid($records);
            $before = $records[$recordUuid]['attendance'] ?? null;

            if ($before !== null && $before !== $attendance) {
                $records[$recordUuid]['attendance'] = $attendance;
                $records[$recordUuid]['updatedAt'] = $this->now();
            }

            return $before;
        });
    }

    /**
     * Admin xoá record để khách xác nhận lại. Khách tự đăng ký có email ngoài data.json
     * thì link ?id= cũ không còn hợp lệ, phải đăng ký lại.
     *
     * @return array|null record đã xoá, null khi record không còn
     */
    public function delete(string $recordUuid): ?array
    {
        return $this->file->update(function (array &$records) use ($recordUuid): ?array {
            $this->assertValid($records);
            $record = $records[$recordUuid] ?? null;
            unset($records[$recordUuid]);

            return $record;
        });
    }

    private function assertAttendance(string $attendance): void
    {
        if (!in_array($attendance, [self::ATTENDING, self::DECLINED], true)) {
            throw new RuntimeException("attendance không hợp lệ: {$attendance}");
        }
    }

    private function now(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone(self::TIMEZONE)))->format(DATE_ATOM);
    }

    private function records(): array
    {
        $records = $this->file->read();
        $this->assertValid($records);

        return $records;
    }

    /**
     * rsvp.json sai cấu trúc => throw để không đọc nhầm và không ghi đè lên dữ liệu cũ.
     */
    private function assertValid(array $records): void
    {
        if ($records !== [] && array_is_list($records)) {
            throw new RuntimeException('rsvp.json sai định dạng: cần object theo uuid');
        }

        foreach ($records as $uuid => $record) {
            $valid = is_string($uuid)
                && isValidUuid($uuid)
                && is_array($record)
                && in_array($record['guestType'] ?? null, [self::GUEST_LISTED, self::GUEST_SELF_REGISTERED], true)
                && in_array($record['attendance'] ?? null, [self::ATTENDING, self::DECLINED], true)
                && (
                    $record['guestType'] === self::GUEST_LISTED
                    || normalizeEmail((string) ($record['email'] ?? '')) !== ''
                );

            if (!$valid) {
                throw new RuntimeException("rsvp.json có record không hợp lệ: {$uuid}");
            }
        }
    }

    /**
     * Xác định khách của một email, theo thứ tự:
     * 1. Khách data.json đã có RSVP
     * 2. Khách tự đăng ký đã có RSVP cùng email. Email đó cũng có trong data.json
     *    (khách được thêm vào danh sách sau khi tự đăng ký) => uuid, tên và thông tin vé
     *    lấy từ data.json, xác nhận lấy từ record tự đăng ký
     * 3. Khách data.json chưa có RSVP
     * 4. Không thuộc ai (uuid null)
     *
     * @return array{uuid: ?string, guest: ?array, rsvp: ?array}
     */
    private function resolveEmail(array $records, string $email): array
    {
        $guest = $this->guests->findByEmail($email);

        if ($guest !== null && isset($records[$guest['uuid']])) {
            return $this->result($guest['uuid'], $records[$guest['uuid']], $guest);
        }

        $selfUuid = $this->findSelfRegistered($records, $email);

        if ($selfUuid !== null) {
            return $this->result($guest['uuid'] ?? $selfUuid, $records[$selfUuid], $guest);
        }

        if ($guest !== null) {
            return ['uuid' => $guest['uuid'], 'guest' => $guest, 'rsvp' => null];
        }

        return ['uuid' => null, 'guest' => null, 'rsvp' => null];
    }

    /**
     * uuid (key trong rsvp.json) của record tự đăng ký đầu tiên có email này, không có => null.
     */
    private function findSelfRegistered(array $records, string $email): ?string
    {
        foreach ($records as $uuid => $record) {
            if (
                $record['guestType'] === self::GUEST_SELF_REGISTERED
                && normalizeEmail((string) $record['email']) === $email
            ) {
                return $uuid;
            }
        }

        return null;
    }

    /**
     * Thông tin hiển thị: khách data.json lấy nguyên bản ghi khách (gồm thông tin vé),
     * khách tự đăng ký chỉ có tên và email đã lưu.
     */
    private function result(string $uuid, array $record, ?array $guest): array
    {
        $guest ??= [
            'uuid'  => $uuid,
            'name'  => (string) ($record['name'] ?? ''),
            'email' => normalizeEmail((string) ($record['email'] ?? '')),
        ];

        return ['uuid' => $uuid, 'guest' => $guest, 'rsvp' => $record];
    }

    /**
     * UUID v4 chưa dùng ở data.json lẫn rsvp.json.
     */
    private function newUuid(array $records): string
    {
        $taken = $records + $this->guests->all();

        do {
            $bytes = random_bytes(16);
            $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
            $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
            $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
        } while (isset($taken[$uuid]));

        return $uuid;
    }
}
