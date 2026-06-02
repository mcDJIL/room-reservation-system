<?php
header('Content-Type: application/json; charset=utf-8');
// Endpoint menerima `room_id` dan files[] (input name: photos[])

require_once __DIR__ . '/../../config/connection.php';

$response = ['success' => false, 'message' => '', 'photos' => []];

function log_upload_error($message, array $context = [])
{
    $logLine = '[room-upload] ' . $message;
    if (!empty($context)) {
        $logLine .= ' | ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    error_log($logLine);
}

function bytes_to_human($bytes)
{
    $bytes = (int) $bytes;
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

function parse_ini_size_to_bytes($size)
{
    $size = trim((string) $size);
    if ($size === '') return 0;

    $unit = strtolower(substr($size, -1));
    $value = (int) $size;

    switch ($unit) {
        case 'g':
            return $value * 1024 * 1024 * 1024;
        case 'm':
            return $value * 1024 * 1024;
        case 'k':
            return $value * 1024;
        default:
            return (int) $size;
    }
}

function file_upload_error_message($errorCode)
{
    $uploadLimit = bytes_to_human(parse_ini_size_to_bytes(ini_get('upload_max_filesize')));
    $postLimit = bytes_to_human(parse_ini_size_to_bytes(ini_get('post_max_size')));

    switch ((int) $errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return 'Ukuran file melebihi batas upload server (upload_max_filesize: ' . $uploadLimit . ').';
        case UPLOAD_ERR_FORM_SIZE:
            return 'Ukuran file melebihi batas form upload.';
        case UPLOAD_ERR_PARTIAL:
            return 'File hanya terunggah sebagian.';
        case UPLOAD_ERR_NO_FILE:
            return 'Tidak ada file yang dipilih.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Folder temporary upload tidak ditemukan di server.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Server gagal menulis file ke disk.';
        case UPLOAD_ERR_EXTENSION:
            return 'Upload dihentikan oleh ekstensi PHP.';
        default:
            return 'Gagal upload file. Batas saat ini: upload_max_filesize=' . $uploadLimit . ', post_max_size=' . $postLimit . '.';
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Method not allowed';
    echo json_encode($response);
    exit;
}

$contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
$postMaxBytes = parse_ini_size_to_bytes(ini_get('post_max_size'));
if ($postMaxBytes > 0 && $contentLength > $postMaxBytes) {
    $response['message'] = 'Total ukuran upload melebihi batas server (post_max_size: ' . bytes_to_human($postMaxBytes) . ').';
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
        log_upload_error('Failed to create upload directory', [
            'uploadDir' => $uploadDir,
            'lastError' => error_get_last(),
        ]);
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

$uploadErrors = [];

// Check existing primary
$hasPrimary = false;
$q = mysqli_prepare($conn, 'SELECT COUNT(*) FROM room_photos WHERE room_id = ? AND is_primary = 1');
if ($q) {
    mysqli_stmt_bind_param($q, 'i', $room_id);
    mysqli_stmt_execute($q);
    mysqli_stmt_bind_result($q, $cnt);
    if (mysqli_stmt_fetch($q) && $cnt > 0) $hasPrimary = true;
    mysqli_stmt_close($q);
} else {
    log_upload_error('Failed to prepare primary photo check query', [
        'room_id' => $room_id,
        'dbError' => mysqli_error($conn),
    ]);
}

foreach ($files as $index => $file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = file_upload_error_message($file['error']);
        $uploadErrors[] = $errorMessage;
        log_upload_error('Upload error from PHP file array', [
            'room_id' => $room_id,
            'fileName' => $file['name'] ?? '',
            'tmpName' => $file['tmp_name'] ?? '',
            'size' => $file['size'] ?? null,
            'type' => $file['type'] ?? '',
            'errorCode' => $file['error'],
            'message' => $errorMessage,
        ]);
        continue;
    }
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
            if (!mysqli_stmt_execute($ins)) {
                log_upload_error('Failed to insert uploaded photo into database', [
                    'room_id' => $room_id,
                    'photo' => $dbPath,
                    'is_primary' => $is_primary,
                    'dbError' => mysqli_stmt_error($ins),
                ]);
                $uploadErrors[] = 'Gagal menyimpan data foto ke database.';
                mysqli_stmt_close($ins);
                @unlink($targetPath);
                continue;
            }
            $photoId = mysqli_insert_id($conn);
            mysqli_stmt_close($ins);

            $response['photos'][] = ['id' => $photoId, 'photo' => $dbPath, 'is_primary' => $is_primary];
        } else {
            log_upload_error('Failed to prepare photo insert query', [
                'room_id' => $room_id,
                'photo' => $dbPath,
                'dbError' => mysqli_error($conn),
            ]);
            $uploadErrors[] = 'Gagal menyiapkan query database.';
            @unlink($targetPath);
        }
    } else {
        log_upload_error('move_uploaded_file failed', [
            'room_id' => $room_id,
            'fileName' => $file['name'] ?? '',
            'tmpName' => $file['tmp_name'] ?? '',
            'targetPath' => $targetPath,
            'size' => $file['size'] ?? null,
            'errorCode' => $file['error'] ?? null,
            'is_uploaded_file' => isset($file['tmp_name']) ? is_uploaded_file($file['tmp_name']) : null,
            'lastError' => error_get_last(),
        ]);
        $uploadErrors[] = 'Gagal memindahkan file upload ke folder tujuan.';
    }
}

mysqli_close($conn);

if (!empty($response['photos'])) {
    $response['success'] = true;
    $response['message'] = 'Foto berhasil diunggah';
    if (!empty($uploadErrors)) {
        $response['message'] .= ' (Sebagian file gagal: ' . implode(' | ', array_unique($uploadErrors)) . ')';
    }
} else {
    if (!empty($uploadErrors)) {
        $response['message'] = implode(' | ', array_unique($uploadErrors));
    } else {
        $response['message'] = 'No files were saved';
    }
}

echo json_encode($response);

?>
