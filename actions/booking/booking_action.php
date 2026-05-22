<?php
    session_start();
    require_once '../../config/connection.php';

    if ($_SERVER["REQUEST_METHOD"]!="POST") {
        header("Location: .././pages/user/booking.php");
        exit();
    }
    $id_user=$_SESSION['user_id'];
    $id_room=intval($_POST['ruangan']);
    $date=mysqli_real_escape_string($conn, $_POST['tanggal']);
    $start=mysqli_real_escape_string($conn, $_POST['mulai']);
    $end=mysqli_real_escape_string($conn, $_POST['selesai']);
    $reason=mysqli_real_escape_string($conn, $_POST['keperluan']);

    $check="SELECT id FROM reservations WHERE room_id=$id_room AND reservation_date='$date' AND status IN ('approved', 'waiting') AND ((start_hour<'$end' AND end_hour>'$start'))";
    $result_check=mysqli_query($conn, $check);

    $insert="INSERT INTO reservations (user_id, room_id, reservation_date, start_hour, end_hour, reason, status) VALUES ($id_user, $id_room, '$date', '$start', '$end', '$reason', 'waiting')";
    $result=mysqli_query($conn, $insert);

    if ($result) echo "mantaf";
    else echo "nono";
    exit();
?>