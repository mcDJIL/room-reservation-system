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

$conn->begin_transaction();

$clear = $conn->prepare('UPDATE room_photos SET is_primary = 0 WHERE room_id = ?');
$clear->bind_param('i', $room_id);
$okClear = $clear->execute();
$clear->close();

$set = $conn->prepare('UPDATE room_photos SET is_primary = 1 WHERE id = ? AND room_id = ?');
$set->bind_param('ii', $photo_id, $room_id);
$okSet = $set->execute();
$set->close();

if (!$okClear || !$okSet) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

$conn->commit();

echo json_encode(['success' => true, 'message' => 'Foto utama berhasil diubah']);