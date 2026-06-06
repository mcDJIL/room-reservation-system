<?php
session_start();
include '../../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/login.php');
    exit;
}

if (!isset($_POST['reservation_id']) || !is_numeric($_POST['reservation_id'])) {
    $_SESSION['error'] = 'ID Reservasi tidak valid.';
    header('Location: ../../pages/user/history.php');
    exit;
}

$reservation_id = (int) $_POST['reservation_id'];
$user_id = (int) $_SESSION['user_id'];
$cancel_reason = isset($_POST['cancel_reason']) ? trim($_POST['cancel_reason']) : '';

if (empty($cancel_reason)) {
    $_SESSION['error'] = 'Alasan pembatalan tidak boleh kosong.';
    header('Location: ../../pages/user/history.php');
    exit;
}

if (strlen($cancel_reason) > 500) {
    $_SESSION['error'] = 'Alasan pembatalan terlalu panjang (maksimal 500 karakter).';
    header('Location: ../../pages/user/history.php');
    exit;
}

$check_sql = "
    SELECT re.*, r.reservation_date, r.start_hour
    FROM reservations re
    LEFT JOIN reservations r ON re.id = r.id
    WHERE re.id = ? AND re.user_id = ?
";

$check_stmt = mysqli_prepare($conn, $check_sql);
if (!$check_stmt) {
    $_SESSION['error'] = 'Database error: ' . mysqli_error($conn);
    header('Location: ../../pages/user/history.php');
    exit;
}

mysqli_stmt_bind_param($check_stmt, 'ii', $reservation_id, $user_id);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);
$reservation = mysqli_fetch_assoc($result);

if (!$reservation) {
    $_SESSION['error'] = 'Reservasi tidak ditemukan.';
    header('Location: ../../pages/user/history.php');
    exit;
}

if (!in_array($reservation['status'], ['waiting', 'approved'])) {
    $_SESSION['error'] = 'Reservasi dengan status ' . $reservation['status'] . ' tidak dapat dibatalkan.';
    header('Location: ../../pages/user/history.php');
    exit;
}

$reservation_datetime = strtotime($reservation['reservation_date'] . ' ' . $reservation['start_hour']);
$now = time();

if ($reservation_datetime < $now) {
    $_SESSION['error'] = 'Tidak dapat membatalkan reservasi yang sudah berlangsung atau telah lewat.';
    header('Location: ../../pages/user/history.php');
    exit;
}

$update_sql = "
    UPDATE reservations
    SET status = 'cancelled', 
        cancelled_at = NOW(),
        cancelled_reason = ?
    WHERE id = ? AND user_id = ?
";

$update_stmt = mysqli_prepare($conn, $update_sql);
if (!$update_stmt) {
    $_SESSION['error'] = 'Database error: ' . mysqli_error($conn);
    header('Location: ../../pages/user/history.php');
    exit;
}

mysqli_stmt_bind_param($update_stmt, 'sii', $cancel_reason, $reservation_id, $user_id);

if (mysqli_stmt_execute($update_stmt)) {
    $_SESSION['success'] = 'Reservasi berhasil dibatalkan.';
} else {
    $_SESSION['error'] = 'Gagal membatalkan reservasi: ' . mysqli_error($conn);
}

mysqli_stmt_close($update_stmt);
mysqli_close($conn);

header('Location: ../../pages/user/history.php');
exit;
