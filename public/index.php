<?php

require dirname(__DIR__) . '/bootstrap.php';

use App\Mail\SentLog;

$employees = loadEmployees();
$sent = (new SentLog(STORAGE_PATH . '/sent.json'))->all();
$testTo = mailTestRecipient();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thư mời YEP 2026</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f5f7;
            --surface: #ffffff;
            --text: #1d1d1f;
            --muted: #6e6e73;
            --track: #e8e8ed;
            --blue: #0071e3;
            --blue-hover: #0077ed;
            --green: #34c759;
            --green-text: #248a3d;
            --red: #ff3b30;
            --red-text: #d70015;
            --amber-bg: #fff8e6;
            --amber-text: #8a5300;
            --focus: 0 0 0 4px rgba(0, 113, 227, .3);
            --font: -apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Display", Inter, "Helvetica Neue", Arial, sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font);
            font-size: 17px;
            line-height: 1.47;
            letter-spacing: -.022em;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== Thanh điều hướng ===== */
        .nav-bar {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(245, 245, 247, .8);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, .06);
        }

        .nav-inner {
            max-width: 820px;
            margin: 0 auto;
            padding: 0 22px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
        }

        .nav-brand img {
            display: block;
            height: 24px;
            width: auto;
        }

        .nav-meta {
            color: var(--muted);
        }

        /* ===== Bố cục ===== */
        .page {
            max-width: 820px;
            margin: 0 auto;
            padding: 0 22px 96px;
        }

        .test-banner {
            margin-top: 20px;
            padding: 12px 18px;
            border-radius: 14px;
            background: var(--amber-bg);
            color: var(--amber-text);
            font-size: 14px;
        }

        .test-banner code {
            color: inherit;
            font-weight: 600;
        }

        /* ===== Hero ===== */
        .hero {
            padding: 64px 0 56px;
            text-align: center;
        }

        .hero-title {
            font-size: clamp(34px, 6vw, 52px);
            font-weight: 600;
            letter-spacing: -.035em;
            line-height: 1.08;
            margin: 0 0 12px;
        }

        .hero-lead {
            max-width: 520px;
            margin: 0 auto 32px;
            font-size: 19px;
            color: var(--muted);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 12px 24px;
        }

        .legend {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px 24px;
            margin-top: 28px;
            font-size: 14px;
            color: var(--muted);
        }

        .legend-item strong {
            color: var(--text);
            font-weight: 600;
            margin-left: 4px;
        }

        .legend-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: 1px;
        }

        /* ===== Nút ===== */
        .btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 22px;
            border: 0;
            border-radius: 980px;
            background: var(--blue);
            color: #fff;
            font-size: 17px;
            font-weight: 500;
            transition: background-color .2s, transform .1s, opacity .2s;
        }

        .btn-pill:hover {
            background: var(--blue-hover);
        }

        .btn-pill:active {
            transform: scale(.97);
        }

        .btn-pill:disabled {
            opacity: .45;
            cursor: default;
            transform: none;
        }

        .btn-link-apple {
            border: 0;
            background: none;
            padding: 0;
            color: var(--blue);
            font-size: 17px;
        }

        .btn-link-apple:hover {
            text-decoration: underline;
        }

        .btn-link-apple:disabled {
            display: none;
        }

        .btn-pill:focus-visible,
        .btn-link-apple:focus-visible,
        .icon-btn:focus-visible,
        .field:focus-within {
            outline: none;
            box-shadow: var(--focus);
        }

        /* ===== Khối nội dung ===== */
        .section-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin: 0 4px 10px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -.02em;
            margin: 0;
        }

        .section-note {
            font-size: 14px;
            color: var(--muted);
        }

        /* Ô nhập email gửi thủ công */
        .field {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            padding: 6px 6px 6px 20px;
            background: var(--surface);
            border-radius: 980px;
            transition: box-shadow .2s;
        }

        .field input {
            flex: 1;
            min-width: 0;
            border: 0;
            outline: 0;
            background: transparent;
            font-size: 17px;
            color: var(--text);
        }

        .field input::placeholder {
            color: #86868b;
        }

        .field .btn-pill {
            min-height: 38px;
            padding: 0 18px;
            font-size: 15px;
        }

        .manual-result {
            min-height: 22px;
            margin: 10px 20px 0;
            font-size: 14px;
        }

        .manual-result.is-muted { color: var(--muted); }
        .manual-result.is-success { color: var(--green-text); }
        .manual-result.is-danger { color: var(--red-text); }

        /* Danh sách nhân viên kiểu inset grouped list */
        .people {
            list-style: none;
            margin: 0;
            padding: 0;
            background: var(--surface);
            border-radius: 18px;
            overflow: hidden;
        }

        .person {
            position: relative;
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr) 190px 36px;
            align-items: center;
            gap: 14px;
            padding: 14px 16px 14px 20px;
        }

        .person + .person::before {
            content: "";
            position: absolute;
            top: 0;
            left: 62px;
            right: 0;
            border-top: 1px solid var(--track);
        }

        .person-index {
            align-self: start;
            padding-top: 1px;
            text-align: right;
            font-size: 15px;
            color: var(--muted);
            font-variant-numeric: tabular-nums;
        }

        .person-name {
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .person-code {
            margin-left: 6px;
            font-size: 13px;
            font-weight: 400;
            color: var(--muted);
        }

        .person-email {
            font-size: 14px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status-cell {
            min-width: 0;
            font-size: 14px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .status-pending { color: var(--muted); }
        .status-sending { color: var(--blue); }
        .status-sent { color: var(--green-text); }
        .status-test { color: var(--blue); }
        .status-failed { color: var(--red-text); }

        .status-sent i { color: var(--green); }
        .status-failed i { color: var(--red); }

        .status-meta {
            display: block;
            margin-top: 1px;
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #c7c7cc;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 50%;
            background: transparent;
            color: var(--blue);
            font-size: 17px;
            transition: background-color .2s;
        }

        .icon-btn:hover {
            background: #f0f0f5;
        }

        .icon-btn:disabled {
            color: #c7c7cc;
            background: transparent;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--muted);
        }

        @media (max-width: 640px) {
            .hero {
                padding: 40px 0;
            }

            .person {
                grid-template-columns: 28px minmax(0, 1fr) 36px;
                row-gap: 6px;
            }

            .status-cell {
                grid-column: 2;
                grid-row: 2;
            }

            .icon-btn {
                grid-column: 3;
                grid-row: 1 / span 2;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .btn-pill {
                transition: none;
            }
        }
    </style>
</head>
<body>
<header class="nav-bar">
    <div class="nav-inner">
        <span class="nav-brand">
            <img src="images/thk-logo.png" alt="THK Holdings Vietnam" width="131" height="24">
        </span>
        <span class="nav-meta">Year End Party 2026</span>
    </div>
</header>

<main class="page">
    <?php if ($testTo !== ''): ?>
        <div class="test-banner">
            Chế độ thử nghiệm đang bật. Tất cả email sẽ được chuyển đến <code><?= e($testTo) ?></code> . Email chưa được gửi đến người nhận thực tế.
        </div>
    <?php endif; ?>

    <section class="hero">
        <h1 class="hero-title">Gửi thư mời Year End Party.</h1>

        <div class="hero-actions">
            <button id="btnBulk" class="btn-pill"></button>
            <button id="btnRetryFailed" class="btn-link-apple"></button>
        </div>

        <div class="legend" role="status" aria-live="polite">
            <span class="legend-item"><span class="legend-dot" style="background: var(--green)"></span>Đã gửi<strong id="countSent">0</strong></span>
            <span class="legend-item"><span class="legend-dot" style="background: var(--red)"></span>Lỗi<strong id="countFailed">0</strong></span>
            <span class="legend-item"><span class="legend-dot" style="background: #c7c7cc"></span>Chưa gửi<strong id="countPending">0</strong></span>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h2 class="section-title">Gửi đến một địa chỉ email</h2>
        </div>
        <form id="manualForm" novalidate>
            <label class="field">
                <span class="visually-hidden">Email người nhận</span>
                <input type="email" id="manualEmail" placeholder="name@thk-hd.vn" autocomplete="off" required>
                <button type="submit" id="btnManual" class="btn-pill">Gửi</button>
            </label>
        </form>
        <div id="manualResult" class="manual-result" aria-live="polite"></div>
    </section>

    <section class="section">
        <div class="section-head">
            <h2 class="section-title">Nhân viên</h2>
            <span class="section-note"><?= count($employees) ?> người</span>
        </div>

        <?php if (count($employees) === 0): ?>
            <div class="people empty-state">Chưa có nhân viên nào. Thêm email vào data.json rồi tải lại trang.</div>
        <?php else: ?>
            <ul class="people">
                <?php $index = 0; foreach ($employees as $email => $employee): ?>
                    <li class="person"
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
                        <div class="status-cell"></div>
                        <button class="icon-btn btn-resend" title="Gửi lại" aria-label="Gửi lại cho <?= e($email) ?>">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>

<script>
    // ===== Cấu hình & trạng thái =====
    const SEND_DELAY_MS = 1000;

    const STATUS = {
        PENDING: 'pending',
        SENDING: 'sending',
        SENT: 'sent',
        TEST: 'test',
        FAILED: 'failed',
    };

    const rows = [...document.querySelectorAll('.person[data-email]')];

    const queueState = {
        running: false,
        done: 0,
        total: 0,
    };

    // ===== Helpers =====
    // Các section có thể bị ẩn/comment trong HTML => mọi truy cập phần tử phải chịu được null.
    function getElement(id) {
        return document.getElementById(id);
    }

    function setText(id, text) {
        const element = getElement(id);

        if (element) {
            element.textContent = text;
        }
    }

    function setDisabled(id, disabled) {
        const element = getElement(id);

        if (element) {
            element.disabled = disabled;
        }
    }

    function onEvent(id, eventName, handler) {
        getElement(id)?.addEventListener(eventName, handler);
    }

    function sleep(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function findRow(email) {
        return rows.find((row) => row.dataset.email === email);
    }

    function getRowsByStatus(status) {
        return rows.filter((row) => row.dataset.status === status);
    }

    function getUnsentRows() {
        return rows.filter((row) => row.dataset.status !== STATUS.SENT);
    }

    // ===== API =====

    /**
     * Gọi send.php. Không throw, luôn trả về {ok, error?, sent_at?, test?}.
     */
    async function requestSendEmail(email, force = false) {
        const body = new FormData();
        body.append('email', email);

        if (force) {
            body.append('force', '1');
        }

        try {
            const response = await fetch('send.php', {method: 'POST', body});
            return await response.json();
        } catch (error) {
            return {ok: false, error: 'Không kết nối được máy chủ: ' + error.message};
        }
    }

    /**
     * send.php trả 409 kèm sent_at khi email đã được gửi trước đó.
     */
    function isAlreadySent(result) {
        return !result.ok && Boolean(result.sent_at);
    }

    // ===== Render =====
    function statusHtml(status, info) {
        switch (status) {
            case STATUS.SENDING:
                return '<span class="status status-sending"><span class="spinner-border spinner-border-sm"></span>Đang gửi</span>';
            case STATUS.SENT:
                return '<span class="status status-sent"><i class="bi bi-check-circle-fill"></i>Đã gửi</span>'
                    + `<span class="status-meta">${escapeHtml(info)}</span>`;
            case STATUS.TEST:
                return '<span class="status status-test"><i class="bi bi-check-circle"></i>Đã gửi thử</span>';
            case STATUS.FAILED:
                return '<span class="status status-failed"><i class="bi bi-exclamation-circle-fill"></i>Gửi lỗi</span>'
                    + `<span class="status-meta" title="${escapeHtml(info)}">${escapeHtml(info)}</span>`;
            default:
                return '<span class="status status-pending"><span class="status-dot"></span>Chưa gửi</span>';
        }
    }

    /**
     * info: thời gian gửi (sent) hoặc lý do lỗi (failed).
     */
    function renderRowStatus(row, status, info = '') {
        row.dataset.status = status;

        if (status === STATUS.SENT) {
            row.dataset.sentAt = info;
        }

        const cell = row.querySelector('.status-cell');

        if (cell) {
            cell.innerHTML = statusHtml(status, info);
        }
    }

    function renderSummary() {
        const total = rows.length;
        const sentCount = getRowsByStatus(STATUS.SENT).length;
        const failedCount = getRowsByStatus(STATUS.FAILED).length;

        setText('countSent', sentCount);
        setText('countFailed', failedCount);
        setText('countPending', total - sentCount - failedCount);
    }

    /**
     * Nhãn nút gửi hàng loạt: đang gửi x/y, đã gửi hết, hoặc số người chưa nhận.
     */
    function renderBulkButton(unsentCount) {
        const btnBulk = getElement('btnBulk');

        if (!btnBulk) {
            return;
        }

        const {running, done, total} = queueState;

        if (running) {
            btnBulk.innerHTML = `<span class="spinner-border spinner-border-sm"></span>Đang gửi ${done + 1}/${total}`;
        } else if (unsentCount === 0) {
            btnBulk.innerHTML = '<i class="bi bi-check2"></i>Đã gửi cho tất cả';
        } else {
            btnBulk.textContent = `Gửi cho ${unsentCount} người chưa nhận`;
        }

        btnBulk.disabled = running || unsentCount === 0;
    }

    /**
     * Bật/tắt và đặt nhãn các nút theo trạng thái hàng đợi.
     */
    function renderControls() {
        const {running} = queueState;
        const failedCount = getRowsByStatus(STATUS.FAILED).length;

        renderBulkButton(getUnsentRows().length);

        setText('btnRetryFailed', `Gửi lại ${failedCount} thư lỗi`);
        setDisabled('btnRetryFailed', running || failedCount === 0);
        setDisabled('btnManual', running);

        rows.forEach((row) => {
            const button = row.querySelector('.btn-resend');

            if (button) {
                button.disabled = running;
            }
        });
    }

    function renderPage() {
        renderSummary();
        renderControls();
    }

    /**
     * type: muted | success | danger
     */
    function showManualResult(type, message) {
        const resultBox = getElement('manualResult');

        if (!resultBox) {
            return;
        }

        resultBox.className = `manual-result is-${type}`;
        resultBox.textContent = message;
    }

    // ===== Gửi email =====

    /**
     * Cập nhật dòng theo kết quả từ send.php. row = undefined khi email không có trong danh sách.
     */
    function applySendResult(row, result) {
        if (!row) {
            return;
        }

        if (result.ok) {
            renderRowStatus(row, result.test ? STATUS.TEST : STATUS.SENT, result.sent_at ?? '');
        } else if (isAlreadySent(result)) {
            renderRowStatus(row, STATUS.SENT, result.sent_at);
        } else {
            renderRowStatus(row, STATUS.FAILED, result.error);
        }

        renderPage();
    }

    /**
     * Gửi email của 1 dòng trong hàng đợi, không hỏi xác nhận.
     */
    async function sendRow(row) {
        renderRowStatus(row, STATUS.SENDING);
        applySendResult(row, await requestSendEmail(row.dataset.email));
    }

    /**
     * Gửi 1 email lẻ. Nếu đã gửi trước đó thì hỏi xác nhận rồi gửi lại với force.
     */
    async function sendWithConfirm(email) {
        const row = findRow(email);

        if (row) {
            renderRowStatus(row, STATUS.SENDING);
        }

        let result = await requestSendEmail(email);

        if (isAlreadySent(result) && confirm(`${email} đã nhận thư lúc ${result.sent_at}. Gửi lại?`)) {
            result = await requestSendEmail(email, true);
        }

        applySendResult(row, result);

        return result;
    }

    // ===== Hàng đợi gửi hàng loạt =====
    function startQueue(total) {
        queueState.running = true;
        queueState.done = 0;
        queueState.total = total;
        renderControls();
    }

    function finishQueue() {
        queueState.running = false;
        renderControls();
    }

    /**
     * Gửi tuần tự: gửi xong 1 email (có response) thì chờ SEND_DELAY_MS rồi mới gửi email tiếp theo.
     */
    async function runQueue(queue) {
        startQueue(queue.length);

        for (const row of queue) {
            await sendRow(row);
            queueState.done++;

            const isLastRow = queueState.done === queue.length;

            if (!isLastRow) {
                renderControls();
                await sleep(SEND_DELAY_MS);
            }
        }

        finishQueue();
    }

    // ===== Event handlers =====
    function onBulkClick() {
        const queue = getUnsentRows();

        if (queue.length > 0 && confirm(`Gửi thư mời cho ${queue.length} người chưa nhận?`)) {
            runQueue(queue);
        }
    }

    function onRetryFailedClick() {
        runQueue(getRowsByStatus(STATUS.FAILED));
    }

    async function onRowResendClick(event) {
        const button = event.currentTarget;
        const row = button.closest('.person');

        button.disabled = true;
        await sendWithConfirm(row.dataset.email);
        button.disabled = queueState.running;
    }

    async function onManualSubmit(event) {
        event.preventDefault();

        const input = getElement('manualEmail');

        if (!input) {
            return;
        }

        const email = input.value.trim().toLowerCase();

        if (email === '' || !input.checkValidity()) {
            showManualResult('danger', 'Nhập một địa chỉ email hợp lệ, ví dụ ten@thk-hd.vn.');
            input.focus();
            return;
        }

        setDisabled('btnManual', true);
        showManualResult('muted', `Đang gửi cho ${email}…`);

        const result = await sendWithConfirm(email);

        if (result.ok) {
            showManualResult('success', result.test ? `Đã gửi thử cho ${email}.` : `Đã gửi cho ${email}.`);
        } else if (isAlreadySent(result)) {
            showManualResult('muted', `Không gửi lại cho ${email}.`);
        } else {
            showManualResult('danger', `Chưa gửi được cho ${email}: ${result.error}`);
        }

        setDisabled('btnManual', queueState.running);
    }

    // ===== Khởi tạo =====
    function init() {
        rows.forEach((row) => {
            renderRowStatus(row, row.dataset.status, row.dataset.sentAt);
            row.querySelector('.btn-resend')?.addEventListener('click', onRowResendClick);
        });

        onEvent('btnBulk', 'click', onBulkClick);
        onEvent('btnRetryFailed', 'click', onRetryFailedClick);
        onEvent('manualForm', 'submit', onManualSubmit);

        renderPage();
    }

    init();
</script>
</body>
</html>
