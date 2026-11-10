<?php

// Nội dung hiển thị của xác nhận tham dự, tra cứu vé và trang link lỗi.
// Lấy bằng message('nhóm.khoá'); placeholder {tên} được thay bằng giá trị truyền vào.
return [
    'email_label'       => 'Email',
    'email_placeholder' => 'name@thk-hd.vn',
    'thanks_declined'   => 'Thank you for your response. We are sorry to miss you this time.',

    'errors' => [
        'email_required' => 'Please enter your email address.',
        'email_invalid'  => 'Please enter a valid email address.',
    ],

    'rsvp' => [
        'title'             => 'Confirm Attendance',
        // {deadline}: hạn phản hồi (config/event.php: rsvp_deadline)
        'lead'              => 'To help us prepare accordingly, kindly respond by {deadline}.',
        'name_label'        => 'Full Name',
        'name_placeholder'  => 'Enter your full name',
        'attendance_label'  => 'Will you be attending?',
        'attending'         => 'Yes, I will attend',
        'declined'          => 'Regretfully, I cannot attend',
        'hint'              => 'Once submitted, your response cannot be changed.',
        'submit'            => 'Confirm',
        'submitting'        => 'Submitting…',

        // Đã xác nhận. {event}: tên sự kiện kèm năm
        'thanks_attending'  => 'Thank you for confirming. We look forward to seeing you at the {event}.',
        'status_label'      => 'Status',
        'status_attending'  => 'Attending',
        'recorded'          => '',
        'ticket_below'      => 'Your ticket is shown below.',
        'ticket_pending'    => '',
        'already_responded' => 'Your response has already been recorded and cannot be changed.',

        'errors' => [
            'save_failed'         => 'We could not save your response. Please try again in a few minutes.',
            'name_required'       => 'Please enter your full name.',
            // {max}: số ký tự tối đa
            'name_too_long'       => 'Full name must be {max} characters or fewer.',
            'attendance_required' => 'Please let us know whether you will attend.',
        ],
    ],

    'lookup' => [
        'title'             => 'Find Your Ticket',
        'lead'              => 'Already confirmed your attendance? Enter your email to find your ticket.',
        'submit'            => 'Find Ticket',
        'submitting'        => 'Searching…',
        'found'             => 'We found your ticket. It is shown below.',
        // Dùng chung cho email không có trong danh sách và email chưa RSVP
        'not_found'         => 'No ticket was found for this email address.',
        // Đã xác nhận tham gia nhưng chưa có mã Lucky Draw hoặc số bàn
        'ticket_pending'    => 'No ticket was found for this email address.',

        'errors' => [
            'failed'         => 'We could not look up your ticket. Please try again in a few minutes.',
        ],
    ],

    'invalid' => [
        'title'             => 'Invalid Link',
        'text'              => 'Sorry, we could not find a ticket associated with this link.',
        // Không đọc được dữ liệu (lỗi tạm thời)
        'unavailable_title' => 'Invitation Temporarily Unavailable',
        'unavailable_text'  => 'We could not load your invitation. Please try again in a few minutes.',
        'back'              => 'Back to Invitation',
    ],
];
