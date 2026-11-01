// ===== Cấu hình ảnh vé =====
// Ảnh vé vẽ lại bằng canvas theo bố cục vé mobile (khung 375×812, xuất 2x), không chụp màn hình website.
const TICKET_IMAGE = {
    width: 375,
    height: 812,
    scale: 2,
    ticketWidth: 311,
    images: {
        paper: 'images/bg_new1.png',
        flowerTop: 'images/hoatop.png',
        flowerBottom: 'images/hoabottom.png',
        logo: 'images/thk-logo.png',
    },
    colors: {
        // Cùng bảng màu public/css/theme.css (navy = màu chữ chính)
        bg: '#050505',
        surface: 'rgba(8, 7, 6, .62)',
        navy: '#fbf7ef',
        label: '#ffffff',
        highlight: '#c08e49',
        gold: '#c08c46',
        goldLine: 'rgba(192, 142, 73, .45)',
        // --color-heading-gold: champagne -> metallic -> bronze, từ trên xuống
        headingGold: ['#f2c670', '#c08c46', '#99612a'],
    },
    fonts: {
        sans: "'Be Vietnam Pro'",
        serif: "'Fraunces'",
        digits: "'Fraunces'",
        script: "'Pinyon Script'",
    },
};

const TOAST_MS = 2600;

const downloadButton = document.querySelector('[data-ticket-download]');
const toast = document.querySelector('.toast');

let toastTimer = null;

// ===== Vẽ ảnh vé =====
function loadImage(src) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = () => reject(new Error(`Không tải được ảnh ${src}`));
        img.src = src;
    });
}

function fontSpec(family, size, weight = 400, style = 'normal') {
    return `${style} ${weight} ${size}px ${family}`;
}

/**
 * Chiều cao dòng "normal" như CSS (ascent + descent của font).
 */
function lineHeightOf(ctx, font, size) {
    ctx.font = font;
    const m = ctx.measureText('Ấg');

    return m.fontBoundingBoxAscent !== undefined ? m.fontBoundingBoxAscent + m.fontBoundingBoxDescent : size * 1.25;
}

function textWidth(ctx, text, spacing) {
    let width = 0;

    for (const ch of text) {
        width += ctx.measureText(ch).width;
    }

    return width + spacing * Math.max(0, [...text].length - 1);
}

/**
 * Vẽ 1 dòng chữ, y là tâm dòng. Có letter-spacing thì vẽ từng ký tự.
 */
function drawText(ctx, text, x, y, {font, color, align = 'center', spacing = 0}) {
    ctx.font = font;
    ctx.fillStyle = color;
    ctx.textBaseline = 'middle';

    if (spacing === 0) {
        ctx.textAlign = align;
        ctx.fillText(text, x, y);
        return;
    }

    const width = textWidth(ctx, text, spacing);
    let cx = align === 'center' ? x - width / 2 : x;
    ctx.textAlign = 'left';

    for (const ch of text) {
        ctx.fillText(ch, cx, y);
        cx += ctx.measureText(ch).width + spacing;
    }
}

/**
 * Gradient vàng kim loại theo chiều cao dòng chữ như .heading-primary, y là tâm dòng.
 */
function headingGold(ctx, y, lineHeight) {
    const [light, mid, dark] = TICKET_IMAGE.colors.headingGold;
    const g = ctx.createLinearGradient(0, y - lineHeight / 2, 0, y + lineHeight / 2);
    g.addColorStop(0, light);
    g.addColorStop(.5, mid);
    g.addColorStop(1, dark);

    return g;
}

/**
 * Xuống dòng theo từ, cân đối độ dài các dòng như text-wrap: balance.
 */
function wrapBalanced(ctx, text, maxWidth, spacing = 0) {
    const words = text.split(/\s+/).filter(Boolean);
    const wrap = (limit) => {
        const lines = [];
        let line = '';

        for (const word of words) {
            const next = line ? `${line} ${word}` : word;

            if (line && textWidth(ctx, next, spacing) > limit) {
                lines.push(line);
                line = word;
            } else {
                line = next;
            }
        }

        return line ? [...lines, line] : lines;
    };

    const lines = wrap(maxWidth);

    if (lines.length < 2) {
        return lines;
    }

    let low = maxWidth / lines.length;
    let high = maxWidth;

    while (high - low > 1) {
        const mid = (low + high) / 2;

        if (wrap(mid).length > lines.length) {
            low = mid;
        } else {
            high = mid;
        }
    }

    return wrap(high);
}

