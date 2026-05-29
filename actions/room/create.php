<?php
header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => ''];

// Ambil input dengan sanitasi dasar
$room_name = isset($_POST["room_name"]) ? trim($_POST["room_name"]) : '';
$building_id = isset($_POST["building_id"]) ? (int) $_POST["building_id"] : 0;
$capacity = isset($_POST["capacity"]) ? (int) $_POST["capacity"] : 0;
$facility = isset($_POST["facility"]) ? trim($_POST["facility"]) : '';
$short_description = isset($_POST["short_description"]) ? trim($_POST["short_description"]) : '';
$detail_description = isset($_POST["detail_description"]) ? trim($_POST["detail_description"]) : '';
$is_active = isset($_POST["is_active"]) ? (int) $_POST["is_active"] : 0;

require_once __DIR__ . '/../../config/connection.php';

if (!$conn || mysqli_connect_errno()) {
    $response['message'] = 'Koneksi database gagal: ' . mysqli_connect_error();
    echo json_encode($response);
    exit;
}

if ($room_name === '') {
    $response['message'] = 'Nama ruangan wajib diisi';
    echo json_encode($response);
    exit;
}

if ($building_id <= 0) {
    $response['message'] = 'Pilih gedung terlebih dahulu';
    echo json_encode($response);
    exit;
}

$sql = "INSERT INTO rooms (room_name, building_id, capacity, facility, short_description, detail_description, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "siisssi", $room_name, $building_id, $capacity, $facility, $short_description, $detail_description, $is_active);

    if (mysqli_stmt_execute($stmt)) {
        $insertId = mysqli_insert_id($conn);
        $response['success'] = true;
        $response['message'] = 'Ruangan berhasil ditambahkan';
        $response['room'] = [
            'id' => $insertId,
            'room_name' => $room_name,
            'building_id' => $building_id,
            'capacity' => $capacity,
            'is_active' => $is_active
        ];
    } else {
        $response['message'] = 'Gagal menambahkan data: ' . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    $response['message'] = 'Gagal menyiapkan query: ' . mysqli_error($conn);
}

mysqli_close($conn);
echo json_encode($response);
?>