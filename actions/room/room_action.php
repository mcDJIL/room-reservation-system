<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'satset_db';

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Ambil ID ruangan dari URL
$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($room_id <= 0) {
    die("ID ruangan tidak valid.");
}

// Query detail ruangan beserta status pemakaian saat ini
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
mysqli_stmt_bind_param($stmt, "ssi", $current_date, $current_time, $room_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$room = mysqli_fetch_assoc($result);

if (!$room) {
    die("Ruangan tidak ditemukan atau tidak aktif.");
}

// Tentukan status ketersediaan sekarang
$status_label = $room['is_booked'] > 0 ? "Sedang Dipakai" : "Tersedia";
$status_class = $room['is_booked'] > 0 ? "danger" : "success";

// Path foto (relative dari actions/room ke folder uploads di root)
$photo_path = !empty($room['photo']) ? '../../uploads/' . $room['photo'] : '../../rooms/default-room.jpg';

// Helper untuk mendapatkan nama gedung
function getBuildingName(int $building_id) {
    switch($building_id) {
        case 1: return 'Gedung Pascasarjana';
        case 2: return 'Gedung D4';
        case 3: return 'Gedung Rektorat';
        case 4: return 'Gedung Laboratorium';
        case 5: return 'Gedung Perpustakaan';
        case 6: return 'Gedung Serbaguna';
        default: return 'Lainnya';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Ruangan - <?= htmlspecialchars($room['room_name']) ?> | Satset Meeting Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
        .detail-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .room-image {
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        .room-image img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
        .room-info-card {
            background: white;
            border-radius: 1.25rem;
            padding: 1.8rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
        }
        .room-name {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
        }
        .status-badge {
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .detail-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            color: #475569;
            margin-bottom: 1rem;
        }
        .facility-list {
            list-style: none;
            padding-left: 0;
        }
        .facility-list li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-book {
            background-color: #2563eb;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 40px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: 0.2s;
            border: none;
        }
        .btn-book:hover {
            background-color: #1d4ed8;
            color: white;
        }
        .btn-back {
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-back:hover {
            background-color: #e2e8f0;
        }
        @media (max-width: 768px) {
            .room-name {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="detail-container">
    <!-- Tombol kembali -->
    <div class="mb-4">
        <a href="javascript:history.back()" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="row g-4">
        <!-- Kolom Foto -->
        <div class="col-lg-6">
            <div class="room-image">
                <img src="<?= htmlspecialchars($photo_path) ?>" alt="<?= htmlspecialchars($room['room_name']) ?>" onerror="this.src='../../rooms/default-room.jpg'">
            </div>
        </div>

        <!-- Kolom Detail Informasi -->
        <div class="col-lg-6">
            <div class="room-info-card">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <h1 class="room-name"><?= htmlspecialchars($room['room_name']) ?></h1>
                    <span class="status-badge bg-<?= $status_class ?> bg-opacity-10 text-<?= $status_class ?> border border-<?= $status_class ?> border-opacity-25">
                        <?= $status_label ?>
                    </span>
                </div>
                
                <p class="text-muted mt-2"><?= nl2br(htmlspecialchars($room['short_description'])) ?></p>
                
                <hr class="my-3">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-label">Kapasitas</div>
                        <div class="detail-value">
                            <i class="fas fa-users me-2"></i> <?= (int)$room['capacity'] ?> orang
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">Lokasi Gedung</div>
                        <div class="detail-value">
                            <i class="fas fa-building me-2"></i> 
                            <?= htmlspecialchars(getBuildingName($room['building_id'])) ?>
                        </div>
                    </div>
                </div>
                
                <div class="detail-label mt-2">Fasilitas</div>
                <div class="detail-value">
                    <ul class="facility-list">
                        <?php 
                        $facilities = explode(',', $room['facility']);
                        foreach ($facilities as $fac):
                            $fac = trim($fac);
                            if (!empty($fac)):
                        ?>
                            <li><i class="fas fa-check-circle text-success"></i> <?= htmlspecialchars($fac) ?></li>
                        <?php 
                            endif;
                        endforeach;
                        ?>
                    </ul>
                </div>
                
                <?php if (!empty($room['long_description'])): ?>
                    <div class="detail-label mt-2">Deskripsi Lengkap</div>
                    <div class="detail-value">
                        <?= nl2br(htmlspecialchars($room['long_description'])) ?>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4 d-flex gap-3 flex-wrap">
                    <a href="../../pages/user/booking.php?id=<?= $room['id'] ?>" class="btn-book">
                        <i class="fas fa-calendar-check"></i> Booking Sekarang
                    </a>
                    <a href="../../index.php#daftar-ruangan" class="btn-back">
                        <i class="fas fa-th-large"></i> Lihat Semua Ruangan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>