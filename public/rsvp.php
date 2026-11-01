<?php

require dirname(__DIR__) . '/bootstrap.php';

use App\Rsvp\RsvpLog;

const RSVP_NAME_MAX = 100;

$event = loadEvent();
$id = $_GET['id'] ?? '';

// Có id => link mời hợp lệ thì điền sẵn tên và ghi kèm email/uuid; id sai => vẫn cho nhập tay như link chung
$employee = $id !== '' ? findInvitee($id) : null;

$name = trim((string) ($employee['name'] ?? ''));
$attending = null;
$errors = [];
$submitted = isset($_GET['done']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $attending = $_POST['attending'] ?? null;

    if ($name === '') {
        $errors['name'] = 'Vui lòng nhập họ và tên.';
    } elseif (mb_strlen($name) > RSVP_NAME_MAX) {
        $errors['name'] = 'Họ và tên tối đa ' . RSVP_NAME_MAX . ' ký tự.';
    }

    if (!in_array($attending, ['yes', 'no'], true)) {
        $errors['attending'] = 'Vui lòng chọn bạn có tham gia được hay không.';
    }

    if ($errors === []) {
        try {
            (new RsvpLog(STORAGE_PATH . '/rsvp.json'))->add([
                'name'      => $name,
                'attending' => $attending === 'yes',
                'email'     => $employee['email'] ?? null,
                'uuid'      => $employee !== null ? employeeUuid($employee) : null,
            ]);

            // PRG: tải lại trang không gửi trùng phản hồi
            $query = array_filter(['id' => $employee !== null ? $id : null, 'done' => $attending]);
            header('Location: rsvp.php?' . http_build_query($query), true, 303);
            exit;
        } catch (RuntimeException $e) {
            error_log('rsvp.php: ' . $e->getMessage());
            $errors['form'] = 'Chưa lưu được phản hồi, vui lòng thử lại sau ít phút.';
        }
    }

    if ($errors !== []) {
        http_response_code(422);
    }
}

require BASE_PATH . '/views/rsvp.php';
