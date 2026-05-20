<?php
include __DIR__ . '/../../config/connection.php';

// 1. TREND PEMINJAMAN (12 BULAN)
$qTrend = mysqli_query($conn, "
    SELECT 
        MONTH(reservation_date) AS bulan,
        COUNT(*) AS total
    FROM reservations
    WHERE YEAR(reservation_date) = YEAR(CURDATE())
    GROUP BY MONTH(reservation_date)
    ORDER BY MONTH(reservation_date)
");

// Siapkan struktur default 12 bulan dengan nilai 0
$monthsMaster = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
$trendDataFilled = array_fill(0, 12, 0); // Membuat array [0, 0, 0, ..., 0] sebanyak 12 slot

while ($row = mysqli_fetch_assoc($qTrend)) {
    $indexBulan = (int)$row['bulan'] - 1; // Bulan 1 (Januari) masuk ke index 0
    $trendDataFilled[$indexBulan] = (int)$row['total'];
}


// 2. RUANGAN TERFAVORIT (TOP 3)
$qTopRooms = mysqli_query($conn, "
    SELECT 
        rooms.room_name,
        COUNT(*) AS total
    FROM reservations
    JOIN rooms ON reservations.room_id = rooms.id
    GROUP BY rooms.id
    ORDER BY total DESC
    LIMIT 3
");

$roomLabel = [];
$roomData = [];

while ($row = mysqli_fetch_assoc($qTopRooms)) {
    $roomLabel[] = $row['room_name'];
    $roomData[] = (int)$row['total']; // Ambil nilai sebagai integer
}
?>