<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid id']);
    exit;
}

$conn->begin_transaction();

$deletePhotos = $conn->prepare('DELETE FROM room_photos WHERE room_id = ?');
$deletePhotos->bind_param('i', $id);
$okPhotos = $deletePhotos->execute();
$deletePhotos->close();

$deleteReservations = $conn->prepare('DELETE FROM reservations WHERE room_id = ?');
$deleteReservations->bind_param('i', $id);
$okReservations = $deleteReservations->execute();
$deleteReservations->close();

$deleteRoom = $conn->prepare('DELETE FROM rooms WHERE id = ?');
$deleteRoom->bind_param('i', $id);
$okRoom = $deleteRoom->execute();
$deleteRoom->close();

if (!$okPhotos || !$okReservations || !$okRoom) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

$conn->commit();

echo json_encode(['success' => true, 'message' => 'Ruangan berhasil dihapus']);
exit;

?>