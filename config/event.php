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
    'dress_code'       => 'Trang phục dự tiệc',
    'rsvp_deadline'    => '2026-12-01',
    'invite' => [
        'background'         => '',
        'background_desktop' => 'images/bg.png',
        'ticket_background'  => '',
        'logo'               => 'images/thk-logo.png',
        'theme_label'        => '[CHỦ ĐỀ]',
        'title_lines'        => ['YEAR END', 'PARTY'],
        // Tháng / thứ dạng chữ in hoa trên thiệp. Để trống => tự lấy từ 'date'
        'month_label'        => '',
        'weekday_label'      => '',
        'time_prefix'        => 'AT',
        'location_label'     => 'LOCATION',
    ],
    'program' => [
        'image'             => '',
        'title'             => 'Chương trình',
        'date_label'        => 'SAVE THE DATE',
        'dress_code_label'  => 'Dress code',
        'dress_code_colors' => ['#06190a', '#dbbd85', '#435c3e', '#f2e6cb'],
        'dress_code_note'   => '',
        'schedule'          => [
            ['time' => '18:00', 'title' => 'Đón khách'],
            ['time' => '18:30', 'title' => 'Khai mạc'],
            ['time' => '19:00', 'title' => 'Tiệc tối'],
            ['time' => '20:00', 'title' => 'Chương trình văn nghệ'],
            ['time' => '21:00', 'title' => 'Bốc thăm may mắn'],
            ['time' => '21:30', 'title' => 'Kết thúc chương trình'],
        ],
    ],
];
