<?php

$id = $_POST["id"];
$name = $_POST["name"];
$building = $_POST["building"];
$capacity = $_POST["capacity"];
$status = $_POST["status"];

$sql = "UPDATE rooms SET room_name = ?, building_id = ?, capacity = ?, is_active = ? WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    
    mysqli_stmt_bind_param($stmt, "ssiii", $name, $building, $capacity, $status, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Update berhasil";
    } else {
        echo "Update Gagal, Error : " . mysqli_stmt_error($stmt);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Gagal menyiapkan query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>