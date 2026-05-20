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

// Soft delete: set is_active = 0
$stmt = $conn->prepare('UPDATE rooms SET is_active = 0 WHERE id = ?');
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

echo json_encode(['success' => true]);
exit;

?>
<?php

$id = $_POST["id"];

$sql = "DELETE FROM rooms WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Data berhasil dihapus";
    } else {
        echo "Gagal menghapus data: " . mysqli_stmt_error($stmt);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Gagal menyiapkan query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>