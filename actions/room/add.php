<?php

$room_name = $_POST["room_name"];
$capacity = $_POST["capacity"];
$facility = $_POST["facility"];
$short_description = $_POST["short_description"];
$detail_description = $_POST["detail_description"];
$is_active = $_POST["is_active"];

include 'connection.php';

$sql = "INSERT INTO rooms (room_name, capacity, facility, short_description, detail_description, is_active) VALUES (?, ?, ?, ?, ?, ?)";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "sisssi", $room_name, $capacity, $facility, $short_description, $detail_description, $is_active);

    if (mysqli_stmt_execute($stmt)) {
        echo "Data berhasil ditambahkan";
    } else {
        echo "Gagal menambahkan data: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Gagal menyiapkan query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>