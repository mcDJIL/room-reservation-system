<?php
session_start();
require_once __DIR__ . '/../../config/connection.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (empty($_SESSION['is_login']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$photo_id = isset($_POST['photo_id']) ? (int) $_POST['photo_id'] : 0;
$room_id = isset($_POST['room_id']) ? (int) $_POST['room_id'] : 0;

if ($photo_id <= 0 || $room_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
    exit;
}

$stmt = $conn->prepare('SELECT photo, is_primary FROM room_photos WHERE id = ? AND room_id = ? LIMIT 1');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyiapkan query']);
    exit;
}

$stmt->bind_param('ii', $photo_id, $room_id);
$stmt->execute();
$result = $stmt->get_result();
$photo = $result->fetch_assoc();
$stmt->close();

if (!$photo) {
    echo json_encode(['success' => false, 'message' => 'Foto tidak ditemukan']);
    exit;
}

$conn->begin_transaction();

$delete = $conn->prepare('DELETE FROM room_photos WHERE id = ? AND room_id = ?');
$delete->bind_param('ii', $photo_id, $room_id);
$okDelete = $delete->execute();
$delete->close();

if (!$okDelete) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

if ((int) $photo['is_primary'] === 1) {
    $next = $conn->prepare('SELECT id FROM room_photos WHERE room_id = ? ORDER BY id ASC LIMIT 1');
    $next->bind_param('i', $room_id);
    $next->execute();
    $nextResult = $next->get_result();
    $nextPhoto = $nextResult->fetch_assoc();
    $next->close();

    if ($nextPhoto) {
        $set = $conn->prepare('UPDATE room_photos SET is_primary = 1 WHERE id = ? AND room_id = ?');
        $nextPhotoId = (int) $nextPhoto['id'];
        $set->bind_param('ii', $nextPhotoId, $room_id);
        $okSet = $set->execute();
        $set->close();

        if (!$okSet) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $conn->error]);
            exit;
        }
    }
}

$conn->commit();

$photoPath = trim((string) $photo['photo']);
$filesystemPath = __DIR__ . '/../../assets/images/' . ltrim($photoPath, '/');
if ($photoPath !== '' && file_exists($filesystemPath)) {
    @unlink($filesystemPath);
}

echo json_encode(['success' => true, 'message' => 'Foto berhasil dihapus']);