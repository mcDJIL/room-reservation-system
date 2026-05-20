<?php
    session_start();

    require_once '../../config/connection.php';
    if ($_SERVER["REQUEST_METHOD"]=="POST") {
        $id_user_active=$_SESSION['user_id'];
        $tanggal=mysqli_real_escape_string($conn, $_POST['tanggal']);
        $start_time=mysqli_real_escape_string($conn, $_POST['mulai']);
        $end_time=mysqli_real_escape_string($conn, $_POST['selesai']);
    } else {
        header("Location: booking.php");
        exit();
    }
?>