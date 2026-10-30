<?php
/**
 * Thiệp mời + vé tham dự trong cùng một trang (nút "Xem thông tin tham dự" chuyển sang vé). $employee = null khi mở link chung (không có id).
 *
 * @var array $event
 * @var array|null $employee
 */

$pageTitle = "Thiệp mời {$event['name']} {$event['year']}";
$uuid = $employee !== null ? employeeUuid($employee) : null;

// Đốm gold: [x, y, đường kính, delay, thời lượng], toạ độ theo khung thiết kế (mobile 375×812, desktop 1440×900)
$particleSets = [
    'mobile'  => [375, 812, [
        [54, 210, 5, -1, 11], [312, 250, 4, -4, 13], [92, 470, 6, -7, 11], [288, 520, 4, -2.5, 9],
        [40, 640, 3, -5, 12], [330, 420, 5, -8, 14], [170, 120, 3, -3, 10], [228, 610, 4, -6, 11],
    ]],
    'desktop' => [1440, 900, [
        [300, 300, 6, -1, 11], [1120, 240, 5, -4, 13], [380, 620, 7, -7, 11], [1060, 560, 5, -2.5, 9],
        [220, 470, 4, -5, 12], [1240, 420, 6, -8, 14], [560, 160, 4, -3, 10], [900, 760, 5, -6, 11],
        [960, 130, 4, -9, 12], [500, 780, 4, -1.5, 10],
    ]],
];
?>
<!doctype html>
<html lang="vi">
<head>
    <?php require __DIR__ . '/partials/head.php'; ?>
    <script>
        // Thiệp đã mở trong phiên => hiện thẳng trạng thái cuối. Chưa mở => phủ lớp giấy trước khi trang hiện ra.
        // motion.js không chạy được (lỗi/mạng chậm) => gỡ lớp giấy để nội dung vẫn đọc được.
        (function (root) {
            var opened = false;

            // try {
            //     opened = sessionStorage.getItem('yep26-opened') === '1';
            // } catch (e) {
            // }
			//
            // if (opened) {
            //     root.classList.add('is-skip');
            //     return;
            // }

            root.classList.add('has-cover', 'is-waiting');
            setTimeout(function () {
                if (!window.yepMotionReady) {
                    root.classList.remove('has-cover', 'is-waiting');
                }
            }, 4000);
        })(document.documentElement);
    </script>
</head>
<body>
<div class="tear-cover" data-texture="images/bg-portrait.jpg">
    <canvas class="tear-canvas" aria-hidden="true"></canvas>
    <div class="tear-hint">
        <div class="tear-hand-wrap" aria-hidden="true">
            <svg class="tear-hand" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11V5.5a1.5 1.5 0 0 1 3 0V10"/><path d="M12 9.5a1.5 1.5 0 0 1 3 0V11"/><path d="M15 10.5a1.5 1.5 0 0 1 3 0v3.5a6 6 0 0 1-6 6h-.6a5 5 0 0 1-3.9-1.9L5 14.7a1.5 1.5 0 0 1 2.3-1.9L9 14.5"/></svg>
        </div>
        <div class="tear-hint-text">Vuốt xuống để mở thiệp</div>
        <button class="tear-btn" type="button">Hoặc nhấn để mở</button>
    </div>
</div>

