<?php
/**
 * Recap "A Year to Remember": 8 chương kể lại một năm, nội dung lấy từ config/recap.php. Chương 01 là tiêu đề h1 duy nhất của trang.
 *
 * @var array $event
 * @var array $recap
 */

$chapters = [
    'mo-dau'      => 'Mở đầu',
    'khoi-dau'    => 'Những ngày đầu tiên',
    'hanh-trinh'  => 'Hành trình đã qua',
    'thu-thach'   => 'Những thử thách',
    'dau-an'      => 'Những dấu ấn',
    'con-nguoi'   => 'Những con người',
    'khoanh-khac' => 'Những khoảnh khắc',
    'chuong-moi'  => 'Chương tiếp theo',
];

// Placeholder trong config bắt đầu bằng "[" => đánh dấu "cần bổ sung"
$isTodo = fn (?string $value): bool => str_starts_with(trim((string) $value), '[');

$text = fn (?string $value): string => $isTodo($value)
    ? '<span class="todo">' . e($value) . '</span>'
    : e($value);

// Ảnh thật nếu có src, ngược lại là khung ảnh chờ kèm mô tả cần chụp
$photo = function (array $item) use ($isTodo): string {
    $note = (string) ($item['note'] ?? '');
    $src = trim((string) ($item['src'] ?? $item['image'] ?? ''));

    if ($src !== '') {
        $alt = $isTodo($note) ? '' : $note;

        return '<img src="' . e($src) . '" alt="' . e($alt) . '" loading="lazy" decoding="async">';
    }

    return '<span class="ph" role="img" aria-label="Ảnh chờ bổ sung: ' . e(trim($note, '[]')) . '">'
        . '<span class="ph-note">' . e($note) . '</span></span>';
};

$chapterNo = fn (string $id): string => sprintf('%02d', array_search($id, array_keys($chapters), true) + 1);
$layouts = ['frame', 'pair', 'band'];
?>
<a class="skip" href="#khoi-dau">Bỏ qua phần mở đầu</a>

<div class="progress" aria-hidden="true"><span class="progress-bar"></span></div>

<nav class="rail" aria-label="Các chương">
    <ol>
        <?php foreach ($chapters as $id => $label): ?>
            <li><a href="#<?= $id ?>" data-rail="<?= $id ?>"><span class="rail-no"><?= $chapterNo($id) ?></span><span class="rail-label"><?= e($label) ?></span></a></li>
        <?php endforeach; ?>
    </ol>
</nav>

