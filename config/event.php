<?php

// Thông tin sự kiện hiển thị trên thiệp và vé. Field tuỳ chọn để trống => ẩn khỏi giao diện.
return [
    'company_name'     => 'THK Holdings Vietnam',
    'company_logo'     => 'images/thk-logo.png',
    'name'             => 'Year End Party',
    'date'             => '2026-12-18',
    'time'             => '18:00',
    'venue_name'       => 'ZEN PLAZA',
    'venue_address'    => '54+56 Nguyễn Trãi, Bến Thành, Hồ Chí Minh, Vietnam',
    'map_url'          => '',
    'monogram'         => '26',
    'dress_code'       => 'Party Attire',
    'rsvp_deadline'    => '2026-12-01',
    'invite' => [
        // Ảnh nền riêng cho thiệp/vé. Để trống => dùng nền chung của cả landing
        // (public/css/theme.css: --page-bg-image), giữ một khung cảnh xuyên suốt.
        'background'         => '',
        'background_desktop' => 'images/4.mp4',
        'ticket_background'  => '',
        'logo'               => 'images/thk-logo.png',
        'theme_label'        => 'HCMC',
        'title_lines'        => ['Year End', 'Party'],
        // Tháng / thứ dạng chữ in hoa trên thiệp. Để trống => tự lấy từ 'date'
        'month_label'        => '',
        'weekday_label'      => '',
        'time_prefix'        => 'AT',
        'location_label'     => '',
    ],
    // Section cuối trang
    // Ảnh trống => chỉ còn nền chung; lời chào và lời cảm ơn đều trống => ẩn cả section.
    'closing' => [
        'image' => 'images/all.webp',
        'lead'  => 'See you on a night to remember',
        'title' => 'With Sincere Thanks',
        'note'  => '',
        'copyright' => '© 2026 THK Holdings Vietnam HCMC. All rights reserved.',
        'credit'    => 'Designed & Developed by Pham Quoc Thang (タン)',
    ],
    'program' => [
        'image'             => '',
        'title'             => 'Event Schedule',
        'date_label'        => 'SAVE THE DATE',
        'dress_code_label'  => 'Dress Code',
        'dress_code_colors' => ['#D2042D', '#FFD700', '#FFFFFF', '#171717'],
        'dress_code_note'   => '',
        'schedule'          => [
            ['time' => '18:00', 'title' => 'Guest Arrival'],
            ['time' => '18:30', 'title' => 'Opening Ceremony'],
            ['time' => '19:00', 'title' => 'Dinner'],
            ['time' => '20:00', 'title' => 'Entertainment Program'],
            ['time' => '21:00', 'title' => 'Lucky Draw'],
            ['time' => '21:30', 'title' => 'Event Closing'],
        ],
    ],
];
