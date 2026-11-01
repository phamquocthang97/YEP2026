<?php
/**
 * Nền mờ dùng chung cho mọi trang trên PC (config/event.php => desktop_background: ảnh hoặc video .mp4/.webm). Điện thoại không hiện.
 * Video chỉ được nạp và phát trên PC (motion.js), điện thoại không tải.
 *
 * @var array $event
 */

$backdrop = trim((string) ($event['desktop_background'] ?? ''));
$isVideo = preg_match('/\.(mp4|webm)$/i', $backdrop) === 1;
?>
<?php if ($isVideo): ?>
    <div class="backdrop" aria-hidden="true">
        <video class="backdrop-video" data-src="<?= e($backdrop) ?>" muted loop playsinline preload="none"></video>
    </div>
<?php else: ?>
    <div class="backdrop" aria-hidden="true"<?= $backdrop !== '' ? ' style="background-image: url(\'' . e($backdrop) . '\')"' : '' ?>></div>
<?php endif; ?>
