<?php
$rsvpErrors ??= [];
$rsvpDraft ??= null;
$opened ??= false;

$pageTitle = "Thiệp mời {$event['title']}";
$uuid = $employee !== null ? employeeUuid($employee) : null;
$stylesheets = ['theme', 'invite', 'recap', 'rsvp', 'story'];

require __DIR__ . '/partials/program-data.php';

// Nội dung thiệp lấy từ config/event.php (khoá 'invite'); tháng/thứ để trống =>
// suy ra từ ngày sự kiện.
$invite = $event['invite'];
$eventDate = new DateTimeImmutable($event['date']);
$monthLabel = $invite['month_label'] !== ''
    ? $invite['month_label']
    : strtoupper($eventDate->format('M'));
$weekdayLabel = $invite['weekday_label'] !== ''
    ? $invite['weekday_label']
    : strtoupper($eventDate->format('l'));
$dayLabel = (string) (int) $event['day'];
?>
<!doctype html>
<html lang="vi"<?= $opened ? ' data-opened' : '' ?>>
<head>
    <?php require __DIR__ . '/partials/head.php'; ?>
    <script>
        // Trang bắt đầu bằng lớp giấy xé phủ toàn màn hình. Đã mở trong phiên,
        // hoặc vào thẳng một section (hash / render lại sau lỗi form) => bỏ qua
        // cổng, hiện thẳng trạng thái cuối.
        // motion.js không chạy được (lỗi/mạng chậm) => gỡ lớp giấy; story.js
        // không chạy được => gỡ `js` để nội dung vẫn đọc được.
        (function (root) {
            var SKIP_HASHES = ['#thiep', '#xac-nhan', '#ve', '#lich-trinh'];
            var opened = false;

            // Mọi khung thiết kế dựng theo iPhone SE 375×667 rồi phóng/thu theo --s.
            // Điện thoại dọc: theo chiều rộng (tránh giật khi thanh địa chỉ co
            // giãn); màn lớn: vừa cả rộng lẫn cao.
            // Điện thoại ngang (≤ 640px): không phóng theo chiều rộng (khung sẽ
            // cao gấp 3 màn hình, chữ quá to) => giữ cỡ thiết kế, chỉ phóng khi
            // màn đủ cao.
            // Rộng lấy từ clientWidth (layout viewport, trừ thanh cuộn):
            // innerWidth gồm thanh cuộn => khung tràn phải, và trên iOS đổi
            // theo pinch-zoom.
            // Ngưỡng 640 vẫn theo innerWidth để khớp @media (max-width: 640px).
            function fit() {
                var w = root.clientWidth;
                var h = window.innerHeight;
                var s = window.innerWidth > 640 ? Math.min(w / 375, h / 667)
                    : w > h ? Math.min(w / 375, Math.max(1, h / 667))
                    : w / 375;
                root.style.setProperty('--s', s.toFixed(4));
            }

            fit();
            window.addEventListener('resize', fit);
            // Thanh cuộn xuất hiện sau khi nội dung nạp không phát sự kiện resize
            if (window.ResizeObserver) {
                new ResizeObserver(fit).observe(root);
            }
            root.classList.add('js');

            setTimeout(function () {
                if (!window.storyReady) {
                    root.classList.remove('js');
                }
            }, 4000);

            try {
                opened = sessionStorage.getItem('yep26-opened') === '1';
            } catch (e) {
                // sessionStorage bị chặn (chế độ riêng tư) => coi như chưa mở
            }

            if (
                opened
                || root.hasAttribute('data-opened')
                || SKIP_HASHES.indexOf(location.hash) !== -1
            ) {
                root.classList.add('is-skip');
                return;
            }

            root.classList.add('has-cover', 'is-waiting');
            setTimeout(function () {
                if (!window.yepMotionReady) {
                    root.classList.remove('has-cover', 'is-waiting');
                    document.dispatchEvent(new CustomEvent('yep:cover-open'));
                }
            }, 4000);
        })(document.documentElement);
    </script>
</head>
<body>
<div class="tear-cover">
    <canvas class="tear-canvas" aria-hidden="true"></canvas>
    <div class="tear-hint">
        <div class="tear-hand-wrap" aria-hidden="true">
            <svg
                class="tear-hand"
                width="30"
                height="30"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.4"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M9 11V5.5a1.5 1.5 0 0 1 3 0V10"/>
                <path d="M12 9.5a1.5 1.5 0 0 1 3 0V11"/>
                <path d="M15 10.5a1.5 1.5 0 0 1 3 0v3.5a6 6 0 0 1-6 6h-.6a5 5 0 0 1-3.9-1.9L5 14.7a1.5 1.5 0 0 1 2.3-1.9L9 14.5"/>
            </svg>
        </div>
        <div class="tear-hint-text">Vuốt xuống để mở thiệp</div>
        <button class="tear-btn" type="button">Hoặc nhấn để mở</button>
    </div>
