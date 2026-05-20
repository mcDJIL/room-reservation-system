<?php
include __DIR__ . '/../../config/connection.php';

$qPending = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE status='waiting'");

$pending = mysqli_fetch_assoc($qPending)['total'];

$qReservasi = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE MONTH(reservation_date)=MONTH(CURRENT_DATE())");

$reservasi = mysqli_fetch_assoc($qReservasi)['total'];

$qAktif = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM rooms
WHERE is_active=1");

$aktif = mysqli_fetch_assoc($qAktif)['total'];

$qApproved = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE status='approved'");

$approved = mysqli_fetch_assoc($qApproved)['total'];
?>