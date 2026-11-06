<?php
return [
    'hero' => [
        'lead'  => 'Mỗi sự kiện là một câu chuyện, mỗi hoạt động là một mảnh ghép của hành trình. '
            . 'Cùng mở lại cuốn nhật ký, để một lần nữa sống trong những câu chuyện đã làm nên năm 2026.',
        'image' => '',
    ],
    
    'beginning' => [
        'intro'      => 'Những ngày làm việc cuối cùng trước kỳ nghỉ Tết cũng là lúc '
            . 'không khí lễ hội bắt đầu len lỏi khắp văn phòng. '
            . 'Những món quà được chuẩn bị, những lời chúc được trao đi, '
            . 'khép lại một năm bằng những khoảnh khắc thật gần gũi.',
        'date'       => 'Đầu năm 2026',
        'direction'  => 'Trước khi bước sang năm mới, ai cũng có vài điều muốn mang theo. '
            . 'Không phải chỉ tiêu hay con số, chỉ là những lời tự hứa giản dị, '
            . 'nhỏ thôi nhưng đủ để thấy năm mới đáng mong chờ.',
        'image'      => 'images/celebrations/tet/6.webp',
        'image_note' => 'Những ngày đầu năm 2026',
        'goals'      => [
            'Sức khỏe và bình an cho mọi người',
            'Thêm nhiều niềm vui bên đồng đội',
            'Cuối năm có thật nhiều chuyện để kể lại',
        ],
        'first_steps' => 'Và hành trình ấy bắt đầu bằng một cái Tết đến sớm ở văn phòng.',
    ],
    
    // Sự kiện đáng nhớ, gom theo chủ đề, mỗi sự kiện xuất hiện
    // Chỉnh vị trí, khung, góc nghiêng từng ảnh: config/recap-collage.php.
    'celebrations' => [
        'intro'  => 'Có những ngày đã được khoanh sẵn trên lịch: một cái Tết, một lời cảm ơn, '
            . 'một lần sinh nhật, một mùa trăng. Đến hẹn, công việc lùi lại nửa bước, '
            . 'để văn phòng được rộn ràng hơn thường lệ một chút.',
        'events' => [
            [
                'time'   => 'Tết cổ truyền',
                'title'  => 'Tết đến sớm',
                'story' => 'Quà được trao tận tay, lời chúc được gửi đi trước khi mọi người nghỉ Tết. '
                    . 'Năm mới còn chưa tới, nhưng hôm ấy, ai rời văn phòng cũng mang theo '
                    . 'một chút Tết về nhà, sớm hơn lịch vài ngày.',
                'photos' => [
                    ['src' => 'images/celebrations/tet/1.webp', 'note' => 'TET · ảnh 1'],
                    ['src' => 'images/celebrations/tet/2.webp', 'note' => 'TET · ảnh 2'],
                    ['src' => 'images/celebrations/tet/3.webp', 'note' => 'TET · ảnh 3'],
                    ['src' => 'images/celebrations/tet/4.webp', 'note' => 'TET · ảnh 4'],
                    ['src' => 'images/celebrations/tet/5.webp', 'note' => 'TET · ảnh 5'],
                ],
            ],
            [
                'time'   => '08/03',
                'title'  => 'Ngày của một nửa thế giới',
                'story' => 'Một lời cảm ơn gửi đến những người phụ nữ đang đồng hành cùng THK, '
                    . 'những người khiến mỗi ngày đi làm thêm phần dịu dàng. Chẳng cần điều gì '
                    . 'to tát, đôi khi chỉ một chút chu đáo cũng đủ làm một ngày bình thường '
                    . 'trở nên khác đi.',
                'photos' => [
                    ['src' => 'images/celebrations/0803/1.webp', 'note' => 'Women Day · ảnh 1'],
                    ['src' => 'images/celebrations/0803/3.webp', 'note' => 'Women Day · ảnh 2'],
                    ['src' => 'images/celebrations/0803/2.webp', 'note' => 'Women Day · ảnh 3'],
                ],
            ],
            [
                'time'   => '15/05',
                'title'  => 'Sinh nhật 2 tuổi THK HOLDINGS VIETNAM',
                'story' => 'Hai năm là dịp để nhìn lại chặng đường THK HOLDINGS VIETNAM '
                    . 'đã cùng nhau xây dựng. Một cột mốc nhỏ ghi dấu những nỗ lực đã qua '
                    . 'và mở ra những mục tiêu mới phía trước.',
                'photos' => [
                    ['src' => 'images/celebrations/birthday/1.webp', 'note' => 'birthday · ảnh 1'],
                    ['src' => 'images/celebrations/birthday/2.webp', 'note' => 'Manager Team'],
                ],
            ],
            [
                'time'   => '25/09',
                'title'  => 'Trung thu',
                'story' => 'Trung thu năm nay là dịp để mọi người thử sức với những hoạt động '
                    . 'khác ngày thường, từ làm bánh đến các trò chơi tập thể. '
                    . 'Một buổi gặp gỡ nhiều tiếng cười, nơi mọi người có thêm những trải nghiệm cùng nhau.',
                'photos' => [
                    ['src' => 'images/celebrations/trungthu/1.webp', 'note' => 'Trung thu · ảnh 1'],
                    ['src' => 'images/celebrations/trungthu/2.webp', 'note' => 'Trung thu · ảnh 2'],
                    ['src' => 'images/celebrations/trungthu/7.webp', 'note' => 'Trung thu · ảnh 3'],
                    ['src' => 'images/celebrations/trungthu/3.webp', 'note' => 'Trung thu · ảnh 4'],
                    ['src' => 'images/celebrations/trungthu/4.webp', 'note' => 'Trung thu · ảnh 5'],
                    ['src' => 'images/celebrations/trungthu/5.webp', 'note' => 'Trung thu · ảnh 6'],
                    ['src' => 'images/celebrations/trungthu/6.webp', 'note' => 'Trung thu · ảnh 7'],
                ],
            ],
        ],
    ],
    
    'trips' => [
        'intro'  => 'Không phải kỷ niệm nào cũng được tạo nên trong văn phòng. '
            . 'Có những câu chuyện bắt đầu từ lúc cả nhóm cùng lên xe, '
            . 'đặt công việc sang một bên và dành thời gian cho nhau.',
        'events' => [
            [
                'time'   => '21/03',
                'title'  => 'Mini trip',
                'story' => 'Bãi cỏ hôm ấy ngập tràn tiếng cười và những màn tranh tài đầy hào hứng. '
                    . 'Mỗi thử thách là một lần cả đội cùng phối hợp, cùng cố gắng và cổ vũ nhau. '
                    . 'Cúp chỉ có một, nhưng niềm vui hôm ấy thì chẳng đội nào phải nhường.',
                'photos' => [
                    ['src' => 'images/minitrips/1.webp', 'note' => 'Mini trip · ảnh 1'],
                    ['src' => 'images/minitrips/2.webp', 'note' => 'Mini trip · ảnh 2'],
                    ['src' => 'images/minitrips/3.webp', 'note' => 'Mini trip · ảnh 3'],
                    ['src' => 'images/minitrips/4.webp', 'note' => 'Mini trip · ảnh 4'],
                    ['src' => 'images/minitrips/5.webp', 'note' => 'Mini trip · ảnh 5'],
                ],
            ],
            [
                'time'   => '08–11/07',
                'title'  => 'Company trip',
                'story' => 'Bốn ngày đổi màn hình máy tính lấy biển xanh, đổi những cuộc họp '
                    . 'lấy tiếng sóng và những trải nghiệm mới mẻ. Từ những hoạt động ngoài trời '
                    . 'đến đêm gala rực rỡ, mỗi khoảnh khắc đều mang theo một câu chuyện riêng. '
                    . 'Chuyến đi khép lại, nhưng những tiếng cười và kỷ niệm trong bốn ngày ấy '
                    . 'sẽ còn được nhắc lại trong những lần gặp gỡ sau này.',
                'photos' => [
                    ['src' => 'images/trips/1.webp', 'note' => 'Company trip · ảnh 1'],
                    ['src' => 'images/trips/2.webp', 'note' => 'Company trip · ảnh 2'],
                    ['src' => 'images/trips/3.webp', 'note' => 'Company trip · ảnh 3'],
                    ['src' => 'images/trips/4.webp', 'note' => 'Company trip · ảnh 4'],
                    ['src' => 'images/trips/5.webp', 'note' => 'Company trip · ảnh 5'],
                    ['src' => 'images/trips/6.webp', 'note' => 'Company trip · ảnh 6'],
                    ['src' => 'images/trips/7.webp', 'note' => 'Company trip · ảnh 7'],
                    ['src' => 'images/trips/8.webp', 'note' => 'Company trip · ảnh 8'],
                ],
            ],
        ],
    ],
    
    'people' => [
        'intro' => 'Mỗi người một câu chuyện, mỗi team một sắc màu. '
            . 'Khi cùng đứng trong một khung hình, tất cả trở thành những mảnh ghép '
            . 'không thể thiếu của THK HOLDINGS VIETNAM.',
        'photos' => [
            ['src' => 'images/people/1.webp', 'note' => ''],
            ['src' => 'images/people/2.webp', 'note' => ''],
            ['src' => 'images/people/3.webp', 'note' => ''],
            ['src' => 'images/people/4.webp', 'note' => ''],
            ['src' => 'images/people/5.webp', 'note' => ''],
            ['src' => 'images/people/6.webp', 'note' => ''],
        ],
    ],
    
    // Ảnh lẻ không thuộc sự kiện nào.
    'moments' => [
        'intro'  => 'Có những tấm ảnh không thuộc về dịp nào: một nụ cười, một lần gặp gỡ, '
            . 'một phút đùa giữa giờ được ai đó tình cờ chụp lại. Nhỏ thôi, '
            . 'nhưng nhật ký nào cũng cần vài trang như thế để mỗi lần đọc lại đều thấy thân quen.',
        'photos' => [
            ['src' => 'images/moments/1.webp', 'note' => ''],
            ['src' => 'images/moments/2.webp', 'note' => ''],
            ['src' => 'images/moments/3.webp', 'note' => ''],
            ['src' => 'images/moments/4.webp', 'note' => ''],
            ['src' => 'images/moments/5.webp', 'note' => ''],
            ['src' => 'images/moments/6.webp', 'note' => ''],
            ['src' => 'images/moments/7.webp', 'note' => ''],
            ['src' => 'images/moments/8.webp', 'note' => ''],
            ['src' => 'images/moments/9.webp', 'note' => ''],
            ['src' => 'images/moments/10.webp', 'note' => ''],
        ],
    ],
    
    'closing' => [
        'looking_back' => 'Mỗi trang nhật ký khép lại là một câu chuyện được lưu giữ. '
            . 'Có niềm vui, có những cuộc gặp gỡ và trên hết là những con người '
            . 'đã cùng nhau làm nên năm 2026.',
        'thanks' => 'Cảm ơn vì đã có mặt, cùng sẻ chia và góp nên những khoảnh khắc ấy. '
            . 'Bởi một hành trình đáng nhớ chưa bao giờ là câu chuyện của riêng một người.',
        'next_year'    => 'Năm 2027 sẽ có những trang của riêng nó, với những câu chuyện '
            . 'mà bây giờ chưa ai đoán trước được. Hẹn gặp lại mọi người ở đó.',
        'message'      => 'Cảm ơn vì đã cùng THK HOLDINGS VIETNAM tạo nên năm 2026 đáng nhớ.',
    ],
];