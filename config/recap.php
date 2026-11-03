<?php
return [
    'hero' => [
        'lead'  => 'Mười hai tháng, những ngày hội và vô số khoảnh khắc. Hãy cùng '
            . 'nhau mở lại cuốn nhật ký của năm vừa qua.',
        'image' => '',
    ],

    'beginning' => [
        'intro'      => 'Mọi hành trình đều bắt đầu từ một khoảnh khắc. Và năm vừa qua, '
            . 'chúng ta bắt đầu bằng những lời chúc, những mong ước và niềm háo '
            . 'hức của ngày đầu năm.',
        'date'       => 'Đầu năm 2026',
        'direction'  => 'Năm mới mở ra bằng những phong bao đỏ, những cái bắt tay và rất '
            . 'nhiều tiếng cười.',
        'image'      => '',
        'image_note' => 'Những ngày đầu năm 2026',
        'goals'      => [
            'Sức khoẻ và bình an cho mọi người',
            'Thêm thật nhiều nụ cười bên đồng đội',
            'Cùng nhau tạo nên những kỷ niệm đẹp',
        ],
        'first_steps' => 'Và năm mới bắt đầu bằng ngày hội đầu tiên: Tết sum vầy.',
    ],

    // Sự kiện đáng nhớ, gom theo chủ đề, mỗi sự kiện xuất hiện
    // Chỉnh vị trí, khung, góc nghiêng từng ảnh: config/recap-collage.php.
    'celebrations' => [
        'intro'  => 'Có những ngày cả công ty cùng dừng lại: để đón một mùa xuân mới, '
            . 'để gửi một lời chúc, hay để cùng thổi nến mừng sinh nhật.',
        'events' => [
            [
                'time'   => 'Tết Bính Ngọ',
                'title'  => 'Tết sum vầy',
                'story'  => 'Những ngày đầu xuân rộn ràng, cùng nhau đón năm mới bằng '
                    . 'lời chúc, phong bao đỏ và thật nhiều tiếng cười.',
                'photos' => [
                    ['src' => '', 'note' => 'Tết sum vầy · ảnh 1'],
                    ['src' => '', 'note' => 'Tết sum vầy · ảnh 2'],
                    ['src' => '', 'note' => 'Tết sum vầy · ảnh 3'],
                    ['src' => '', 'note' => 'Tết sum vầy · ảnh 4'],
                    ['src' => '', 'note' => 'Tết sum vầy · ảnh 5'],
                ],
            ],
            [
                'time'   => '08/03',
                'title'  => 'Ngày của một nửa thế giới',
                'story'  => 'Một ngày dành riêng để gửi lời cảm ơn tới những người '
                    . 'phụ nữ đã làm cho mỗi ngày đi làm thêm dịu dàng.',
                'photos' => [
                    ['src' => '', 'note' => 'Ngày của một nửa thế giới · ảnh 1'],
                    ['src' => '', 'note' => 'Ngày của một nửa thế giới · ảnh 2'],
                    ['src' => '', 'note' => 'Ngày của một nửa thế giới · ảnh 3'],
                ],
            ],
            [
                'time'   => '[Cần bổ sung: thời gian]',
                'title'  => 'Tiệc sinh nhật',
                'story'  => 'Thổi nến, cắt bánh và những lời chúc dành cho nhau.',
                'photos' => [
                    ['src' => '', 'note' => 'Tiệc sinh nhật · ảnh 1'],
                ],
            ],
        ],
    ],

    'trips' => [
        'intro'  => 'Có những ngày chúng ta rời xa bàn làm việc, cùng nhau lên đường '
            . 'và mang về thật nhiều kỷ niệm.',
        'events' => [
            [
                'time'   => '21/03',
                'title'  => 'Team building',
                'story'  => 'Một ngày ngoài trời: cùng chạy, cùng hô, cùng thắng và '
                    . 'cùng cười đến khàn giọng.',
                'photos' => [
                    ['src' => '', 'note' => 'Team building · ảnh 1'],
                    ['src' => '', 'note' => 'Team building · ảnh 2'],
                    ['src' => '', 'note' => 'Team building · ảnh 3'],
                    ['src' => '', 'note' => 'Team building · ảnh 4'],
                    ['src' => '', 'note' => 'Team building · ảnh 5'],
                ],
            ],
            [
                'time'   => '[Cần bổ sung: thời gian]',
                'title'  => 'Company trip',
                'story'  => 'Cùng nhau xách balo lên đường, để lại phía sau những '
                    . 'ngày bận rộn và mang về thật nhiều kỷ niệm.',
                'photos' => [
                    ['src' => '', 'note' => 'Company trip · ảnh 1'],
                    ['src' => '', 'note' => 'Company trip · ảnh 2'],
                    ['src' => '', 'note' => 'Company trip · ảnh 3'],
                    ['src' => '', 'note' => 'Company trip · ảnh 4'],
                    ['src' => '', 'note' => 'Company trip · ảnh 5'],
                    ['src' => '', 'note' => 'Company trip · ảnh 6'],
                    ['src' => '', 'note' => 'Company trip · ảnh 7'],
                ],
            ],
        ],
    ],

    'people' => [
        'intro'  => 'Mỗi khoảnh khắc đáng nhớ đều có những con người cùng tạo nên. '
            . 'Đây là gương mặt và nụ cười của chúng ta.',
        'photos' => [
            ['src' => '', 'note' => 'Ảnh tập thể toàn công ty'],
            ['src' => '', 'note' => 'Cùng nhau chụp ảnh'],
            ['src' => '', 'note' => 'Nụ cười của đồng đội'],
            ['src' => '', 'note' => 'Những cái ôm'],
            ['src' => '', 'note' => 'Khoảnh khắc đồng hành'],
        ],
    ],

    // Ảnh lẻ không thuộc sự kiện nào.
    'moments' => [
        'intro'  => 'Những khoảnh khắc nhỏ làm nên một năm không thể quên.',
        'photos' => [
            ['src' => '', 'note' => 'Giờ nghỉ trưa'],
            ['src' => '', 'note' => 'Góc nhỏ quen thuộc'],
            ['src' => '', 'note' => 'Hậu trường ngày hội'],
            ['src' => '', 'note' => 'Khoảnh khắc đời thường'],
            ['src' => '', 'note' => 'Những bữa ăn chung'],
            ['src' => '', 'note' => 'Cùng nhau vui chơi'],
            ['src' => '', 'note' => 'Khoảnh khắc đáng nhớ'],
        ],
    ],

    'closing' => [
        'looking_back' => 'Nhìn lại năm 2026, điều đọng lại không phải những con số mà là '
            . 'tiếng cười, những cái ôm và những ngày chúng ta có nhau.',
        'thanks'       => 'Cảm ơn mỗi người đã góp một phần vào những khoảnh khắc ấy.',
        'signature'    => 'Ban lãnh đạo',
        'next_year'    => 'Năm tới, hãy cùng nhau tạo thêm thật nhiều kỷ niệm đáng nhớ.',
        'message'      => 'Một hành trình khép lại, nhưng những điều chúng ta cùng nhau tạo '
            . 'nên sẽ còn tiếp tục. Cảm ơn vì đã là một phần của câu chuyện '
            . 'này.',
    ],
];