function drawCover(ctx, img, width, height) {
    const scale = Math.max(width / img.naturalWidth, height / img.naturalHeight);
    const w = img.naturalWidth * scale;
    const h = img.naturalHeight * scale;
    ctx.drawImage(img, (width - w) / 2, (height - h) / 2, w, h);
}

// Lớp phủ tối như body::before trong theme.css
function drawOverlay(ctx, width, height) {
    const g = ctx.createLinearGradient(0, 0, 0, height);
    g.addColorStop(0, 'rgba(5, 5, 5, .6)');
    g.addColorStop(.45, 'rgba(5, 5, 5, .3)');
    g.addColorStop(1, 'rgba(5, 5, 5, .65)');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, width, height);
}

// Logo đỏ không đọc được trên nền tối => tô đơn sắc. Dùng source-in thay vì ctx.filter (Safari không hỗ trợ).
function tintImage(img, color) {
    const c = document.createElement('canvas');
    c.width = img.naturalWidth;
    c.height = img.naturalHeight;
    const x = c.getContext('2d');
    x.drawImage(img, 0, 0);
    x.globalCompositeOperation = 'source-in';
    x.fillStyle = color;
    x.fillRect(0, 0, c.width, c.height);

    return c;
}

// Khớp vé mobile trên web: bo góc vé = --radius-md (16px), khung bên trong bo đồng tâm = 16 - inset (tối thiểu 4px)
const TICKET_RADIUS = 16;

/**
 * Thân + cuống vé, khoét lỗ tròn r=10 ở đường xé.
 */
function ticketPath(ctx, x, y, w, bodyH, stubH) {
    const r = TICKET_RADIUS;
    const notch = 10;
    const joint = y + bodyH;
    const bottom = joint + stubH;

    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + r, r);
    ctx.lineTo(x + w, joint - notch);
    ctx.arc(x + w, joint, notch, -Math.PI / 2, Math.PI / 2, true);
    ctx.arcTo(x + w, bottom, x + w - r, bottom, r);
    ctx.arcTo(x, bottom, x, bottom - r, r);
    ctx.lineTo(x, joint + notch);
    ctx.arc(x, joint, notch, Math.PI / 2, -Math.PI / 2, true);
    ctx.arcTo(x, y, x + r, y, r);
    ctx.closePath();
}

function strokeFrame(ctx, x, y, w, h, color, inset) {
    ctx.strokeStyle = color;
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.roundRect(x + .5, y + .5, w - 1, h - 1, Math.max(4, TICKET_RADIUS - inset));
    ctx.stroke();
}

