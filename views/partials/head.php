<?php
/**
 * Phần <head> dùng chung cho trang landing và link lỗi.
 *
 * @var array $event
 * @var string $pageTitle
 * @var string[]|null $stylesheets tên file trong public/css/ (không đuôi .css),
 * mặc định theme + invite
 */

$stylesheets ??= ['theme', 'invite'];
$ogDescription = "{$event['weekday']}, {$event['date_text']}"
    . " · {$event['time']} · {$event['venue_name']}";
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#241019">
<title><?= e($pageTitle) ?></title>
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($event['title']) ?> · <?= e($event['company_name']) ?>">
<meta property="og:description" content="<?= e($ogDescription) ?>">
<?php // Font tự host (css/fonts.css + css/fonts/), không tải từ Google Fonts ?>
<link href="<?= e(asset('css/fonts.css')) ?>" rel="stylesheet">
<?php foreach ($stylesheets as $stylesheet): ?>
<link href="<?= e(asset("css/{$stylesheet}.css")) ?>" rel="stylesheet">
<?php endforeach; ?>
