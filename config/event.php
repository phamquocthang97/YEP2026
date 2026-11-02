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
    // Dòng mô tả dress code ở cuối section Chương trình (views/partials/schedule.php). Bảng màu đặt ở 'program' => 'dress_code_colors'.
    // Cả dòng mô tả lẫn bảng màu trống => ẩn block dress code.
    'dress_code'       => 'Trang phục dự tiệc',
    'invitation_image' => 'images/yep2026.png',
    // Hạn phản hồi hiển thị ở section Xác nhận tham gia (views/partials/rsvp.php). Để trống => ẩn câu nhắc hạn.
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
    // Lịch trình trống => ẩn đầu mục và danh sách; dress code (mô tả + bảng màu) trống => ẩn block dress code; cả hai trống => ẩn section; field nào trống => ẩn block đó.
    'program' => [
        // Ảnh tham khảo ở đầu khung Chương trình (đường dẫn trong public/). Để trống => bỏ ảnh, nội dung chiếm toàn bộ khung.
        'image'             => 'images/invite_bg.webp',
        'title'             => 'Lịch trình chương trình',
        'date_label'        => 'SAVE THE DATE',
        'dress_code_label'  => 'Dress Code',
        // Bảng màu dress code: mỗi phần tử là một mã màu hex (#rgb hoặc #rrggbb)
        'dress_code_colors' => ['#06190a', '#dbbd85', '#435c3e', '#f2e6cb'],
        // Ghi chú thêm dưới dress code (tuỳ chọn). Để trống => ẩn.
        'dress_code_note'   => '',
        // Mỗi phần tử là một mốc: giờ + tên hoạt động. NỘI DUNG MẪU: thay bằng chương trình thật trước khi gửi thiệp.
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
