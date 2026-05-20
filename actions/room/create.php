<?php
    $room_name = $_POST["room_name"];
    $capacity = $_POST["capacity"];
    $facility = $_POST["facility"];
    $short_description = $_POST["short_description"];
    $detail_description = $_POST["detail_description"];
    $is_active = $_POST["is_active"];

    include 'connection.php';
    $sql = "INSERT INTO rooms(room_name,capacity,facility,short_description,detail_description,is_active)
    VALUES ($room_name,$capacity,$facility,$short_description,$detail_description,$is_active)";
?>