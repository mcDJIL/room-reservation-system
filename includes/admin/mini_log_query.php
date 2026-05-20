<?php
include __DIR__ . '/../../config/connection.php';

$qLog = mysqli_query($conn, "
SELECT 
    r.id,
    u.name AS user_name,
    rm.room_name,
    r.status,
    r.created_at
FROM reservations r
JOIN users u ON r.user_id = u.id
JOIN rooms rm ON r.room_id = rm.id
GROUP BY r.id
ORDER BY r.created_at DESC
LIMIT 10
");
?>