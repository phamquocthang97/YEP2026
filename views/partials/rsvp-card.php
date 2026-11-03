<?php
/**
 * Ruột thẻ xác nhận tham dự: form hai lựa chọn khi chưa trả lời; đã trả lời thì
 * lời cảm ơn + "Đổi phản hồi" (mở lại form).
 * Dùng cho lần render đầu và cho phản hồi JSON (rsvpHtml) của public/rsvp.php.
 *
 * @var array $event
 * @var array $employee người nhận của link mời hợp lệ
 * @var array|null $rsvp phản hồi mới nhất của người này, null => chưa trả lời
 * @var array $rsvpErrors lỗi theo field: 'attending', 'form'
 * @var string|null $rsvpDraft lựa chọn vừa gửi ('yes'|'no') để giữ lại khi form lỗi
 */

$options = [
    'yes' => 'Tham gia được',
    'no'  => 'Rất tiếc, tôi không thể đến',
];
// Fragment đưa người dùng không JS về đúng section khi form báo lỗi (fetch bỏ
// fragment, 303 có fragment riêng)
$rsvpAction = 'rsvp.php' . inviteQuery((string) employeeUuid($employee)) . '#xac-nhan';

ob_start();
?>
<?php if (isset($rsvpErrors['form'])): ?>
    <p class="rsvp-alert" role="alert"><?= e($rsvpErrors['form']) ?></p>
<?php endif; ?>

<form class="rsvp-form" method="post" action="<?= e($rsvpAction) ?>" novalidate data-rsvp-form>
    <fieldset
        class="rsvp-field"
        <?= isset($rsvpErrors['attending']) ? 'aria-describedby="rsvp-attending-error"' : '' ?>
    >
        <legend class="rsvp-label">Bạn sẽ tham gia chứ?</legend>
        <?php foreach ($options as $value => $label): ?>
            <label class="rsvp-option">
                <input
                    type="radio"
                    name="attending"
                    value="<?= $value ?>"
                    required<?= $rsvpDraft === $value ? ' checked' : '' ?>
                >
                <span><?= e($label) ?></span>
            </label>
        <?php endforeach; ?>
        <?php if (isset($rsvpErrors['attending'])): ?>
            <p class="rsvp-error" id="rsvp-attending-error"><?= e($rsvpErrors['attending']) ?></p>
        <?php endif; ?>
    </fieldset>

    <button class="rsvp-submit" type="submit">Xác nhận</button>
</form>
<?php
$formHtml = ob_get_clean();
?>
<?php if ($rsvp !== null): ?>
    <div class="rsvp-done" role="status">
        <svg
            class="rsvp-done-icon"
            aria-hidden="true"
            width="28"
            height="28"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linecap="round"
            stroke-linejoin="round"
        ><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
        <?php if (($rsvp['attending'] ?? false) === true): ?>
            <p class="rsvp-thanks">
                Cảm ơn bạn đã xác nhận! Hẹn gặp bạn tại <?= e($event['title']) ?>.
            </p>
        <?php else: ?>
            <p class="rsvp-thanks">
                Cảm ơn bạn đã phản hồi. Rất tiếc vì lần này chưa thể gặp bạn tại
                <?= e($event['title']) ?>.
            </p>
        <?php endif; ?>
    </div>

    <details class="rsvp-change"<?= $rsvpErrors !== [] ? ' open' : '' ?>>
        <summary>Đổi phản hồi</summary>
        <?= $formHtml ?>
    </details>
<?php else: ?>
    <?= $formHtml ?>
<?php endif; ?>
