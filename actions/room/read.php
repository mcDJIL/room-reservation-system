<?php
require_once __DIR__ . '/../../config/connection.php';

if ($conn->connect_error) {
    die('Koneksi Gagal: ' . $conn->connect_error);
}

$rooms = [];
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 15;

$sql = "SELECT r.id, r.room_name, r.capacity, r.is_active, b.name AS building_name 
        FROM rooms r 
        LEFT JOIN buildings b ON r.building_id = b.id";

$where = [];
if ($status_filter === 'aktif') {
    $where[] = "r.is_active = 1";
} elseif ($status_filter === 'tidak_aktif') {
    $where[] = "r.is_active = 0";
}

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY r.room_name ASC';

// Get total for pagination info
$count_sql = str_replace('SELECT r.id, r.room_name, r.capacity, r.is_active, b.name AS building_name', 'SELECT COUNT(*) as total', $sql);
$count_result = mysqli_query($conn, $count_sql);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = max(1, ceil($total_rows / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;

// Paginate
$sql .= " LIMIT $offset, $per_page";

$result = mysqli_query($conn, $sql);
if ($result === false) {
    die('Query error: ' . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {
    $rooms[] = $row;
}

$pagination = [
    'current_page' => $page,
    'total_pages' => $total_pages,
    'total_rows' => $total_rows,
    'per_page' => $per_page,
    'from' => $total_rows > 0 ? ($page - 1) * $per_page + 1 : 0,
    'to' => min($page * $per_page, $total_rows)
];

mysqli_close($conn);
?>