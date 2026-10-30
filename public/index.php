<?php

require dirname(__DIR__) . '/bootstrap.php';

$event = loadEvent();
$id = $_GET['id'] ?? '';
$employee = null;

// Có id => phải là link mời hợp lệ; không có id => thiệp chung, không cá nhân hoá
if ($id !== '') {
    $employee = findInvitee($id);

    if ($employee === null) {
        http_response_code(404);
        require BASE_PATH . '/views/invalid.php';
        exit;
    }
}

require BASE_PATH . '/views/invite.php';
