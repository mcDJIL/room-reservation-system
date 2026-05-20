<?php
include '/config/connection.php';

$rooms = [];
$sql = "SELECT r.id, r.room_name, r.capacity, r.is_active, b.building_name 
        FROM rooms r 
        LEFT JOIN buildings b ON r.building_id = b.id";
        
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }
}

mysqli_close($conn);
?>