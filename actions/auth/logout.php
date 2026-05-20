<?php
session_start();
session_destroy(); // Menghapus semua session user
header("Location: ../../pages/auth/login.php"); // Mengarahkan kembali ke halaman utama
exit();
?>