</div>

<!--
    Ánh sáng chủ đạo của cả trang: lớp cố định trên nền chung, đổi trạng thái
    dawn/light theo vùng cuộn (story.css, story.js)
-->
<div class="dawn" aria-hidden="true"></div>

<div class="scene">
    <section class="page page-invite">
        <?php if ($invite['background_desktop'] !== ''): ?>
            <div
                class="page-bg has-pc"
                aria-hidden="true"
                style="background-image: url('<?= e($invite['background_desktop']) ?>')"
            ></div>
        <?php elseif ($invite['background'] !== ''): ?>
            <div
                class="page-bg"
                aria-hidden="true"
                style="background-image: url('<?= e($invite['background']) ?>')"
            ></div>
        <?php endif; ?>
        <div class="stage">
            <section class="invite" id="thiep">
                <div class="invite-card">
                    <div
                        class="card-bg<?= $invite['background_desktop'] !== '' ? ' has-pc' : '' ?>"
                    >
                        <?php if ($invite['background'] !== ''): ?>
                            <div
                                class="card-bg-sp"
                                style="background-image: url('<?= e($invite['background']) ?>')"
                            ></div>
                        <?php endif; ?>
                        <?php if ($invite['background_desktop'] !== ''): ?>
                            <div
                                class="card-bg-pc"
                                style="background-image: url('<?= e($invite['background_desktop']) ?>')"
                            ></div>
                        <?php endif; ?>
                    </div>

                    <?php if ($invite['logo'] !== ''): ?>
                        <img
                            class="card-logo iv intro"
                            style="--d: 0"
                            src="<?= e($invite['logo']) ?>"
                            alt="<?= e($event['company_name']) ?>"
                        >
                    <?php endif; ?>

                    <div class="card-theme iv intro" style="--d: 1">
                        <?php if ($invite['theme_label'] !== ''): ?>
                            <div class="card-eyebrow"><?= e($invite['theme_label']) ?></div>
                        <?php endif; ?>
                        <h2 class="card-heading hero-title heading-primary">
                            <?php foreach ($invite['title_lines'] as $line): ?>
                                <span><?= e($line) ?></span>
                            <?php endforeach; ?>
                        </h2>
                    </div>

                    <div class="card-when iv intro" style="--d: 2">
                        <div class="card-side"><?= e($weekdayLabel) ?></div>
                        <div class="card-date">
                            <span class="card-month"><?= e($monthLabel) ?></span>
                            <span class="card-day"><?= e($dayLabel) ?></span>
                            <span class="card-year"><?= e($event['year']) ?></span>
                        </div>
                        <div class="card-side">
                            <?= e(trim($invite['time_prefix'] . ' ' . $event['time'])) ?>
                        </div>
                    </div>

                    <div class="card-where iv intro" style="--d: 3">
                        <?php if ($invite['location_label'] !== ''): ?>
                            <div class="card-label"><?= e($invite['location_label']) ?></div>
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
                            <svg
                                class="invite-note-icon"
                                aria-hidden="true"
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="M3.5 6.5l8.5 6 8.5-6"/>
                            </svg>
                            <span>Vui lòng mở link trong email mời để xem vé của bạn.</span>
                        </p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </section>
</div>

<?php require __DIR__ . '/partials/recap.php'; ?>

<?= renderPartial('seam.php') ?>

<div class="scene">
    <?php if ($employee !== null): ?>
        <?php require __DIR__ . '/partials/rsvp.php'; ?>
        <div id="ve" data-ticket-slot>
            <?= renderPartial('ticket-slot.php', [
                'event' => $event,
                'employee' => $employee,
                'showTicket' => $showTicket,
            ]) ?>
        </div>
    <?php endif; ?>

    <?php require __DIR__ . '/partials/schedule.php'; ?>
</div>

<?php if ($showTicket): ?>
    <?php require __DIR__ . '/partials/ticket-overlays.php'; ?>
<?php endif; ?>

<script src="<?= asset('js/motion.js') ?>"></script>
<script src="<?= asset('js/recap.js') ?>"></script>
<script src="<?= asset('js/story.js') ?>"></script>
<?php if ($showTicket && canDownloadTicket($employee)): ?>
    <script src="<?= asset('js/ticket.js') ?>" data-ticket-js></script>
<?php endif; ?>
</body>
</html>
