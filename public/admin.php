<?php

require dirname(__DIR__) . '/bootstrap.php';

header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex, nofollow');

$adminToken = $_GET['token'] ?? null;

if (!isAdminToken($adminToken)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403 Forbidden');
}

$employees = loadEmployees();
$mailTestRecipient = mailTestRecipient();

$missingUuidEmails = array_keys(array_filter(
    $employees,
    fn (array $employee) => employeeUuid($employee) === null
));
// data.json theo uuid nên uuid không thể trùng; email trùng thì chỉ một khách hiện trong danh sách
$duplicateEmails = findDuplicateValues(guestList()->all(), 'email');
$duplicateCodes = findDuplicateValues($employees, 'luckyDrawCode');

$sentLogError = null;
try {
    $sent = sentLog()->all();
} catch (RuntimeException $e) {
    $sent = [];
    $sentLogError = $e->getMessage();
}

// Xác nhận tham dự: rsvp.json hỏng => ẩn phần RSVP, phần gửi thư vẫn chạy
$rsvpError = null;
try {
    $rsvpEntries = rsvpLog()->entries();
} catch (RuntimeException $e) {
    $rsvpEntries = null;
    $rsvpError = $e->getMessage();
}

// Khách tự đăng ký có email ngoài data.json: chưa có mã Lucky Draw, số bàn và thư mời
$selfRegistered = array_diff_key($rsvpEntries ?? [], guestList()->all());
$missingTicketEmails = array_keys(array_filter(
    $employees,
    fn (array $employee) => isAttending($rsvpEntries[$employee['uuid']]['rsvp'] ?? null)
        && !canDownloadTicket($employee)
));

// Một cột của phần xác nhận tham dự trong row; rsvp.json hỏng (không có entry) => để trống
$rsvpSlot = fn (?array $entry, string $slot): string => $entry !== null
    ? renderPartial('admin-rsvp.php', ['entry' => $entry, 'slot' => $slot])
    : '';

// Cột Vé: mã Lucky Draw và số bàn trong data.json, thiếu => "—" (khách tự đăng ký luôn thiếu)
$ticketCell = function (array $guest): string {
    $code = stringField($guest, 'luckyDrawCode');
    $table = stringField($guest, 'tableNumber');

    return '<div class="person-ticket">'
        . '<div title="Mã Lucky Draw">' . ($code !== '' ? e($code) : '<span class="ticket-empty">—</span>') . '</div>'
        . '<div title="Số bàn">' . ($table !== '' ? 'Bàn ' . e($table) : '<span class="ticket-empty">—</span>') . '</div>'
        . '</div>';
};

// Tiêu đề cột dùng chung cho các danh sách (ẩn trên mobile)
$peopleHead = '<div class="people-head" aria-hidden="true">'
    . '<span></span><span>Khách</span><span>Vé</span><span>Trạng thái</span><span>Thời gian</span><span>Thao tác</span>'
    . '</div>';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Thư mời YEP 2026</title>
    <link href="<?= asset('css/fonts-admin.css') ?>" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet" >
    <link href="css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
</head>
<body
    data-sending-locked="<?= $sentLogError !== null ? 'true' : 'false' ?>"
    data-admin-token="<?= e($adminToken) ?>"
>
<header class="nav-bar">
    <div class="nav-inner">
        <span class="nav-brand">
            <img src="images/thk-logo.png" alt="THK Holdings Vietnam" width="131" height="24">
        </span>
        <span class="nav-meta">Year End Party 2026</span>
    </div>
</header>

<main class="page">
    <?php if ($mailTestRecipient !== ''): ?>
        <div class="test-banner">
            Chế độ thử nghiệm đang bật.
        </div>
    <?php endif; ?>

    <?php if ($sentLogError !== null): ?>
        <div class="test-banner is-danger" role="alert">
            Đã khoá gửi email vì không đọc được nhật ký gửi.
            <br><small><?= e($sentLogError) ?></small>
        </div>
    <?php endif; ?>

    <?php if ($missingUuidEmails !== []): ?>
        <div class="test-banner is-danger" role="alert">
            <?= count($missingUuidEmails) ?> nhân viên thiếu hoặc sai định dạng
            <code>uuid</code>, sẽ không gửi được thư mời.
            <br><small><?= e(implode(', ', $missingUuidEmails)) ?></small>
        </div>
    <?php endif; ?>

    <?php if ($duplicateEmails !== []): ?>
        <div class="test-banner is-danger" role="alert">
            Trùng <code>email</code> trong data.json, các khách này
            sẽ không xác nhận tham dự hay tra cứu vé được.
            <?php foreach ($duplicateEmails as $email => $uuids): ?>
                <br><small><?= e($email) ?>: <?= e(implode(', ', $uuids)) ?></small>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($duplicateCodes !== []): ?>
        <div class="test-banner" role="alert">
            Trùng <code>luckyDrawCode</code>, kiểm tra lại data.json.
            <?php foreach ($duplicateCodes as $code => $emails): ?>
                <br><small><?= e(strtoupper($code)) ?>: <?= e(implode(', ', $emails)) ?></small>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($rsvpError !== null): ?>
        <div class="test-banner is-danger" role="alert">
            Không đọc được <code>rsvp.json</code>, đang ẩn phần xác nhận tham dự.
            <br><small><?= e($rsvpError) ?></small>
        </div>
    <?php endif; ?>

    <?php if ($missingTicketEmails !== []): ?>
        <div class="test-banner" role="alert">
            <?= count($missingTicketEmails) ?> khách đã xác nhận tham gia nhưng thiếu
            <code>luckyDrawCode</code> hoặc <code>tableNumber</code>, chưa tải được vé.
            <br><small><?= e(implode(', ', $missingTicketEmails)) ?></small>
        </div>
    <?php endif; ?>

    <section class="hero">
        <h1 class="hero-title">Year End Party 2026</h1>

        <div class="hero-actions">
            <?php
            //<button id="btnBulk" class="btn-pill"></button>
            //<button id="btnRetryFailed" class="btn-link-apple"></button>
             ?>
        </div>

		<?php