async function renderTicketImage(data) {
    const {width: W, height: H, scale, ticketWidth, colors, fonts} = TICKET_IMAGE;
    const f = {
        label: fontSpec(fonts.sans, 11, 500),
        eyebrow: fontSpec(fonts.sans, 10, 500),
        code: fontSpec(fonts.sans, 30, 600),
        script: fontSpec(fonts.script, 34),
        year: fontSpec(fonts.digits, 15, 600),
        name: fontSpec(fonts.serif, 30, 700),
        caption: fontSpec(fonts.serif, 17, 500),
        table: fontSpec(fonts.digits, 46, 600),
    };

    const sample = `${data.name}${data.code}VÉ THAM DỰ CÁ NHÂN KHÁCH MỜI SỐ BÀN Bàn của bạn`;
    const [images] = await Promise.all([
        Promise.all(Object.values(TICKET_IMAGE.images).map(loadImage)),
        ...Object.values(f).map((font) => document.fonts.load(font, sample)),
    ]);
    const [paper, flowerTop, flowerBottom, logo] = images;

    const canvas = document.createElement('canvas');
    canvas.width = W * scale;
    canvas.height = H * scale;

    const ctx = canvas.getContext('2d');
    ctx.scale(scale, scale);

    // Nền giấy, hoa 2 góc, logo
    drawCover(ctx, paper, W, H);
    drawOverlay(ctx, W, H);
    ctx.drawImage(flowerTop, -34, -30, 150, 150 * flowerTop.naturalHeight / flowerTop.naturalWidth);

    const flowerBottomH = 160 * flowerBottom.naturalHeight / flowerBottom.naturalWidth;
    ctx.drawImage(flowerBottom, W + 34 - 160, H + 34 - flowerBottomH, 160, flowerBottomH);
    ctx.drawImage(tintImage(logo, colors.navy), W - 22 - 26, 20, 26, 26 * logo.naturalHeight / logo.naturalWidth);

    // Đo bố cục thân vé
    const pad = 26;
    const contentW = ticketWidth - pad * 2;
    ctx.font = f.name;

    const nameSpacing = 30 * .05;
    const nameLines = wrapBalanced(ctx, data.name, contentW, nameSpacing);

    // Mã Lucky Draw dài thì thu nhỏ chữ để không tràn khung vé
    let codeSize = 30;

    while (codeSize > 18) {
        ctx.font = fontSpec(fonts.sans, codeSize, 600);

        if (textWidth(ctx, data.code, codeSize * .16) <= contentW) {
            break;
        }

        codeSize -= 1;
    }

    const lh = {
        label: lineHeightOf(ctx, f.label, 11),
        eyebrow: lineHeightOf(ctx, f.eyebrow, 10),
        script: 34 * 1.15,
        year: lineHeightOf(ctx, f.year, 15),
        name: 30 * 1.15,
        code: 30 * 1.2,
        caption: 17 * 1.3,
    };
    const bodyH = 26 + lh.script + lh.year
        + 18 + lh.eyebrow + 4 + lh.name * nameLines.length
        + 18 + 1
        + 16 + lh.eyebrow + 4 + lh.code
        + 22;
    const badge = 94;
    const stubH = 22 + badge + 26;
    const contentH = lh.label + 16 + bodyH + stubH;

    // Nội dung căn giữa theo chiều dọc trong vùng padding 48 / 40
    const top = 48 + (H - 88 - contentH) / 2;
    const cx = W / 2;
    const x0 = (W - ticketWidth) / 2;

    drawText(ctx, 'VÉ THAM DỰ CÁ NHÂN', cx, top + lh.label / 2, {font: f.label, color: colors.label, spacing: 11 * .24});

    const y0 = top + lh.label + 16;
    const joint = y0 + bodyH;

    ctx.save();
    ctx.shadowColor = 'rgba(0, 0, 0, .5)';
    ctx.shadowBlur = 24;
    ctx.shadowOffsetY = 10;
    ctx.fillStyle = colors.surface;
    ticketPath(ctx, x0, y0, ticketWidth, bodyH, stubH);
    ctx.fill();
    ctx.restore();

    // Khung gold đôi ở thân vé, khung đơn ở cuống
    strokeFrame(ctx, x0 + 10, y0 + 10, ticketWidth - 20, bodyH - 20, colors.gold, 10);
    strokeFrame(ctx, x0 + 14, y0 + 14, ticketWidth - 28, bodyH - 28, colors.goldLine, 14);
    strokeFrame(ctx, x0 + 10, joint + 10, ticketWidth - 20, stubH - 20, colors.gold, 10);

    // Đường răng cưa
    ctx.fillStyle = colors.gold;

    for (let x = x0 + 16 + 4.5; x < x0 + ticketWidth - 16; x += 9) {
        ctx.beginPath();
        ctx.arc(x, joint, 1.4, 0, Math.PI * 2);
        ctx.fill();
    }

    // Thân vé
    let y = y0 + 26;
    drawText(ctx, data.script, cx, y + lh.script / 2, {font: f.script, color: headingGold(ctx, y + lh.script / 2, lh.script)});
    y += lh.script;
    drawText(ctx, data.year, cx, y + lh.year / 2, {font: f.year, color: headingGold(ctx, y + lh.year / 2, lh.year), spacing: 15 * .5});
    y += lh.year + 18;
    drawText(ctx, 'KHÁCH MỜI', cx, y + lh.eyebrow / 2, {font: f.eyebrow, color: colors.label, spacing: 10 * .26});
    y += lh.eyebrow + 4;

    for (const line of nameLines) {
        drawText(ctx, line, cx, y + lh.name / 2, {font: f.name, color: headingGold(ctx, y + lh.name / 2, lh.name), spacing: nameSpacing});
        y += lh.name;
    }

    y += 18;
    ctx.fillStyle = colors.gold;
    ctx.fillRect(cx - 24, y, 48, 1);
    y += 1 + 16;
    drawText(ctx, 'LUCKY DRAW CODE', cx, y + lh.eyebrow / 2, {font: f.eyebrow, color: colors.label, spacing: 10 * .26});
    y += lh.eyebrow + 4;
    drawText(ctx, data.code, cx, y + lh.code / 2, {font: fontSpec(fonts.sans, codeSize, 600), color: colors.highlight, spacing: codeSize * .16});
    // Cuống vé: nhãn + chú thích bên trái, badge số bàn bên phải
    ctx.font = f.eyebrow;
    const labelW = textWidth(ctx, 'SỐ BÀN', 10 * .26);
    ctx.font = f.caption;
    const captionW = ctx.measureText('Bàn của bạn').width;
    const textW = Math.max(labelW, captionW);
    const rowX = cx - (textW + 20 + badge) / 2;
    const rowY = joint + 22 + badge / 2;
    const textH = lh.eyebrow + 4 + lh.caption;

    drawText(ctx, 'SỐ BÀN', rowX, rowY - textH / 2 + lh.eyebrow / 2, {font: f.eyebrow, color: colors.label, align: 'left', spacing: 10 * .26});
    drawText(ctx, 'Bàn của bạn', rowX, rowY + textH / 2 - lh.caption / 2, {font: f.caption, color: colors.navy, align: 'left'});

    const bx = rowX + textW + 20 + badge / 2;
    ctx.beginPath();
    ctx.arc(bx, rowY, badge / 2 - .5, 0, Math.PI * 2);
    ctx.fillStyle = colors.bg;
    ctx.fill();
    ctx.lineWidth = 1;
    ctx.strokeStyle = colors.gold;
    ctx.stroke();
    ctx.beginPath();
    ctx.arc(bx, rowY, badge / 2 - 3, 0, Math.PI * 2);
    ctx.lineWidth = 4;
    ctx.strokeStyle = colors.surface;
    ctx.stroke();
    ctx.beginPath();
    ctx.arc(bx, rowY, badge / 2 - 5.5, 0, Math.PI * 2);
    ctx.lineWidth = 1;
    ctx.strokeStyle = colors.goldLine;
    ctx.stroke();
    drawText(ctx, data.table, bx, rowY, {font: f.table, color: colors.highlight});

    return canvas;
}