<div class="scene">
    <div class="paper" aria-hidden="true"></div>

    <?php foreach ($particleSets as $set => [$frameWidth, $frameHeight, $particles]): ?>
        <div class="particles particles-<?= $set ?> intro" aria-hidden="true">
            <?php foreach ($particles as [$x, $y, $size, $delay, $duration]): ?>
                <span class="particle" style="left: <?= round($x / $frameWidth * 100, 2) ?>%; top: <?= round($y / $frameHeight * 100, 2) ?>%; width: <?= $size ?>px; height: <?= $size ?>px; animation-delay: <?= $delay ?>s; animation-duration: <?= $duration ?>s"></span>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="flower flower-tl settle intro" aria-hidden="true">
        <div class="sway sway-a"><img src="images/hoatop.png" alt="" width="299" height="335"></div>
    </div>
    <div class="flower flower-tr settle intro" aria-hidden="true">
        <div class="sway sway-c"><img class="is-mirrored" src="images/hoatop.png" alt="" width="299" height="335"></div>
    </div>
    <div class="flower flower-bl settle intro" aria-hidden="true">
        <div class="sway sway-d"><img class="is-mirrored" src="images/hoabottom.png" alt="" width="300" height="337"></div>
    </div>
    <div class="flower flower-br settle intro" aria-hidden="true">
        <div class="sway sway-b"><img src="images/hoabottom.png" alt="" width="300" height="337"></div>
    </div>

    <picture class="brand intro a-logo">
        <source media="(min-width: 641px)" srcset="images/thk-logo.png" width="657" height="120">
        <img src="images/thk-icon.png" alt="THK Holdings Vietnam" width="243" height="320">
    </picture>

    <main class="invite" id="thiep">
        <?php if ($event['monogram'] !== ''): ?>
            <div class="monogram" aria-hidden="true"><?= e($event['monogram']) ?></div>
        <?php endif; ?>

        <h1 class="invite-title"><span class="sheen-text intro"><?= e($event['name']) ?></span></h1>

        <div class="invite-year tnum">
            <span class="invite-year-rule" aria-hidden="true"></span>
            <span class="invite-year-text"><?= e($event['year']) ?></span>
            <span class="invite-year-rule" aria-hidden="true"></span>
        </div>

        <?php if ($employee !== null): ?>
            <div class="invite-guest">
                <div class="eyebrow">Thân mời</div>
                <div class="invite-name"><?= e($employee['name'] ?? '') ?></div>
            </div>
        <?php endif; ?>

        <div class="invite-rule" aria-hidden="true"></div>

        <div class="invite-date tnum">
            <div class="invite-date-side"><?= e($event['weekday']) ?></div>
            <div class="invite-day"><?= e($event['day']) ?></div>
            <div class="invite-date-side">Tháng <?= e($event['month']) ?></div>
        </div>
        <div class="invite-time tnum"><?= e($event['year']) ?> <span class="dot" aria-hidden="true">·</span> <?= e($event['time']) ?></div>

        <div class="invite-venue">
            <div class="invite-venue-name"><?= e($event['venue_name']) ?></div>
            <?php if ($event['venue_address'] !== ''): ?>
                <div class="invite-venue-address">
                    <?php if ($event['map_url'] !== ''): ?>
                        <a href="<?= e($event['map_url']) ?>" target="_blank" rel="noopener"><?= e($event['venue_address']) ?></a>
                    <?php else: ?>
                        <?= e($event['venue_address']) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($event['dress_code'] !== ''): ?>
                <div class="invite-venue-address">Dress code: <?= e($event['dress_code']) ?></div>
            <?php endif; ?>
        </div>

        <div class="invite-actions intro a-cta">
            <?php if ($uuid !== null): ?>
                <button class="btn btn-primary shimmer" type="button" data-show-ticket aria-controls="ve">Xem thông tin tham dự</button>
            <?php else: ?>
                <p class="invite-note">
                    <svg class="invite-note-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6.5l8.5 6 8.5-6"/></svg>
                    <span>Vui lòng mở link trong email mời để xem vé của bạn.</span>
                </p>
            <?php endif; ?>
        </div>
    </main>

    <?php if ($uuid !== null): ?>
        <?php require __DIR__ . '/partials/ticket.php'; ?>
    <?php endif; ?>
</div>

<?php if ($uuid !== null): ?>
    <?php require __DIR__ . '/partials/ticket-overlays.php'; ?>
<?php endif; ?>

<script src="js/motion.js?v=<?= filemtime(BASE_PATH . '/public/js/motion.js') ?>"></script>
<?php if ($uuid !== null && $canDownload): ?>
    <script src="js/ticket.js?v=<?= filemtime(BASE_PATH . '/public/js/ticket.js') ?>"></script>
<?php endif; ?>
</body>
</html>
