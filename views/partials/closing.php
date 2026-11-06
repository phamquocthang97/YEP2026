<?php
/**
 * Section cuối trang: ảnh lớn phủ kín, nửa trên giữ nguyên ảnh, nửa dưới có lớp
 * phủ tối dần để chứa lời cảm ơn. Nội dung lấy từ config/event.php (khoá 'closing');
 * lời chào và lời cảm ơn đều trống => bỏ cả section, ảnh trống => chỉ còn lớp phủ trên nền chung.
 *
 * @var array $event
 */

$closing = $event['closing'] ?? [];
$closingImage = stringField($closing, 'image');
$closingLead = stringField($closing, 'lead');
$closingTitle = stringField($closing, 'title');
$closingNote = stringField($closing, 'note');
$closingCopyright = stringField($closing, 'copyright');
$closingCredit = stringField($closing, 'credit');

if ($closingLead === '' && $closingTitle === '') {
    return;
}
?>
<section class="page page-closing" id="cam-on" aria-label="<?= e($closingTitle !== '' ? $closingTitle : $closingLead) ?>">
    <?php if ($closingImage !== ''): ?>
        <div class="closing-photo" aria-hidden="true">
            <img src="<?= e($closingImage) ?>" alt="" loading="lazy" decoding="async">
        </div>
    <?php endif; ?>

    <div class="closing-inner">
        <h2 class="closing-heading" data-reveal style="--d: 0">
            <?php if ($closingLead !== ''): ?>
                <span class="closing-lead"><?= e($closingLead) ?></span>
            <?php endif; ?>
            <?php if ($closingTitle !== ''): ?>
                <span class="closing-title"><?= e($closingTitle) ?></span>
            <?php endif; ?>
        </h2>
        <?php if ($closingNote !== ''): ?>
            <p class="closing-note" data-reveal style="--d: 1"><?= e($closingNote) ?></p>
        <?php endif; ?>
        <?php if ($closingCopyright !== '' || $closingCredit !== ''): ?>
            <?php /* Không dùng data-reveal: footer nằm sát đáy trang, rơi vào vùng -12% rootMargin của reveal (story.js) nên không bao giờ được hiện. */ ?>
            <footer class="closing-footer">
                <?php if ($closingCopyright !== ''): ?>
                    <p class="closing-copyright"><?= e($closingCopyright) ?></p>
                <?php endif; ?>
                <?php if ($closingCredit !== ''): ?>
                    <p class="closing-credit"><?= e($closingCredit) ?></p>
                <?php endif; ?>
            </footer>
        <?php endif; ?>
    </div>
</section>