// ===== Tải vé / hướng dẫn lưu ảnh =====
function ticketFileName(code) {
    return `${code.replace(/[^\w.-]+/g, '-')}.png`;
}

function downloadCanvas(canvas, fileName) {
    return new Promise((resolve, reject) => {
        canvas.toBlob((blob) => {
            if (!blob) {
                reject(new Error('Không tạo được file PNG'));
                return;
            }

            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = fileName;
            document.body.append(link);
            link.click();
            link.remove();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
            resolve();
        }, 'image/png');
    });
}

/**
 * type: success | danger
 */
function showToast(type, html) {
    clearTimeout(toastTimer);
    toast.classList.toggle('is-danger', type === 'danger');
    toast.querySelector('.toast-text').innerHTML = html;
    toast.hidden = false;
    toastTimer = setTimeout(() => {
        toast.hidden = true;
    }, TOAST_MS);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function onDownloadClick() {
    const data = downloadButton.dataset;

    // Không dùng disabled để nút không đổi sang kiểu "chưa tải được" trong lúc vẽ ảnh
    if (downloadButton.getAttribute('aria-busy') === 'true') {
        return;
    }

    downloadButton.setAttribute('aria-busy', 'true');

    try {
        const canvas = await renderTicketImage(data);

        const fileName = ticketFileName(data.code);
        await downloadCanvas(canvas, fileName);
        showToast('success', `Đã tải ảnh vé <span class="toast-file tnum">${escapeHtml(fileName)}</span>`);
    } catch (error) {
        console.error(error);
        showToast('danger', 'Chưa tạo được ảnh vé, vui lòng thử lại.');
    } finally {
        downloadButton.removeAttribute('aria-busy');
    }
}

// ===== Khởi tạo =====
downloadButton?.addEventListener('click', onDownloadClick);
