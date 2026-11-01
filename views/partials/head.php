<?php
/**
 * Phần <head> dùng chung cho trang thiệp, vé và link lỗi.
 *
 * @var array $event
 * @var string $pageTitle
 */

$eventTitle = "{$event['name']} {$event['year']}";
$ogImage = appUrl() !== '' ? appUrl() . '/' . $event['invitation_image'] : $event['invitation_image'];
$ogDescription = "{$event['weekday']}, {$event['date_text']} · {$event['time']} · {$event['venue_name']}";
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#050505">
<title><?= e($pageTitle) ?></title>
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($eventTitle) ?> · THK Holdings Vietnam">
<meta property="og:description" content="<?= e($ogDescription) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600&family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,300..700,0..100,0..1;1,9..144,300..700,0..100,0..1&family=Pinyon+Script&display=swap" rel="stylesheet">
<link href="css/theme.css?v=<?= filemtime(BASE_PATH . '/public/css/theme.css') ?>" rel="stylesheet">
<link href="css/invite.css?v=<?= filemtime(BASE_PATH . '/public/css/invite.css') ?>" rel="stylesheet">
