<?php
return [
    'hero' => [
        'lead'  => 'Every event tells a story, and every activity is a piece of the journey. '
            . 'Let us open the diary once more and relive the stories that made 2026.',
        'image' => '',
    ],
    
    'beginning' => [
        'intro'      => 'The last working days before the Tet holiday were also when '
            . 'the festive spirit began to fill the office. '
            . 'Gifts were prepared and warm wishes exchanged, '
            . 'closing the year with truly heartfelt moments.',
        'date'       => 'Early 2026',
        'direction'  => 'Before stepping into the new year, everyone had a few things they wished to carry along. '
            . 'Not targets or figures, just simple promises to ourselves, '
            . 'small yet enough to make the new year worth looking forward to.',
        'image'      => 'images/celebrations/tet/6.webp',
        'image_note' => 'The first days of 2026',
        'goals'      => [
            'Health and peace for everyone',
            'More joyful moments with our teammates',
            'Plenty of stories to share by the end of the year',
        ],
        'first_steps' => 'And so the journey began with an early Tet celebration at the office.',
    ],
    
    // Sự kiện đáng nhớ, gom theo chủ đề, mỗi sự kiện xuất hiện
    // Chỉnh vị trí, khung, góc nghiêng từng ảnh: config/recap-collage.php.
    'celebrations' => [
        'intro'  => 'Some days were circled on the calendar well in advance: a Tet celebration, a word of thanks, '
            . 'a birthday, a Mid-Autumn moon. When they arrived, work took half a step back, '
            . 'letting the office come alive a little more than usual.',
        'events' => [
            [
                'time'   => 'Lunar New Year',
                'title'  => 'An Early Tet',
                'story' => 'Gifts were handed out in person and wishes shared before everyone left for the Tet holiday. '
                    . 'The new year had not yet arrived, but that day, everyone who left the office took '
                    . 'a little bit of Tet home, a few days ahead of the calendar.',
                'photos' => [
                    ['src' => 'images/celebrations/tet/1.webp', 'note' => 'TET · Photo 1'],
                    ['src' => 'images/celebrations/tet/2.webp', 'note' => 'TET · Photo 2'],
                    ['src' => 'images/celebrations/tet/3.webp', 'note' => 'TET · Photo 3'],
                    ['src' => 'images/celebrations/tet/4.webp', 'note' => 'TET · Photo 4'],
                    ['src' => 'images/celebrations/tet/5.webp', 'note' => 'TET · Photo 5'],
                ],
            ],
            [
                'time'   => 'March 8',
                'title'  => 'International Women\'s Day',
                'story' => 'A word of thanks to the women who walk alongside THK, '
                    . 'making every workday a little gentler. It takes nothing '
                    . 'grand; sometimes a small gesture of care is enough to make an ordinary day '
                    . 'feel different.',
                'photos' => [
                    ['src' => 'images/celebrations/0803/1.webp', 'note' => 'Women\'s Day · Photo 1'],
                    ['src' => 'images/celebrations/0803/3.webp', 'note' => 'Women\'s Day · Photo 2'],
                    ['src' => 'images/celebrations/0803/2.webp', 'note' => 'Women\'s Day · Photo 3'],
                ],
            ],
            [
                'time'   => 'May 15',
                'title'  => 'THK HOLDINGS VIETNAM\'s 2nd Anniversary',
                'story' => 'Two years offered a chance to look back on the path THK HOLDINGS VIETNAM '
                    . 'has built together. A small milestone that honors the efforts behind us '
                    . 'and opens the way to new goals ahead.',
                'photos' => [
                    ['src' => 'images/celebrations/birthday/1.webp', 'note' => 'Anniversary · Photo 1'],
                    ['src' => 'images/celebrations/birthday/2.webp', 'note' => 'Manager Team'],
                ],
            ],
            [
                'time'   => 'September 25',
                'title'  => 'Mid-Autumn Festival',
                'story' => 'This year\'s Mid-Autumn Festival gave everyone a chance to try activities '
                    . 'beyond the everyday, from cake making to team games. '
                    . 'A gathering full of laughter, where everyone shared new experiences together.',
                'photos' => [
                    ['src' => 'images/celebrations/trungthu/1.webp', 'note' => 'Mid-Autumn · Photo 1'],
                    ['src' => 'images/celebrations/trungthu/2.webp', 'note' => 'Mid-Autumn · Photo 2'],
                    ['src' => 'images/celebrations/trungthu/7.webp', 'note' => 'Mid-Autumn · Photo 3'],
                    ['src' => 'images/celebrations/trungthu/3.webp', 'note' => 'Mid-Autumn · Photo 4'],
                    ['src' => 'images/celebrations/trungthu/4.webp', 'note' => 'Mid-Autumn · Photo 5'],
                    ['src' => 'images/celebrations/trungthu/5.webp', 'note' => 'Mid-Autumn · Photo 6'],
                    ['src' => 'images/celebrations/trungthu/6.webp', 'note' => 'Mid-Autumn · Photo 7'],
                ],
            ],
        ],
    ],
    
    'trips' => [
        'intro'  => 'Not every memory is made in the office. '
            . 'Some stories begin the moment the whole team sets off together, '
            . 'putting work aside and making time for one another.',
        'events' => [
            [
                'time'   => 'March 21',
                'title'  => 'Mini Trip',
                'story' => 'That day, the lawn was filled with laughter and spirited competition. '
                    . 'Every challenge brought each team together to coordinate, give their best, and cheer one another on. '
                    . 'There was only one trophy, but no team had to give up the joy of that day.',
                'photos' => [
                    ['src' => 'images/minitrips/1.webp', 'note' => 'Mini Trip · Photo 1'],
                    ['src' => 'images/minitrips/2.webp', 'note' => 'Mini Trip · Photo 2'],
                    ['src' => 'images/minitrips/3.webp', 'note' => 'Mini Trip · Photo 3'],
                    ['src' => 'images/minitrips/4.webp', 'note' => 'Mini Trip · Photo 4'],
                    ['src' => 'images/minitrips/5.webp', 'note' => 'Mini Trip · Photo 5'],
                ],
            ],
            [
                'time'   => 'July 8–11',
                'title'  => 'Company Trip',
                'story' => 'Four days of trading computer screens for blue seas, and meetings '
                    . 'for the sound of waves and new experiences. From outdoor activities '
                    . 'to a dazzling gala night, every moment carried a story of its own. '
                    . 'The trip has come to an end, but the laughter and memories of those four days '
                    . 'will be shared again whenever we meet.',
                'photos' => [
                    ['src' => 'images/trips/1.webp', 'note' => 'Company Trip · Photo 1'],
                    ['src' => 'images/trips/2.webp', 'note' => 'Company Trip · Photo 2'],
                    ['src' => 'images/trips/3.webp', 'note' => 'Company Trip · Photo 3'],
                    ['src' => 'images/trips/4.webp', 'note' => 'Company Trip · Photo 4'],
                    ['src' => 'images/trips/5.webp', 'note' => 'Company Trip · Photo 5'],
                    ['src' => 'images/trips/6.webp', 'note' => 'Company Trip · Photo 6'],
                    ['src' => 'images/trips/7.webp', 'note' => 'Company Trip · Photo 7'],
                    ['src' => 'images/trips/8.webp', 'note' => 'Company Trip · Photo 8'],
                ],
            ],
        ],
    ],
    
    'people' => [
        'intro' => 'Each person has a story, each team its own color. '
            . 'Standing together in one frame, everyone becomes an essential piece '
            . 'of THK HOLDINGS VIETNAM.',
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
        'intro'  => 'Some photos belong to no particular occasion: a smile, an encounter, '
            . 'a lighthearted moment during a break, captured by chance. Small as they are, '
            . 'every diary needs a few pages like these, so that each time we read it again, it feels familiar.',
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
        'looking_back' => 'Each page of the diary we close is a story preserved. '
            . 'There was joy, there were gatherings, and above all, there were the people '
            . 'who shaped 2026 together.',
        'thanks' => 'Thank you for being there, for sharing, and for helping create those moments. '
            . 'After all, a memorable journey is never the story of just one person.',
        'next_year'    => 'The year 2027 will have pages of its own, filled with stories '
            . 'no one can yet foresee. We look forward to seeing you all there.',
        'message'      => 'Thank you for making 2026 a memorable year with THK HOLDINGS VIETNAM.',
    ],
];