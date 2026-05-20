<?php

$id = $_POST["id"];

$sql = "DELETE FROM rooms WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Data berhasil dihapus";
    } else {
        echo "Gagal menghapus data: " . mysqli_stmt_error($stmt);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Gagal menyiapkan query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>