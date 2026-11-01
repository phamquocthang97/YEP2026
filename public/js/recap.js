// ===== Landing "A Year to Remember": hiện dần khi cuộn, timeline, ánh sáng theo cuộn, album =====
// Thời lượng/easing của hiệu ứng nằm trong css/recap.css. File này chỉ gắn trạng thái và giá trị theo vị trí cuộn.
const RECAP = {
    reveal: {
        rootMargin: '0px 0px -12% 0px',
        threshold: .15,
    },
    activeLine: .45,      // chương đang xem = chương có mép trên vượt 45% chiều cao màn hình
    spineLead: .6,        // timeline vẽ tới điểm 60% chiều cao màn hình
    counterMs: 1600,
};

window.recapReady = true;

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const clamp = (v, min = 0, max = 1) => Math.min(max, Math.max(min, v));

// ===== Đếm số liệu =====
function countUp(el) {
    const raw = el.dataset.count;
    const target = parseFloat(raw);
    const decimals = (raw.split('.')[1] || '').length;
    const num = el.querySelector('.stat-num');
    const format = (v) => v.toLocaleString('vi-VN', {minimumFractionDigits: decimals, maximumFractionDigits: decimals});

    if (reduced || !Number.isFinite(target)) {
        num.textContent = format(target);
        return;
    }

    const start = performance.now();
    const tick = (now) => {
        const t = clamp((now - start) / RECAP.counterMs);
        num.textContent = format(target * (1 - Math.pow(1 - t, 3)));

        if (t < 1) {
            requestAnimationFrame(tick);
        }
    };

    num.textContent = format(0);
    requestAnimationFrame(tick);
}

// ===== Hiện dần khi cuộn tới =====
function initReveal() {
    const items = document.querySelectorAll('[data-reveal]');
    const show = (el) => {
        el.classList.add('is-in');
        el.querySelectorAll('[data-count]').forEach(countUp);
    };

    if (reduced || !('IntersectionObserver' in window)) {
        items.forEach(show);
        return;
    }

    // Tiêu đề bị clip-path che hết => diện tích giao bằng 0, nên theo dõi qua phần tử cha
    const watched = new Map();
    items.forEach((el) => {
        const target = el.dataset.reveal === 'title' ? el.parentElement : el;
        watched.set(target, [...(watched.get(target) || []), el]);
    });

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                watched.get(entry.target).forEach(show);
                io.unobserve(entry.target);
            }
        });
    }, RECAP.reveal);

    watched.forEach((_, target) => io.observe(target));
}

// ===== Giá trị theo vị trí cuộn: tiến trình, chương hiện tại, timeline, ánh sáng, parallax =====
function initScroll() {
    const bar = document.querySelector('.progress-bar');
    const rail = document.querySelector('.rail');
    const railItems = new Map([...document.querySelectorAll('[data-rail]')].map((a) => [a.dataset.rail, a.parentElement]));
    const chapters = [...document.querySelectorAll('[data-chapter]')];
    const timeline = document.querySelector('.timeline');
    const spine = document.querySelector('.spine-fill');
    const challenges = document.querySelector('.challenges');
    const challengesLight = document.querySelector('.challenges-light');
    const closing = document.querySelector('.closing');
    const closingLight = document.querySelector('.closing-light');
    const parallax = reduced ? [] : [...document.querySelectorAll('[data-parallax]')]
        .map((el) => ({
            // Ảnh tràn màn hình: dịch ảnh bên trong khung, khung đứng yên
            el: el.classList.contains('band-photo') ? el.firstElementChild : el,
            box: el,
            factor: parseFloat(el.dataset.parallax) || 0,
        }))
        .filter((p) => p.factor !== 0);

    let active = null;
    let queued = false;

    const update = () => {
        queued = false;
        const vh = window.innerHeight;
        const doc = document.documentElement;
        const max = doc.scrollHeight - vh;

        bar.style.transform = `scaleX(${max > 0 ? clamp(window.scrollY / max) : 0})`;

        let current = chapters[0];
        chapters.forEach((ch) => {
            if (ch.getBoundingClientRect().top <= vh * RECAP.activeLine) {
                current = ch;
            }
        });

        if (current !== active) {
            active = current;
            railItems.forEach((li, id) => li.classList.toggle('is-active', id === current.id));
            rail.classList.toggle('is-hero', current === chapters[0]);
        }

        const t = timeline.getBoundingClientRect();
        spine.style.transform = `scaleY(${clamp((vh * RECAP.spineLead - t.top) / t.height)})`;

        const c = challenges.getBoundingClientRect();
        challengesLight.style.setProperty('--light', clamp((vh - c.top) / (c.height + vh)).toFixed(3));

        const z = closing.getBoundingClientRect();
        closingLight.style.setProperty('--dawn', clamp((vh - z.top) / (z.height * .9)).toFixed(3));

        parallax.forEach(({el, box, factor}) => {
            const r = box.getBoundingClientRect();

            if (r.bottom < -200 || r.top > vh + 200) {
                return;
            }

            const offset = (r.top + r.height / 2 - vh / 2) * factor;
            el.style.transform = `translate3d(0, ${offset.toFixed(1)}px, 0)`;
        });
    };

    const request = () => {
        if (!queued) {
            queued = true;
            requestAnimationFrame(update);
        }
    };

    window.addEventListener('scroll', request, {passive: true});
    window.addEventListener('resize', request);
    update();
}

// ===== Album khoảnh khắc: nút trước/sau + kéo bằng chuột =====
function initAlbum() {
    const album = document.querySelector('.album');
    const prev = document.querySelector('[data-album="-1"]');
    const next = document.querySelector('[data-album="1"]');

    if (!album) {
        return;
    }

    const step = () => {
        const item = album.querySelector('.album-item');
        const gap = parseFloat(getComputedStyle(album).columnGap) || 0;

        return item ? item.offsetWidth + gap : album.clientWidth * .8;
    };

    const syncButtons = () => {
        prev.disabled = album.scrollLeft <= 4;
        next.disabled = album.scrollLeft >= album.scrollWidth - album.clientWidth - 4;
    };

    [prev, next].forEach((btn) => btn.addEventListener('click', () => {
        album.scrollBy({left: step() * Number(btn.dataset.album), behavior: reduced ? 'auto' : 'smooth'});
    }));

    album.addEventListener('scroll', syncButtons, {passive: true});
    window.addEventListener('resize', syncButtons);
    syncButtons();

    // Cảm ứng đã cuộn ngang tự nhiên; chỉ thêm kéo cho chuột
    let drag = null;

    album.addEventListener('pointerdown', (e) => {
        if (e.pointerType !== 'mouse' || e.button !== 0) {
            return;
        }

        drag = {x: e.clientX, left: album.scrollLeft, moved: false};
    });

    window.addEventListener('pointermove', (e) => {
        if (!drag) {
            return;
        }

        const dx = e.clientX - drag.x;

        if (!drag.moved && Math.abs(dx) > 4) {
            drag.moved = true;
            album.classList.add('is-drag');
        }

        if (drag.moved) {
            album.scrollLeft = drag.left - dx;
        }
    });

    window.addEventListener('pointerup', () => {
        if (!drag) {
            return;
        }

        const left = album.scrollLeft;
        drag = null;
        album.classList.remove('is-drag');
        // Bật lại snap rồi giữ nguyên vị trí để snap mượt tới ảnh gần nhất
        album.scrollLeft = left;
    });
}

initReveal();
initScroll();
initAlbum();
