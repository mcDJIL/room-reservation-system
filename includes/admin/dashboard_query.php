<?php
include __DIR__ . '/../../config/connection.php';

// Antrean pending
$qPending = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE status='waiting'");

$pending = mysqli_fetch_assoc($qPending)['total'];


// Total reservasi bulan ini
$qReservasi = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE MONTH(reservation_date)=MONTH(CURRENT_DATE())");

$reservasi = mysqli_fetch_assoc($qReservasi)['total'];


// Ruangan aktif
$qAktif = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM rooms
WHERE is_active=1");

$aktif = mysqli_fetch_assoc($qAktif)['total'];


// Reservasi disetujui
$qApproved = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE status='approved'");

$approved = mysqli_fetch_assoc($qApproved)['total'];
?>