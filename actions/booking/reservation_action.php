<?php
session_start();
require_once '../../config/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

if (empty($_SESSION['is_login']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) { echo json_encode(['success' => false, 'message' => 'ID tidak valid']); exit(); }

    $stmt = $conn->prepare('DELETE FROM reservations WHERE id = ?');
    $stmt->bind_param('i', $id);
    echo json_encode(['success' => $stmt->execute(), 'message' => $stmt->error]);
    $stmt->close();
    exit();
}

// ── ADD ───────────────────────────────────────────────────────────────────────
if ($action === 'add') {
    $user_id  = intval($_POST['user_id']  ?? 0);
    $room_id  = intval($_POST['room_id']  ?? 0);
    $date     = trim($_POST['reservation_date'] ?? '');
    $start    = trim($_POST['start_hour']  ?? '');
    $end      = trim($_POST['end_hour']    ?? '');
    $reason   = trim($_POST['reason']      ?? '');
    $status   = in_array($_POST['status'] ?? '', ['waiting','approved','rejected'])
                ? $_POST['status'] : 'waiting';

    if (!$user_id || !$room_id || !$date || !$start || !$end) {
        echo json_encode(['success' => false, 'message' => 'Field wajib tidak lengkap']); exit();
    }

    $approved_by = ($status === 'approved') ? intval($_SESSION['user_id']) : null;

    $stmt = $conn->prepare(
        'INSERT INTO reservations (user_id, room_id, reservation_date, start_hour, end_hour, reason, status, approved_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('iisssssi', $user_id, $room_id, $date, $start, $end, $reason, $status, $approved_by);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => $stmt->error]); $stmt->close(); exit();
    }
    $new_id = $stmt->insert_id;
    $stmt->close();

    // Return the full row for instant table insert
    $row = $conn->query("
        SELECT r.id, u.name AS user_name, u.email AS user_email,
               b.name AS building_name, rm.room_name,
               r.reservation_date, r.start_hour, r.end_hour,
               r.reason, r.status, r.user_id, r.room_id
        FROM reservations r
        JOIN users u  ON u.id  = r.user_id
        JOIN rooms rm ON rm.id = r.room_id
        JOIN buildings b ON b.id = rm.building_id
        WHERE r.id = $new_id LIMIT 1
    ")->fetch_assoc();

    echo json_encode(['success' => true, 'row' => $row]);
    exit();
}

// ── EDIT ──────────────────────────────────────────────────────────────────────
if ($action === 'edit') {
    $id      = intval($_POST['id']      ?? 0);
    $user_id = intval($_POST['user_id'] ?? 0);
    $room_id = intval($_POST['room_id'] ?? 0);
    $date    = trim($_POST['reservation_date'] ?? '');
    $start   = trim($_POST['start_hour']  ?? '');
    $end     = trim($_POST['end_hour']    ?? '');
    $reason  = trim($_POST['reason']      ?? '');
    $status  = in_array($_POST['status'] ?? '', ['waiting','approved','rejected'])
               ? $_POST['status'] : 'waiting';

    if (!$id || !$user_id || !$room_id || !$date || !$start || !$end) {
        echo json_encode(['success' => false, 'message' => 'Field wajib tidak lengkap']); exit();
    }

    $approved_by = ($status === 'approved') ? intval($_SESSION['user_id']) : null;

    $stmt = $conn->prepare(
        'UPDATE reservations SET user_id=?, room_id=?, reservation_date=?, start_hour=?, end_hour=?, reason=?, status=?, approved_by=?
         WHERE id=?'
    );
    $stmt->bind_param('iisssssii', $user_id, $room_id, $date, $start, $end, $reason, $status, $approved_by, $id);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => $stmt->error]); $stmt->close(); exit();
    }
    $stmt->close();

    $row = $conn->query("
        SELECT r.id, u.name AS user_name, u.email AS user_email,
               b.name AS building_name, rm.room_name,
               r.reservation_date, r.start_hour, r.end_hour,
               r.reason, r.status, r.user_id, r.room_id
        FROM reservations r
        JOIN users u  ON u.id  = r.user_id
        JOIN rooms rm ON rm.id = r.room_id
        JOIN buildings b ON b.id = rm.building_id
        WHERE r.id = $id LIMIT 1
    ")->fetch_assoc();

    echo json_encode(['success' => true, 'row' => $row]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Action tidak dikenal']);
