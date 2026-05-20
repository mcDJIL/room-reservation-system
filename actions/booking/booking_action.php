<?php
    session_start();

    require_once '../../config/connection.php';
    if ($_SERVER["REQUEST_METHOD"]=="POST") {
        $tanggal=mysqli_real_escape_string($conn, $_POST['tanggal']);
        $start_time=mysqli_real_escape_string($conn, $_POST['mulai']);
        $end_time=mysqli_real_escape_string($conn, $_POST['selesai']);
    }
?>