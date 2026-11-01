<?php
/**
 * Thiệp mời + vé tham dự trong cùng một trang cuộn (thiệp ở trên, vé ngay bên dưới). $employee = null khi mở link chung (không có id).
 *
 * @var array $event
 * @var array|null $employee
 */

$pageTitle = "Thiệp mời {$event['name']} {$event['year']}";
$uuid = $employee !== null ? employeeUuid($employee) : null;

// Nội dung thiệp lấy từ config/event.php (khoá 'invite'); tháng/thứ để trống => suy ra từ ngày sự kiện.
$inv = $event['invite'];
$eventDate = new DateTimeImmutable($event['date']);
$monthLabel = $inv['month_label'] !== '' ? $inv['month_label'] : strtoupper($eventDate->format('M'));
$weekdayLabel = $inv['weekday_label'] !== '' ? $inv['weekday_label'] : strtoupper($eventDate->format('l'));
$dayLabel = (string) (int) $event['day'];
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

            // Mọi khung thiết kế dựng theo iPhone SE 375×667 rồi phóng/thu theo --s.
            // Điện thoại: theo chiều rộng (tránh giật khi thanh địa chỉ co giãn); màn lớn: vừa cả rộng lẫn cao.
            function fit() {
                var w = window.innerWidth;
                var s = w <= 640 ? w / 375 : Math.min(w / 375, window.innerHeight / 667);
                root.style.setProperty('--s', s.toFixed(4));
            }

            fit();
            window.addEventListener('resize', fit);

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
    <section class="page page-invite">
        <?php if ($inv['background'] !== ''): ?>
            <div class="page-bg" aria-hidden="true" style="background-image: url('<?= e($inv['background']) ?>')"></div>
        <?php endif; ?>
        <div class="stage">
            <main class="invite" id="thiep">
                <div class="invite-card">
                    <div class="card-bg<?= $inv['background_desktop'] !== '' ? ' has-pc' : '' ?>">
                        <?php if ($inv['background'] !== ''): ?>
                            <div class="card-bg-sp" style="background-image: url('<?= e($inv['background']) ?>')"></div>
                        <?php endif; ?>
                        <?php if ($inv['background_desktop'] !== ''): ?>
                            <div class="card-bg-pc" style="background-image: url('<?= e($inv['background_desktop']) ?>')"></div>
                        <?php endif; ?>
                    </div>

                    <?php if ($inv['logo'] !== ''): ?>
                        <img class="card-logo iv intro" style="--d: 0" src="<?= e($inv['logo']) ?>" alt="THK Holdings Vietnam">
                    <?php endif; ?>

                    <div class="card-theme iv intro" style="--d: 1">
                        <?php if ($inv['theme_label'] !== ''): ?>
                            <div class="card-eyebrow"><?= e($inv['theme_label']) ?></div>
                        <?php endif; ?>
                        <h1 class="card-heading">
                            <?php foreach ($inv['title_lines'] as $line): ?>
                                <span><?= e($line) ?></span>
                            <?php endforeach; ?>
                        </h1>
                    </div>

                    <div class="card-when iv intro" style="--d: 2">
                        <div class="card-side"><?= e($weekdayLabel) ?></div>
                        <div class="card-date">
                            <span class="card-month"><?= e($monthLabel) ?></span>
                            <span class="card-day"><?= e($dayLabel) ?></span>
                            <span class="card-year"><?= e($event['year']) ?></span>
                        </div>
                        <div class="card-side"><?= e(trim($inv['time_prefix'] . ' ' . $event['time'])) ?></div>
                    </div>

                    <div class="card-where iv intro" style="--d: 3">
                        <?php if ($inv['location_label'] !== ''): ?>
                            <div class="card-label"><?= e($inv['location_label']) ?></div>
                        <?php endif; ?>
                        <?php if ($event['venue_name'] !== ''): ?>
                            <div class="card-name"><?= e($event['venue_name']) ?></div>
                        <?php endif; ?>
                        <?php if ($event['venue_address'] !== ''): ?>
                            <div class="card-address"><?= e($event['venue_address']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($uuid === null): ?>
                    <div class="invite-actions intro a-cta">
                        <p class="invite-note">
                            <svg class="invite-note-icon" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6.5l8.5 6 8.5-6"/></svg>
                            <span>Vui lòng mở link trong email mời để xem vé của bạn.</span>
                        </p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </section>

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
