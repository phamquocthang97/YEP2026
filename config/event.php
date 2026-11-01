<?php

// Thông tin sự kiện hiển thị trên thiệp và vé. Field tuỳ chọn để trống => ẩn khỏi giao diện.
return [
    'name'             => 'Year End Party',
    'date'             => '2026-12-18',
    'time'             => '18:00',
    'venue_name'       => 'ZEN PLAZA',
    'venue_address'    => '54+56 Nguyễn Trãi, Bến Thành, Hồ Chí Minh, Vietnam',
    'map_url'          => '',
    'monogram'         => '26',
    'dress_code'       => '',
    'invitation_image' => 'images/yep2026.png',
    // Hạn phản hồi hiển thị trên trang xác nhận tham dự (/rsvp.php). Để trống => ẩn câu nhắc hạn.
    'rsvp_deadline'    => '2026-12-01',
    // Ảnh nền mờ phía sau thiệp/vé trên PC (đường dẫn trong public/; ảnh hoặc video .mp4/.webm — video tự phát lặp, không tiếng). Để trống => nền tối mặc định.
    'desktop_background' => 'images/invite_poster.png',
    // Nội dung thiệp mời (views/invite.php). Ngày, tháng, thứ, năm, giờ, địa điểm lấy từ các field phía trên; field nào để trống => tự suy ra hoặc ẩn.
    'invite' => [
        // Ảnh nền thiệp: điện thoại (dọc) và PC (ngang). 'background_desktop' để trống => dùng ảnh điện thoại.
        'background'         => 'images/invite_bg.webp',
        'background_desktop' => 'images/bg_new1.png',
        // Nền phần vé trên điện thoại: nối tiếp phần đáy của 'background' để dải sáng chạy liền từ thiệp xuống vé. Để trống => nền tối mặc định.
        'ticket_background'  => 'images/tttt.png',
        'logo'               => 'images/thk-logo.png',
        'theme_label'        => 'CHỦ ĐỀ',
        // Mỗi phần tử là một dòng tiêu đề.
        'title_lines'        => ['YEAR END', 'PARTY'],
        // Tháng / thứ dạng chữ in hoa trên thiệp. Để trống => tự lấy từ 'date' (tiếng Anh, 3 chữ cái cho tháng).
        'month_label'        => '',
        'weekday_label'      => '',
        // Chữ đứng trước giờ, ví dụ "AT 18:00".
        'time_prefix'        => 'AT',
        'location_label'     => 'LOCATION',
    ],
];
