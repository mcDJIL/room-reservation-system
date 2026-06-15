<?php
require_once __DIR__ . '/../../config/connection.php';

header('Content-Type: application/json; charset=utf-8');

$image_placeholder = "data:image/svg+xml;utf8," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500"><rect width="800" height="500" fill="#e2e8f0"/><rect x="70" y="70" width="660" height="360" rx="28" fill="#f8fafc" stroke="#cbd5e1" stroke-width="8"/><path d="M170 340l110-120 90 92 70-70 140 148H170z" fill="#cbd5e1"/><circle cx="290" cy="185" r="38" fill="#cbd5e1"/><text x="400" y="455" text-anchor="middle" font-family="Arial, sans-serif" font-size="28" fill="#64748b">Gambar ruangan belum tersedia</text></svg>');

function sendRoomResponse(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function getBuildingName(int $building_id): string
{
    switch ($building_id) {
        case 1:
            return 'Gedung Pascasarjana';
        case 2:
            return 'Gedung D4';
        case 3:
            return 'Gedung Rektorat';
        case 4:
            return 'Gedung Laboratorium';
        case 5:
            return 'Gedung Perpustakaan';
        case 6:
            return 'Gedung Serbaguna';
        default:
            return 'Lainnya';
    }
}

$room_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($room_id <= 0) {
    sendRoomResponse(400, [
        'success' => false,
        'message' => 'ID ruangan tidak valid.',
    ]);
}

$current_date = date('Y-m-d');
$current_time = date('H:i:s');

$sql = "SELECT r.*, p.photo,
        (SELECT COUNT(*) FROM reservations res
         WHERE res.room_id = r.id
         AND res.reservation_date = ?
         AND ? BETWEEN res.start_hour AND res.end_hour
         AND res.status = 'approved') as is_booked
        FROM rooms r
        LEFT JOIN room_photos p ON r.id = p.room_id AND p.is_primary = 1
        WHERE r.id = ? AND r.is_active = 1";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    sendRoomResponse(500, [
        'success' => false,
        'message' => 'Gagal menyiapkan query detail ruangan.',
    ]);
}

mysqli_stmt_bind_param($stmt, 'ssi', $current_date, $current_time, $room_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$room = mysqli_fetch_assoc($result);

if (!$room) {
    sendRoomResponse(404, [
        'success' => false,
        'message' => 'Ruangan tidak ditemukan atau tidak aktif.',
    ]);
}

$facilities = array_values(array_filter(array_map('trim', explode(',', (string) $room['facility']))));
$status_label = ((int) $room['is_booked'] > 0) ? 'Sedang Dipakai' : 'Tersedia';
$status_class = ((int) $room['is_booked'] > 0) ? 'danger' : 'success';
$photo_path = !empty($room['photo']) ? '../../assets/images/' . $room['photo'] : $image_placeholder;

sendRoomResponse(200, [
    'success' => true,
    'room' => [
        'id' => (int) $room['id'],
        'room_name' => $room['room_name'],
        'short_description' => $room['short_description'] ?? '',
        'long_description' => $room['long_description'] ?? '',
        'capacity' => (int) $room['capacity'],
        'building_name' => getBuildingName((int) $room['building_id']),
        'facility' => $room['facility'] ?? '',
        'facilities' => $facilities,
        'photo_url' => $photo_path,
        'status_label' => $status_label,
        'status_class' => $status_class,
        'booking_url' => 'pages/user/booking.php?room_id=' . (int) $room['id'],
    ],
]);