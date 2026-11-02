<?php
/**
 * Section Xác nhận tham gia. Chỉ dựng khi có người nhận hợp lệ ($employee !== null): không có id thì không có form.
 *
 * @var array $event
 * @var array $employee
 * @var array|null $rsvp
 * @var array $rsvpErrors
 * @var string|null $rsvpDraft
 */

$deadline = trim((string) ($event['rsvp_deadline'] ?? ''));
$deadlineText = $deadline !== '' ? (new DateTimeImmutable($deadline))->format('d.m.Y') : '';
?>
<section class="page page-rsvp" id="xac-nhan" aria-labelledby="rsvp-title">
    <div class="rsvp-wrap">
        <header class="rsvp-intro">
            <p class="rsvp-eyebrow" data-reveal style="--d: 0"><?= e($event['name']) ?> <?= e($event['year']) ?></p>
            <h2 class="rsvp-title heading-primary" id="rsvp-title" data-reveal style="--d: 1">Xác nhận tham dự</h2>

            <?php if ($rsvp === null && $deadlineText !== ''): ?>
                <p class="rsvp-lead" data-reveal style="--d: 2">Vui lòng phản hồi trước ngày <?= e($deadlineText) ?> để chúng tôi kịp chuẩn bị chu đáo.</p>
            <?php endif; ?>

            <dl class="rsvp-facts" data-reveal style="--d: 3">
                <div>
                    <dt>Thời gian</dt>
                    <dd><?= e($event['weekday']) ?>, <?= e($event['date_text']) ?> · <?= e($event['time']) ?></dd>
                </div>
                <div>
                    <dt>Địa điểm</dt>
                    <dd><?= e($event['venue_name']) ?><?php if ($event['venue_address'] !== ''): ?><span><?= e($event['venue_address']) ?></span><?php endif; ?></dd>
                </div>
            </dl>
        </header>

        <div class="rsvp-card" id="rsvp-card" data-rsvp-card data-reveal style="--d: 4">
            <?php require __DIR__ . '/rsvp-card.php'; ?>
        </div>
    </div>
</section>
