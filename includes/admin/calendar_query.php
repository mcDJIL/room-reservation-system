<?php
include __DIR__ . '/../../config/connection.php';

$qCalendar = mysqli_query($conn, "
SELECT 
    r.reservation_date,
    r.start_hour,
    r.end_hour,
    r.status,
    rm.room_name,
    u.name AS user_name
FROM reservations r
JOIN rooms rm ON r.room_id = rm.id
JOIN users u ON r.user_id = u.id
WHERE r.status = 'approved' -- Mengambil data ruangan yang disetujui/fix dipinjam
ORDER BY r.reservation_date ASC
");

$calendarEvents = [];

while ($row = mysqli_fetch_assoc($qCalendar)) {
    // Ambil tanggal dan jam start
    $start = $row['reservation_date'] . "T" . $row['start_hour'];
    
    // Pastikan end_hour ada sebelum digabungkan
    $end = !empty($row['end_hour']) ? $row['reservation_date'] . "T" . $row['end_hour'] : null;

    $calendarEvents[] = [
        "title" => $row['room_name'] . " (" . $row['user_name'] . ")",
        "start" => $start,
        "end"   => $end
    ];
}
?>