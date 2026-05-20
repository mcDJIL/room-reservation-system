<?php

include __DIR__ . '/../../config/connection.php';

$qRecent = mysqli_query($conn,
"SELECT
    reservations.id,
    users.name AS user_name,
    rooms.room_name,
    reservations.reservation_date,
    reservations.status
FROM reservations
JOIN users ON reservations.user_id = users.id
JOIN rooms ON reservations.room_id = rooms.id
ORDER BY reservations.created_at DESC 
LIMIT 5");

?>