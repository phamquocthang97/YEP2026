<?php
/**
 * Dữ liệu cho section Chương trình gồm lịch trình và dress code (khoá 'program' trong config/event.php). Chỉ đặt biến, không in ra.
 * Lịch trình trống => $hasSchedule = false; mô tả và bảng màu dress code trống => $hasDress = false; cả hai trống => $hasProgram = false.
 *
 * @var array $event
 */

$program = $event['program'] ?? [];
$schedule = array_values(array_filter(
    $program['schedule'] ?? [],
    fn ($item) => trim((string) ($item['title'] ?? '')) !== ''
));
$dressCode = trim((string) ($event['dress_code'] ?? ''));
$dressCodeNote = trim((string) ($program['dress_code_note'] ?? ''));

// Mã màu được đưa thẳng vào thuộc tính style => chỉ nhận #rgb / #rrggbb, mã sai bị bỏ qua
$dressColors = array_values(array_filter(
    array_map(fn ($color) => strtolower(trim((string) $color)), $program['dress_code_colors'] ?? []),
    fn ($color) => preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/', $color) === 1
));
$hasDress = $dressCode !== '' || $dressColors !== [];
$hasSchedule = $schedule !== [];
$hasProgram = $hasDress || $hasSchedule;

$programImage = trim((string) ($program['image'] ?? ''));
$programTitle = trim((string) ($program['title'] ?? ''));
$dateLabel = trim((string) ($program['date_label'] ?? ''));
$dressCodeLabel = trim((string) ($program['dress_code_label'] ?? ''));
$dateText = (new DateTimeImmutable($event['date']))->format('d.m.Y');
