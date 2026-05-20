<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$building = isset($_POST['building']) ? trim($_POST['building']) : '';
$capacity = isset($_POST['capacity']) ? (int)$_POST['capacity'] : null;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($id <= 0 || $name === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// map status text to is_active
$is_active = ($status === 'Aktif' || $status === '1' || strtolower($status) === 'active') ? 1 : 0;

// find or create building id by name
$building_id = null;
if ($building !== '') {
    $stmt = $conn->prepare('SELECT id FROM buildings WHERE name = ? LIMIT 1');
    $stmt->bind_param('s', $building);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $building_id = (int)$row['id'];
    } else {
        // create new building
        $ins = $conn->prepare('INSERT INTO buildings (name) VALUES (?)');
        $ins->bind_param('s', $building);
        $ins->execute();
        $building_id = $ins->insert_id ?: null;
        if ($ins) $ins->close();
    }
    if ($stmt) $stmt->close();
}

// perform update
$sql = 'UPDATE rooms SET room_name = ?, building_id = ?, capacity = ?, is_active = ? WHERE id = ?';
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}
$stmt->bind_param('siiii', $name, $building_id, $capacity, $is_active, $id);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

// return updated row summary
$q = $conn->prepare('SELECT r.id, r.room_name, r.capacity, r.is_active, b.name AS building_name FROM rooms r LEFT JOIN buildings b ON r.building_id = b.id WHERE r.id = ? LIMIT 1');
$q->bind_param('i', $id);
$q->execute();
$res = $q->get_result();
$row = $res->fetch_assoc();
$q->close();

echo json_encode(['success' => true, 'room' => $row]);
exit;

?>
<?php

$id = $_POST["id"];
$name = $_POST["name"];
$building = $_POST["building"];
$capacity = $_POST["capacity"];
$status = $_POST["status"];

$sql = "UPDATE rooms SET room_name = ?, building_id = ?, capacity = ?, is_active = ? WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    
    mysqli_stmt_bind_param($stmt, "ssiii", $name, $building, $capacity, $status, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Update berhasil";
    } else {
        echo "Update Gagal, Error : " . mysqli_stmt_error($stmt);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Gagal menyiapkan query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>