<?php
include __DIR__ . '/../../config/connection.php';

$qLog = mysqli_query($conn, "
SELECT 
    r.id,
    u.name AS user_name,
    a.name AS admin_name,
    rm.room_name,
    r.status,
    r.created_at
FROM reservations r
JOIN users u ON r.user_id = u.id
LEFT JOIN users a ON r.approved_by = a.id
JOIN rooms rm ON r.room_id = rm.id
ORDER BY r.created_at DESC
LIMIT 10
");
?>