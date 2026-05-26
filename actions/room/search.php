<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connection.php';

if ($conn->connect_error) {
    die(json_encode(['error' => 'Koneksi gagal']));
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 15;

// Build WHERE conditions
$where_conditions = [];

if ($status_filter === 'aktif') {
    $where_conditions[] = "r.is_active = 1";
} elseif ($status_filter === 'tidak_aktif') {
    $where_conditions[] = "r.is_active = 0";
}

if (!empty($query)) {
    $search_term = '%' . mysqli_real_escape_string($conn, $query) . '%';
    $where_conditions[] = "(r.room_name LIKE '$search_term' OR b.name LIKE '$search_term' OR r.facility LIKE '$search_term')";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM rooms r LEFT JOIN buildings b ON r.building_id = b.id $where_clause";
$count_result = mysqli_query($conn, $count_sql);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = max(1, ceil($total_rows / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;

// Get paginated data
$sql = "SELECT r.id, r.room_name, r.capacity, r.is_active, b.name AS building_name 
        FROM rooms r 
        LEFT JOIN buildings b ON r.building_id = b.id 
        $where_clause
        ORDER BY r.room_name ASC
        LIMIT $offset, $per_page";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die(json_encode(['error' => 'Query error: ' . mysqli_error($conn)]));
}

$rooms = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rooms[] = $row;
}

mysqli_close($conn);

echo json_encode([
    'success' => true,
    'data' => $rooms,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'per_page' => $per_page,
        'total_rows' => $total_rows,
        'from' => $total_rows > 0 ? ($page - 1) * $per_page + 1 : 0,
        'to' => min($page * $per_page, $total_rows)
    ]
]);
?>