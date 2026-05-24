<?php

// Ambil input dengan sanitasi dasar
$room_name = isset($_POST["room_name"]) ? trim($_POST["room_name"]) : '';
$building_id = isset($_POST["building_id"]) ? (int) $_POST["building_id"] : null;
$capacity = isset($_POST["capacity"]) ? (int) $_POST["capacity"] : 0;
$facility = isset($_POST["facility"]) ? trim($_POST["facility"]) : '';
$short_description = isset($_POST["short_description"]) ? trim($_POST["short_description"]) : '';
$detail_description = isset($_POST["detail_description"]) ? trim($_POST["detail_description"]) : '';
$is_active = isset($_POST["is_active"]) ? (int) $_POST["is_active"] : 0;

require_once __DIR__ . '/../../config/connection.php';

if ($room_name === '') {
    echo "Nama ruangan wajib diisi";
    exit;
}

$sql = "INSERT INTO rooms (room_name, building_id, capacity, facility, short_description, detail_description, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)";

header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => ''];

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "siisssi", $room_name, $building_id, $capacity, $facility, $short_description, $detail_description, $is_active);

    if (mysqli_stmt_execute($stmt)) {
        $insertId = mysqli_insert_id($conn);
        // Ambil nama gedung jika tersedia
        $building_name = null;
        if ($building_id) {
            $bq = mysqli_prepare($conn, "SELECT name FROM buildings WHERE id = ? LIMIT 1");
            if ($bq) {
                mysqli_stmt_bind_param($bq, 'i', $building_id);
                mysqli_stmt_execute($bq);
                mysqli_stmt_bind_result($bq, $bname);
                if (mysqli_stmt_fetch($bq)) $building_name = $bname;
                mysqli_stmt_close($bq);
            }
        }

        $response['success'] = true;
        $response['message'] = 'Data berhasil ditambahkan';
        $response['room'] = [
            'id' => $insertId,
            'room_name' => $room_name,
            'building_id' => $building_id,
            'building_name' => $building_name,
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