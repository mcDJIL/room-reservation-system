<?php
session_start();
require_once '../../config/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

if (empty($_SESSION['is_login']) || empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$id     = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$id || !in_array($action, ['approved', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
    exit();
}

$admin_id = intval($_SESSION['user_id']);
$stmt = $conn->prepare("UPDATE reservations SET status = ?, approved_by = ? WHERE id = ?");
$stmt->bind_param('sii', $action, $admin_id, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'status' => $action]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status']);
}
$stmt->close();
