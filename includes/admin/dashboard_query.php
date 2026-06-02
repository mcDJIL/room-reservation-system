<?php

include __DIR__ . '/../../config/connection.php';

// Menghitung jumlah data reservasi dengan status 'waiting' atau dalam antrean
$qPending = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE status = 'waiting'");

$pending = mysqli_fetch_assoc($qPending)['total'];

// Menghitung jumlah data reservasi bulan ini
$qReservasi = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE MONTH(reservation_date) = MONTH(CURRENT_DATE())
AND YEAR(reservation_date) = YEAR(CURRENT_DATE())");

$reservasi = mysqli_fetch_assoc($qReservasi)['total'];

// Menghitung jumlah ruangan yang aktif
$qAktif = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM rooms
WHERE is_active = 1");

$aktif = mysqli_fetch_assoc($qAktif)['total'];

// Menghitung jumlah data reservasi dengan status 'approved' yang sedang berlangsung hari ini
$qDipinjam = mysqli_query($conn,
"SELECT COUNT(*) as total
FROM reservations
WHERE status = 'approved'
AND reservation_date = CURDATE()
AND CURRENT_TIME() BETWEEN start_hour AND end_hour");

$dipinjam = mysqli_fetch_assoc($qDipinjam)['total'];

// Menghitung jumlah ruangan yang tersedia (aktif - dipinjam)
$tersedia = $aktif - $dipinjam;

?>