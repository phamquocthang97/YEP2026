<?php
/**
 * Link không hợp lệ: id sai định dạng, không tìm thấy hoặc dữ liệu trùng uuid.
 *
 * @var array $event
 */

$pageTitle = 'Liên kết không hợp lệ';
?>
<!doctype html>
<html lang="vi">
<head>
    <?php require __DIR__ . '/partials/head.php'; ?>
</head>
<body>
<div class="scene scene-ticket">
    <div class="paper" aria-hidden="true"></div>
    <div class="flower flower-tl" aria-hidden="true"><img src="images/hoatop.png" alt="" width="299" height="335"></div>
    <div class="flower flower-br" aria-hidden="true"><img src="images/hoabottom.png" alt="" width="300" height="337"></div>

    <picture class="brand">
        <source media="(min-width: 641px)" srcset="images/thk-logo.png" width="657" height="120">
        <img src="images/thk-icon.png" alt="THK Holdings Vietnam" width="243" height="320">
    </picture>

    <main class="notice rise-in">
        <div class="notice-icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6.5l8.5 6 8.5-6"/><path class="notice-icon-x" d="M16.5 15.5l3 3M19.5 15.5l-3 3"/></svg>
        </div>
        <div class="notice-script"><?= e($event['name']) ?></div>
        <div class="notice-rule" aria-hidden="true"></div>
        <h1 class="notice-title heading-primary">Liên kết không hợp lệ</h1>
        <p class="notice-text">Rất tiếc, chúng tôi không tìm thấy vé ứng với đường link này. Vui lòng mở lại link trong email mời của bạn.</p>
        <a class="btn btn-primary notice-btn" href="./">Về trang thiệp</a>
    </main>
</div>
</body>
</html>
