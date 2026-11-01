<?php

// Nội dung landing page "A Year to Remember" (/recap.php).
// Chuỗi bắt đầu bằng "[" là placeholder => hiển thị kiểu "cần bổ sung". Thay bằng nội dung thật, không tự đặt số liệu/sự kiện.
// 'image' / 'src': đường dẫn trong public/ (vd. images/recap/q1.jpg). Để trống => hiện khung ảnh chờ.
return [
    'hero' => [
        'lead'  => 'Mười hai tháng, hàng trăm ngày làm việc và vô số khoảnh khắc. Hãy cùng nhau mở lại cuốn nhật ký của năm vừa qua.',
        'image' => '',
    ],

    'beginning' => [
        'intro'      => 'Mọi hành trình đều bắt đầu từ một mục tiêu. Và năm vừa qua, chúng ta đã bắt đầu với những kỳ vọng, quyết tâm và khát vọng riêng.',
        'date'       => '[Cần bổ sung: ngày khởi động năm, vd. buổi kick-off]',
        'direction'  => '[Cần bổ sung: định hướng chung của công ty trong năm, 1–2 câu]',
        'image'      => '',
        'image_note' => '[Ảnh buổi kick-off / họp đầu năm]',
        'goals'      => [
            '[Cần bổ sung: mục tiêu / kế hoạch 1]',
            '[Cần bổ sung: mục tiêu / kế hoạch 2]',
            '[Cần bổ sung: mục tiêu / kế hoạch 3]',
        ],
        'first_steps' => '[Cần bổ sung: những dự án / bước đi đầu tiên được khởi động]',
    ],

    // layout: frame | pair | band. Để trống => tự xoay vòng để tạo nhịp.
    'milestones' => [
        [
            'time'   => '[Mốc 1: tháng / quý]',
            'title'  => '[Cần bổ sung: sự kiện hoặc hoạt động nổi bật]',
            'story'  => '[Cần bổ sung: câu chuyện ngắn phía sau cột mốc, 2–3 câu]',
            'result' => '[Cần bổ sung: kết quả đạt được, nếu có]',
            'images' => [['src' => '', 'note' => '[Ảnh thực tế mốc 1]']],
        ],
        [
            'time'   => '[Mốc 2: tháng / quý]',
            'title'  => '[Cần bổ sung: sự kiện hoặc hoạt động nổi bật]',
            'story'  => '[Cần bổ sung: câu chuyện ngắn phía sau cột mốc, 2–3 câu]',
            'result' => '',
            'images' => [['src' => '', 'note' => '[Ảnh mốc 2 – 1]'], ['src' => '', 'note' => '[Ảnh mốc 2 – 2]']],
        ],
        [
            'time'   => '[Mốc 3: tháng / quý]',
            'title'  => '[Cần bổ sung: sự kiện hoặc hoạt động nổi bật]',
            'story'  => '[Cần bổ sung: câu chuyện ngắn phía sau cột mốc, 2–3 câu]',
            'result' => '[Cần bổ sung: kết quả đạt được, nếu có]',
            'images' => [['src' => '', 'note' => '[Ảnh toàn cảnh mốc 3]']],
        ],
        [
            'time'   => '[Mốc 4: tháng / quý]',
            'title'  => '[Cần bổ sung: sự kiện hoặc hoạt động nổi bật]',
            'story'  => '[Cần bổ sung: câu chuyện ngắn phía sau cột mốc, 2–3 câu]',
            'result' => '[Cần bổ sung: kết quả đạt được, nếu có]',
            'images' => [['src' => '', 'note' => '[Ảnh thực tế mốc 4]']],
        ],
    ],

    'challenges' => [
        'intro' => 'Không phải mọi chặng đường đều dễ dàng. Nhưng chính những thử thách đã giúp chúng ta trưởng thành hơn và hiểu rõ giá trị của sự đồng hành.',
        'items' => [
            ['hard' => '[Cần bổ sung: thử thách / thay đổi 1]', 'overcome' => '[Cần bổ sung: cách tập thể đã vượt qua]'],
            ['hard' => '[Cần bổ sung: thử thách / thay đổi 2]', 'overcome' => '[Cần bổ sung: cách tập thể đã vượt qua]'],
            ['hard' => '[Cần bổ sung: thử thách / thay đổi 3]', 'overcome' => '[Cần bổ sung: cách tập thể đã vượt qua]'],
        ],
        'turn' => 'Và rồi, từ những ngày khó khăn ấy, thành quả bắt đầu thành hình.',
    ],

    // value: số nguyên/thập phân (đếm động khi cuộn tới). null => hiện khung chờ số liệu.
    'achievements' => [
        'stats' => [
            ['value' => null, 'suffix' => '', 'label' => '[Cần bổ sung: tên chỉ số]', 'context' => '[Cần bổ sung: con số này có ý nghĩa gì, đến từ đâu]'],
            ['value' => null, 'suffix' => '', 'label' => '[Cần bổ sung: tên chỉ số]', 'context' => '[Cần bổ sung: bối cảnh của con số]'],
            ['value' => null, 'suffix' => '', 'label' => '[Cần bổ sung: tên chỉ số]', 'context' => '[Cần bổ sung: bối cảnh của con số]'],
        ],
        'highlights' => [
            ['kind' => '[Dự án]', 'title' => '[Cần bổ sung: tên dự án quan trọng]', 'note' => '[Cần bổ sung: mô tả ngắn]'],
            ['kind' => '[Giải thưởng]', 'title' => '[Cần bổ sung: giải thưởng / thành tích]', 'note' => '[Cần bổ sung: mô tả ngắn]'],
            ['kind' => '[Đội ngũ]', 'title' => '[Cần bổ sung: sự phát triển của đội ngũ]', 'note' => '[Cần bổ sung: mô tả ngắn]'],
        ],
    ],

    'people' => [
        'intro'  => 'Phía sau mỗi thành quả là những con người đã cùng nhau nỗ lực. Chính họ đã tạo nên câu chuyện của năm vừa qua.',
        'photos' => [
            ['src' => '', 'note' => '[Ảnh tập thể toàn công ty]'],
            ['src' => '', 'note' => '[Ảnh team đang làm việc]'],
            ['src' => '', 'note' => '[Ảnh chân dung / nụ cười]'],
            ['src' => '', 'note' => '[Ảnh họp nhóm]'],
            ['src' => '', 'note' => '[Ảnh hoạt động tập thể]'],
            ['src' => '', 'note' => '[Ảnh sự kiện nội bộ]'],
            ['src' => '', 'note' => '[Ảnh khoảnh khắc đồng hành]'],
        ],
    ],

    'moments' => [
        'intro'  => '[Cần bổ sung: một câu mở đầu cho album kỷ niệm, nếu muốn]',
        'photos' => [
            ['src' => '', 'note' => '[Team building]'],
            ['src' => '', 'note' => '[Sự kiện nội bộ]'],
            ['src' => '', 'note' => '[Hậu trường]'],
            ['src' => '', 'note' => '[Khoảnh khắc đời thường]'],
            ['src' => '', 'note' => '[Sinh nhật / kỷ niệm]'],
            ['src' => '', 'note' => '[Hoạt động công ty]'],
            ['src' => '', 'note' => '[Khoảnh khắc đáng nhớ]'],
        ],
    ],

    'closing' => [
        'looking_back' => '[Cần bổ sung: nhìn lại những gì đã đạt được, 1–2 câu]',
        'thanks'       => '[Cần bổ sung: lời cảm ơn từ Ban lãnh đạo đến toàn thể nhân viên]',
        'signature'    => '[Cần bổ sung: người gửi lời cảm ơn]',
        'next_year'    => '[Cần bổ sung: thông điệp / định hướng cho năm tiếp theo]',
        'message'      => 'Một hành trình khép lại, nhưng những điều chúng ta cùng nhau tạo nên sẽ còn tiếp tục. Cảm ơn vì đã là một phần của câu chuyện này.',
    ],
];