//        <div class="legend" role="status" aria-live="polite">
//            <span class="legend-item">
//                <span class="legend-dot" style="background: var(--green)"></span>Đã gửi<strong id="countSent">0</strong>
//            </span>
//            <span class="legend-item">
//                <span class="legend-dot" style="background: var(--red)"></span>Lỗi<strong id="countFailed">0</strong>
//            </span>
//            <span class="legend-item">
//                <span class="legend-dot" style="background: #c7c7cc"></span>Chưa gửi<strong id="countPending">0</strong>
//            </span>
//        </div>
	  	?>

        <?php if ($rsvpEntries !== null): ?>
            <div class="legend" role="status" aria-live="polite">
                <span class="legend-label">Xác nhận tham dự</span>
                <span class="legend-item">
                    <span class="legend-dot" style="background: var(--green)"></span>Tham gia<strong id="countAttending">0</strong>
                </span>
                <span class="legend-item">
                    <span class="legend-dot" style="background: var(--red)"></span>Không tham gia<strong id="countDeclined">0</strong>
                </span>
                <span class="legend-item">
                    <span class="legend-dot" style="background: #c7c7cc"></span>Chưa phản hồi<strong id="countNoReply">0</strong>
                </span>
                <a class="btn-link-apple legend-link" href="admin-export.php?token=<?= e(rawurlencode($adminToken)) ?>">
                    <i class="bi bi-download"></i> Tải CSV
                </a>
            </div>
        <?php endif; ?>
    </section>

    <section class="section">
        <div class="section-head">
            <h2 class="section-title">Nhân viên</h2>
            <span class="section-note"><?= count($employees) ?> người</span>
        </div>

        <?php if (count($employees) === 0): ?>
            <div class="people empty-state">
                Chưa có nhân viên nào. Thêm email vào data.json rồi tải lại trang.
            </div>
        <?php else: ?>
            <?= $peopleHead ?>
            <ul class="people">
                <?php $index = 0; foreach ($employees as $email => $employee): ?>
                    <?php $entry = $rsvpEntries[$employee['uuid']] ?? null; ?>
                    <li class="person"
                        data-uuid="<?= e($employee['uuid']) ?>"
                        data-email="<?= e($email) ?>"
                        data-status="<?= isset($sent[$email]) ? 'sent' : 'pending' ?>"
                        data-sent-at="<?= e($sent[$email] ?? '') ?>">
                        <div class="person-index"><?= ++$index ?></div>
                        <div class="person-main">
                            <div class="person-name">
                                <?= e($employee['name'] ?? '') ?>
                                <span class="person-code"><?= e($employee['code'] ?? '') ?></span>
                            </div>
                            <div class="person-email"><?= e($email) ?></div>
                        </div>
                        <?= $ticketCell($employee) ?>
                        <div class="person-status">
                            <div class="status-cell"></div>
                            <?= $rsvpSlot($entry, 'status') ?>
                        </div>
                        <div class="person-times">
                            <div class="time-row time-sent" hidden></div>
                            <?= $rsvpSlot($entry, 'time') ?>
                        </div>
                        <div class="person-actions">
                           <?php
//                            <button
//                                class="icon-btn btn-resend"
//                                title="Gửi lại thư mời"
/*                                aria-label="Gửi lại thư mời cho <?= e($email) ?>"*/
//                            >
//                                <i class="bi bi-arrow-clockwise"></i>
//                            </button>
                           ?>
                            <?= $rsvpSlot($entry, 'actions') ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <?php if ($selfRegistered !== []): ?>
        <section class="section">
            <div class="section-head">
                <h2 class="section-title">Khách tự đăng ký</h2>
                <span class="section-note"><span id="selfRegisteredCount"><?= count($selfRegistered) ?></span> người</span>
            </div>
            <p class="section-hint"></p>
            <?= $peopleHead ?>
            <ul class="people" id="selfRegisteredList">
                <?php $index = 0; foreach ($selfRegistered as $uuid => $entry): ?>
                    <li class="person" data-uuid="<?= e($uuid) ?>">
                        <div class="person-index"><?= ++$index ?></div>
                        <div class="person-main">
                            <div class="person-name"><?= e(stringField($entry['guest'], 'name')) ?></div>
                            <div class="person-email"><?= e($entry['guest']['email']) ?></div>
                        </div>
                        <?= $ticketCell($entry['guest']) ?>
                        <div class="person-status"><?= $rsvpSlot($entry, 'status') ?></div>
                        <div class="person-times"><?= $rsvpSlot($entry, 'time') ?></div>
                        <!-- Khách tự đăng ký chưa có thư mời: chừa ô của nút Gửi lại để các nút thẳng cột -->
                        <div class="person-actions"><span class="icon-btn-spacer"></span><?= $rsvpSlot($entry, 'actions') ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
</main>

<script src="<?= asset('js/app.js') ?>"></script>
<?php if ($rsvpEntries !== null): ?>
    <script src="<?= asset('js/admin-rsvp.js') ?>"></script>
<?php endif; ?>
</body>
</html>
