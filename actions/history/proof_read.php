<?php

include '../../config/connection.php';

$proof_data = null;
$proof_error = '';

if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true || !isset($_SESSION['user_id'])) {
    $proof_error = 'Anda harus login untuk mengakses bukti reservasi.';
    return;
}

$user_id = (int) $_SESSION['user_id'];
$reservation_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

if ($reservation_id <= 0) {
    $proof_error = 'ID reservasi tidak valid.';
    return;
}

$sql = "
    SELECT
        re.id AS reservation_id,
        re.reservation_date,
        re.start_hour,
        re.end_hour,
        re.reason,
        re.status,
        re.created_at,
        r.room_name,
        r.capacity,
        b.name AS building_name,
        u.name AS user_name,
        au.name AS approved_by_name
    FROM reservations re
    INNER JOIN users u ON re.user_id = u.id
    INNER JOIN rooms r ON re.room_id = r.id
    INNER JOIN buildings b ON r.building_id = b.id
    LEFT JOIN users au ON re.approved_by = au.id
    WHERE re.id = ? AND re.user_id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    $proof_error = 'Gagal menyiapkan data bukti.';
    return;
}

mysqli_stmt_bind_param($stmt, 'ii', $reservation_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$proof_data = $result ? mysqli_fetch_assoc($result) : null;

if (!$proof_data) {
    $proof_error = 'Data reservasi tidak ditemukan.';
    return;
}

if ($proof_data['status'] !== 'approved') {
    $proof_data = null;
    $proof_error = 'Bukti hanya tersedia untuk reservasi dengan status disetujui.';
}
