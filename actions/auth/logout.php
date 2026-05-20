<?php
session_start();
session_destroy(); // Menghapus semua session user
header("Location: ../../pages/auth/login.php?status=logout"); // Mengarahkan kembali sambil membawa status logout
exit();
?>