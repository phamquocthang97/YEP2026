<?php
/**
 * Vé tham dự cá nhân (ẩn, hiện khi bấm "Xem thông tin tham dự"). Thiếu Lucky Draw Code / số bàn => "Đang cập nhật", không cho tải vé.
 *
 * @var array $event
 * @var array $employee
 */

$name = trim((string) ($employee['name'] ?? ''));
$nameWords = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
$luckyDrawCode = trim((string) ($employee['luckyDrawCode'] ?? ''));
$tableNumber = trim((string) ($employee['tableNumber'] ?? ''));
$isTableNumeric = ctype_digit($tableNumber);
$tableText = $isTableNumeric ? sprintf('%02d', (int) $tableNumber) : $tableNumber;
$canDownload = $luckyDrawCode !== '' && $tableNumber !== '';
?>
    <main class="ticket-page" id="ve" hidden>
        <div class="eyebrow ticket-page-label a-head">Vé tham dự cá nhân</div>

        <div class="ticket">
            <section class="ticket-body" aria-label="Thông tin vé">
                <div class="frame" aria-hidden="true"><span class="frame-t"></span><span class="frame-r"></span><span class="frame-b"></span><span class="frame-l"></span></div>
                <div class="frame is-thin" aria-hidden="true"><span class="frame-t"></span><span class="frame-r"></span><span class="frame-b"></span><span class="frame-l"></span></div>

                <div class="ticket-head a-head">
                    <span class="ticket-script"><?= e($event['name']) ?></span>
                    <span class="ticket-year tnum"><?= e($event['year']) ?></span>
                </div>

                <div class="eyebrow ticket-label">Khách mời</div>
                <h2 class="ticket-name">
                    <?php foreach ($nameWords as $index => $word): ?>
                        <span class="word" style="--i: <?= $index ?>"><?= e($word) ?></span>
                    <?php endforeach; ?>
                </h2>

                <div class="ticket-rule" aria-hidden="true"></div>

                <div class="ticket-details">
                    <div class="ticket-code-block">
                        <div class="eyebrow ticket-label">Lucky Draw Code</div>
                        <?php if ($luckyDrawCode !== ''): ?>
                            <div class="ticket-code tnum"><?= e($luckyDrawCode) ?></div>
                        <?php else: ?>
                            <div class="ticket-pending">
                                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></svg>
                                Đang cập nhật
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="ticket-stub" aria-label="Số bàn">
                <div class="perf" aria-hidden="true"></div>
                <div class="frame" aria-hidden="true"><span class="frame-t"></span><span class="frame-r"></span><span class="frame-b"></span><span class="frame-l"></span></div>

                <div class="ticket-stub-text">
                    <div class="eyebrow ticket-stub-label">Số bàn</div>
                    <div class="ticket-table-caption"><?= $tableNumber !== '' ? 'Bàn của bạn' : 'Đang cập nhật' ?></div>
                </div>
                <div class="ticket-table a-table tnum"<?= $isTableNumeric ? ' data-count-to="' . (int) $tableNumber . '"' : '' ?>><?= $tableNumber !== '' ? e($tableText) : '—' ?></div>
            </section>
        </div>

        <div class="ticket-actions a-back">
            <div class="ticket-buttons">
                <button class="btn btn-ghost" type="button" data-show-invite>
                    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
                    <span class="label-mobile">Thiệp mời</span><span class="label-desktop">Quay lại thiệp</span>
                </button>
                <?php if ($canDownload): ?>
                    <button class="btn btn-primary" type="button"
                            data-ticket-download
                            data-name="<?= e($name) ?>"
                            data-code="<?= e($luckyDrawCode) ?>"
                            data-table="<?= e($tableText) ?>"
                            data-script="<?= e($event['name']) ?>"
                            data-year="<?= e($event['year']) ?>">
                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11"/><path d="M7 10.5l5 5 5-5"/><path d="M5 19.5h14"/></svg>
                        <span>Tải vé</span>
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary" type="button" disabled aria-describedby="save-note">
                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11"/><path d="M7 10.5l5 5 5-5"/><path d="M5 19.5h14"/></svg>
                        <span>Tải vé</span>
                    </button>
                <?php endif; ?>
            </div>
            <?php if (!$canDownload): ?>
                <p id="save-note" class="ticket-note">Tải được vé khi số bàn và mã Lucky Draw đã cập nhật.</p>
            <?php endif; ?>
        </div>
    </main>