<main class="recap">
    <!-- 01 · Mở đầu: phòng trưng bày bật đèn -->
    <header class="hero" id="mo-dau" data-chapter>
        <div class="hero-light" aria-hidden="true"></div>
        <div class="hero-frame" aria-hidden="true">
            <?php if ($recap['hero']['image'] !== ''): ?>
                <img src="<?= e($recap['hero']['image']) ?>" alt="">
            <?php endif; ?>
        </div>
        <div class="hero-inner">
            <div class="plaque hero-plaque">
                <img src="images/thk-logo.png" alt="THK Holdings Vietnam" width="657" height="120">
            </div>
            <p class="hero-kicker"><?= e($event['name']) ?> <?= e($event['year']) ?></p>
            <h1 class="hero-title heading-primary">
                <span class="line"><span>A Year</span></span>
                <span class="line"><span>to Remember</span></span>
            </h1>
            <p class="hero-sub">Hành trình một năm đáng nhớ</p>
            <p class="hero-lead"><?= $text($recap['hero']['lead']) ?></p>
            <a class="cta" href="#khoi-dau">
                <span>Bắt đầu hành trình</span>
                <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
            </a>
        </div>
    </header>

    <!-- 02 · Những ngày đầu tiên: trang nhật ký mở ra -->
    <section class="chapter beginning tone-wine" id="khoi-dau" data-chapter>
        <div class="wrap beginning-grid">
            <div class="beginning-text">
                <p class="chapter-no" data-reveal>Chương <?= $chapterNo('khoi-dau') ?>, The Beginning</p>
                <h2 class="chapter-title heading-primary" data-reveal="title">Những ngày<br>đầu tiên</h2>
                <p class="chapter-intro" data-reveal><?= $text($recap['beginning']['intro']) ?></p>
                <p class="body" data-reveal><?= $text($recap['beginning']['direction']) ?></p>
            </div>

            <div class="diary" data-reveal="diary">
                <div class="diary-page diary-left">
                    <p class="diary-date"><?= $text($recap['beginning']['date']) ?></p>
                    <figure class="polaroid tilt-l">
                        <?= $photo(['src' => $recap['beginning']['image'], 'note' => $recap['beginning']['image_note']]) ?>
                    </figure>
                </div>
                <div class="diary-page diary-right">
                    <p class="diary-head">Những điều chúng ta tự hứa</p>
                    <ol class="diary-goals">
                        <?php foreach ($recap['beginning']['goals'] as $goal): ?>
                            <li><?= $text($goal) ?></li>
                        <?php endforeach; ?>
                    </ol>
                    <p class="diary-foot"><?= $text($recap['beginning']['first_steps']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- 03 · Hành trình đã qua: dòng thời gian vẽ dần theo cuộn trang -->
    <section class="chapter journey" id="hanh-trinh" data-chapter>
        <div class="wrap">
            <div class="chapter-head center">
                <p class="chapter-no" data-reveal>Chương <?= $chapterNo('hanh-trinh') ?>, The Journey</p>
                <h2 class="chapter-title heading-primary" data-reveal="title">Hành trình đã qua</h2>
            </div>
        </div>

        <div class="wrap timeline">
            <div class="spine" aria-hidden="true"><span class="spine-fill"></span></div>

            <?php foreach ($recap['milestones'] as $i => $m): ?>
                <?php
                $layout = $m['layout'] ?? $layouts[$i % count($layouts)];
                $images = $m['images'] ?: [['src' => '', 'note' => '[Ảnh thực tế]']];
                ?>
                <article class="milestone is-<?= e($layout) ?> <?= $i % 2 ? 'is-flip' : '' ?>" data-reveal="milestone">
                    <span class="milestone-dot" aria-hidden="true"></span>
                    <div class="milestone-media">
                        <?php if ($layout === 'pair'): ?>
                            <?php foreach (array_slice($images, 0, 2) as $j => $img): ?>
                                <figure class="polaroid <?= $j ? 'tilt-r' : 'tilt-l' ?>" data-parallax="<?= $j ? '-0.06' : '0.04' ?>"><?= $photo($img) ?></figure>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <figure class="<?= $layout === 'band' ? 'band-photo' : 'gilt' ?>" data-parallax="<?= $layout === 'band' ? '0.08' : '0' ?>"><?= $photo($images[0]) ?></figure>
                        <?php endif; ?>
                    </div>
                    <div class="milestone-body">
                        <p class="milestone-time"><?= $text($m['time']) ?></p>
                        <h3 class="milestone-title"><?= $text($m['title']) ?></h3>
                        <p class="body"><?= $text($m['story']) ?></p>
                        <?php if (($m['result'] ?? '') !== ''): ?>
                            <p class="milestone-result"><span>Kết quả</span><?= $text($m['result']) ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 04 · Những thử thách: từ bóng tối ra ánh sáng -->
    <section class="chapter challenges" id="thu-thach" data-chapter>
        <div class="challenges-light" aria-hidden="true"></div>
        <div class="wrap narrow">
            <p class="chapter-no" data-reveal>Chương <?= $chapterNo('thu-thach') ?>, The Challenges</p>
            <h2 class="chapter-title heading-primary" data-reveal="title">Những thử thách<br>đã vượt qua</h2>
            <p class="chapter-intro" data-reveal><?= $text($recap['challenges']['intro']) ?></p>

            <ol class="trials">
                <?php foreach ($recap['challenges']['items'] as $item): ?>
                    <li class="trial" data-reveal>
                        <p class="trial-hard"><?= $text($item['hard']) ?></p>
                        <p class="trial-over"><span>Chúng ta đã vượt qua bằng cách</span><?= $text($item['overcome']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>

            <p class="turn" data-reveal="title"><?= $text($recap['challenges']['turn']) ?></p>
        </div>
    </section>

    <!-- 05 · Những dấu ấn: con số đặt trong bối cảnh -->
    <section class="chapter achievements" id="dau-an" data-chapter>
        <div class="wrap">
            <div class="chapter-head">
                <p class="chapter-no" data-reveal>Chương <?= $chapterNo('dau-an') ?>, The Achievements</p>
                <h2 class="chapter-title heading-primary" data-reveal="title">Những dấu ấn<br>đáng nhớ</h2>
            </div>

            <div class="stats">
                <?php foreach ($recap['achievements']['stats'] as $stat): ?>
                    <div class="stat" data-reveal>
                        <?php if (is_numeric($stat['value'])): ?>
                            <p class="stat-value" data-count="<?= e((string) $stat['value']) ?>"><span class="stat-num"><?= e((string) $stat['value']) ?></span><?= e($stat['suffix']) ?></p>
                        <?php else: ?>
                            <p class="stat-value is-empty" aria-label="Số liệu cần bổ sung">000</p>
                        <?php endif; ?>
                        <div class="stat-text">
                            <h3 class="stat-label"><?= $text($stat['label']) ?></h3>
                            <p class="body"><?= $text($stat['context']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <ul class="labels">
                <?php foreach ($recap['achievements']['highlights'] as $h): ?>
                    <li class="plaque label" data-reveal>
                        <p class="label-kind"><?= $text($h['kind']) ?></p>
                        <h3 class="label-title"><?= $text($h['title']) ?></h3>
                        <p class="label-note"><?= $text($h['note']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- 06 · Những con người: bức tường tranh treo kiểu salon -->
    <section class="chapter people tone-wine" id="con-nguoi" data-chapter>
        <div class="wrap">
            <div class="chapter-head">
                <p class="chapter-no" data-reveal>Chương <?= $chapterNo('con-nguoi') ?>, The People</p>
                <h2 class="chapter-title heading-primary" data-reveal="title">Những con người<br>tạo nên hành trình</h2>
                <p class="chapter-intro" data-reveal><?= $text($recap['people']['intro']) ?></p>
            </div>

            <ul class="salon">
                <?php foreach ($recap['people']['photos'] as $i => $p): ?>
                    <li class="salon-item s<?= $i % 7 + 1 ?>" data-reveal>
                        <figure class="gilt" tabindex="0">
                            <?= $photo($p) ?>
                            <figcaption class="salon-cap"><?= $text($p['note']) ?></figcaption>
                        </figure>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- 07 · Những khoảnh khắc: album polaroid cuộn ngang -->
    <section class="chapter moments" id="khoanh-khac" data-chapter>
        <div class="wrap moments-head">
            <div>
                <p class="chapter-no" data-reveal>Chương <?= $chapterNo('khoanh-khac') ?>, The Moments</p>
                <h2 class="chapter-title heading-primary" data-reveal="title">Những khoảnh khắc<br>không quên</h2>
                <p class="chapter-intro" data-reveal><?= $text($recap['moments']['intro']) ?></p>
            </div>
            <div class="album-nav">
                <button type="button" class="album-btn" data-album="-1" aria-label="Ảnh trước">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
                </button>
                <button type="button" class="album-btn" data-album="1" aria-label="Ảnh tiếp theo">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                </button>
            </div>
        </div>

        <ul class="album" tabindex="0" aria-label="Album khoảnh khắc, cuộn ngang để xem thêm">
            <?php foreach ($recap['moments']['photos'] as $i => $p): ?>
                <li class="album-item">
                    <figure class="polaroid <?= $i % 2 ? 'tilt-r' : 'tilt-l' ?>">
                        <?= $photo($p) ?>
                        <figcaption><?= $text($p['note']) ?></figcaption>
                    </figure>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- 08 · Chương tiếp theo: ánh sáng mở rộng -->
    <section class="chapter closing" id="chuong-moi" data-chapter>
        <div class="closing-light" aria-hidden="true"></div>
        <div class="wrap closing-inner">
            <p class="chapter-no" data-reveal>Chương <?= $chapterNo('chuong-moi') ?>, The Next Chapter</p>
            <h2 class="chapter-title heading-primary" data-reveal="title">Chương tiếp theo</h2>

            <div class="letter" data-reveal>
                <p><?= $text($recap['closing']['looking_back']) ?></p>
                <p><?= $text($recap['closing']['thanks']) ?></p>
                <p><?= $text($recap['closing']['next_year']) ?></p>
                <p class="letter-sign"><?= $text($recap['closing']['signature']) ?></p>
            </div>

            <p class="closing-message" data-reveal><?= $text($recap['closing']['message']) ?></p>

            <p class="finale" data-reveal="title">
                <span class="line"><span>Thank you for being</span></span>
                <span class="line"><span>part of our journey.</span></span>
            </p>

            <div class="sign-off" data-reveal>
                <p class="sign-event"><?= e(strtoupper($event['name'])) ?> <?= e($event['year']) ?></p>
                <p class="sign-meta">
                    <span><?= e($event['weekday']) ?>, <?= e($event['date_text']) ?></span>
                    <span><?= e($event['time']) ?></span>
                    <span><?= e($event['venue_name']) ?></span>
                </p>
                <div class="plaque sign-logo">
                    <img src="images/thk-logo.png" alt="THK Holdings Vietnam" width="657" height="120" loading="lazy">
                </div>
                <a class="cta cta-ghost" href="#thiep">Xem thiệp mời</a>
            </div>
        </div>
    </section>
</main>
