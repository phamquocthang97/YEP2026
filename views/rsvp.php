<?php
/**
 * Trang xác nhận tham dự: nhập tên + chọn tham gia / không, gửi xong hiện lời cảm ơn.
 *
 * @var array $event
 * @var string $name
 * @var string|null $attending
 * @var array $errors
 * @var bool $submitted
 */

$pageTitle = "Xác nhận tham dự {$event['name']} {$event['year']}";
$deadline = trim((string) ($event['rsvp_deadline'] ?? ''));
$deadlineText = $deadline !== '' ? (new DateTimeImmutable($deadline))->format('d.m.Y') : '';
$inv = $event['invite'];
$options = [
    'yes' => 'Tham gia được',
    'no'  => 'Rất tiếc, tôi không thể đến',
];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#050505">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600&family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,300..700,0..100,0..1;1,9..144,300..700,0..100,0..1&display=swap" rel="stylesheet">
    <link href="css/theme.css?v=<?= filemtime(BASE_PATH . '/public/css/theme.css') ?>" rel="stylesheet">
    <link href="css/rsvp.css?v=<?= filemtime(BASE_PATH . '/public/css/rsvp.css') ?>" rel="stylesheet">
</head>
<body>
<main class="rsvp">
    <div class="rsvp-bg<?= $inv['background_desktop'] !== '' ? ' has-pc' : '' ?>" aria-hidden="true">
        <?php if ($inv['background'] !== ''): ?>
            <div class="rsvp-bg-sp" style="background-image: url('<?= e($inv['background']) ?>')"></div>
        <?php endif; ?>
        <?php if ($inv['background_desktop'] !== ''): ?>
            <div class="rsvp-bg-pc" style="background-image: url('<?= e($inv['background_desktop']) ?>')"></div>
        <?php endif; ?>
    </div>

    <?php if ($inv['logo'] !== ''): ?>
        <img class="rsvp-logo" src="<?= e($inv['logo']) ?>" alt="THK Holdings Vietnam">
    <?php endif; ?>

    <div class="rsvp-wrap">
        <header class="rsvp-intro">
            <p class="rsvp-eyebrow"><?= e($event['name']) ?> <?= e($event['year']) ?></p>
            <h1 class="rsvp-title">Xác nhận tham dự</h1>

            <?php if (!$submitted && $deadlineText !== ''): ?>
                <p class="rsvp-lead">Vui lòng phản hồi trước ngày <?= e($deadlineText) ?> để chúng tôi kịp chuẩn bị chu đáo.</p>
            <?php endif; ?>

            <dl class="rsvp-facts">
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

        <div class="rsvp-card">
            <?php if ($submitted): ?>
                <div class="rsvp-done" role="status">
                    <svg class="rsvp-done-icon" aria-hidden="true" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                    <?php if ($_GET['done'] === 'no'): ?>
                        <p class="rsvp-thanks">Cảm ơn bạn đã phản hồi. Rất tiếc vì lần này chưa thể gặp bạn tại <?= e($event['name']) ?> <?= e($event['year']) ?>.</p>
                    <?php else: ?>
                        <p class="rsvp-thanks">Cảm ơn bạn đã xác nhận! Hẹn gặp bạn tại <?= e($event['name']) ?> <?= e($event['year']) ?>.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php if (isset($errors['form'])): ?>
                    <p class="rsvp-alert" role="alert"><?= e($errors['form']) ?></p>
                <?php endif; ?>

                <form class="rsvp-form" method="post" novalidate>
                    <div class="rsvp-field">
                        <label class="rsvp-label" for="rsvp-name">Tên của bạn *</label>
                        <input class="rsvp-input" id="rsvp-name" name="name" type="text" value="<?= e($name) ?>"
                               placeholder="Họ và tên khách mời" autocomplete="name" maxlength="100" required
                               <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="rsvp-name-error"' : '' ?>>
                        <?php if (isset($errors['name'])): ?>
                            <p class="rsvp-error" id="rsvp-name-error"><?= e($errors['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <fieldset class="rsvp-field"<?= isset($errors['attending']) ? ' aria-describedby="rsvp-attending-error"' : '' ?>>
                        <legend class="rsvp-label">Bạn sẽ tham gia chứ?</legend>
                        <?php foreach ($options as $value => $label): ?>
                            <label class="rsvp-option">
                                <input type="radio" name="attending" value="<?= $value ?>" required<?= $attending === $value ? ' checked' : '' ?>>
                                <span><?= e($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                        <?php if (isset($errors['attending'])): ?>
                            <p class="rsvp-error" id="rsvp-attending-error"><?= e($errors['attending']) ?></p>
                        <?php endif; ?>
                    </fieldset>

                    <button class="rsvp-submit" type="submit">Xác nhận</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>
