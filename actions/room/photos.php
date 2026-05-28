<?php
require_once __DIR__ . '/../../config/connection.php';

header('Content-Type: application/json; charset=utf-8');

$room_id = isset($_GET['room_id']) ? (int) $_GET['room_id'] : 0;
if ($room_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID ruangan tidak valid', 'photos' => []]);
    exit;
}

$stmt = $conn->prepare('SELECT id, photo, is_primary FROM room_photos WHERE room_id = ? ORDER BY is_primary DESC, id ASC');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyiapkan query', 'photos' => []]);
    exit;
}

$stmt->bind_param('i', $room_id);
$stmt->execute();
$result = $stmt->get_result();

$photos = [];
while ($row = $result->fetch_assoc()) {
    $photoPath = trim((string) $row['photo']);
    $webPath = $photoPath !== '' ? '../../assets/images/' . ltrim($photoPath, '/') : '';
    $photos[] = [
        'id' => (int) $row['id'],
        'photo' => $photoPath,
        'photo_url' => $webPath,
        'is_primary' => (int) $row['is_primary'],
    ];
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'photos' => $photos]);