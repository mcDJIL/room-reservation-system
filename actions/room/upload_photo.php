<?php
header('Content-Type: application/json; charset=utf-8');
// Endpoint menerima `room_id` dan files[] (input name: photos[])

require_once __DIR__ . '/../../config/connection.php';

$response = ['success' => false, 'message' => '', 'photos' => []];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Method not allowed';
    echo json_encode($response);
    exit;
}

$room_id = isset($_POST['room_id']) ? (int) $_POST['room_id'] : 0;
if ($room_id <= 0) {
    $response['message'] = 'Invalid room_id';
    echo json_encode($response);
    exit;
}

if (!isset($_FILES) || empty($_FILES)) {
    $response['message'] = 'No files uploaded';
    echo json_encode($response);
    exit;
}

$uploadDir = __DIR__ . '/../../assets/images/uploads';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        $response['message'] = 'Failed to create upload directory';
        echo json_encode($response);
        exit;
    }
}

$files = [];
// Normalize multiple files input (photos[])
foreach ($_FILES as $field => $data) {
    if (is_array($data['name'])) {
        for ($i = 0; $i < count($data['name']); $i++) {
            $files[] = [
                'name' => $data['name'][$i],
                'tmp_name' => $data['tmp_name'][$i],
                'error' => $data['error'][$i],
                'size' => $data['size'][$i],
                'type' => $data['type'][$i]
            ];
        }
    } else {
        $files[] = $data;
    }
}

// Check existing primary
$hasPrimary = false;
$q = mysqli_prepare($conn, 'SELECT COUNT(*) FROM room_photos WHERE room_id = ? AND is_primary = 1');
if ($q) {
    mysqli_stmt_bind_param($q, 'i', $room_id);
    mysqli_stmt_execute($q);
    mysqli_stmt_bind_result($q, $cnt);
    if (mysqli_stmt_fetch($q) && $cnt > 0) $hasPrimary = true;
    mysqli_stmt_close($q);
}

foreach ($files as $index => $file) {
    if ($file['error'] !== UPLOAD_ERR_OK) continue;
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '-', pathinfo($file['name'], PATHINFO_FILENAME));
    $newName = $safeName . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $uploadDir . '/' . $newName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $dbPath = 'uploads/' . $newName; // store relative path

        // determine is_primary (first uploaded becomes primary if none exists)
        $is_primary = (!$hasPrimary && $index === 0) ? 1 : 0;
        if ($is_primary) $hasPrimary = true;

        $ins = mysqli_prepare($conn, 'INSERT INTO room_photos (room_id, photo, is_primary) VALUES (?, ?, ?)');
        if ($ins) {
            mysqli_stmt_bind_param($ins, 'isi', $room_id, $dbPath, $is_primary);
            mysqli_stmt_execute($ins);
            $photoId = mysqli_insert_id($conn);
            mysqli_stmt_close($ins);

            $response['photos'][] = ['id' => $photoId, 'photo' => $dbPath, 'is_primary' => $is_primary];
        }
    }
}

mysqli_close($conn);

if (!empty($response['photos'])) {
    $response['success'] = true;
    $response['message'] = 'Foto berhasil diunggah';
} else {
    $response['message'] = 'No files were saved';
}

echo json_encode($response);

?>
