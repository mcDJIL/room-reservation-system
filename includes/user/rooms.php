<?php
// hero.php - Halaman utama dengan filter ruangan

require_once __DIR__ . '/../../config/connection.php';

// ---------- FILTER LOGIC (dengan checkbox facilities) ----------
$search         = isset($_GET['search']) ? trim($_GET['search']) : '';
$minCapacity    = isset($_GET['capacity']) && is_numeric($_GET['capacity']) ? (int)$_GET['capacity'] : 0;
$selectedFacilities = isset($_GET['facilities']) && is_array($_GET['facilities']) ? $_GET['facilities'] : [];
$selectedFacilities = array_map('trim', $selectedFacilities);
$selectedFacilities = array_filter($selectedFacilities);
$availability   = isset($_GET['availability']) ? trim($_GET['availability']) : '';

$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Base SQL with subquery for current booking status
$sql_rooms = "SELECT r.*, p.photo,
              (SELECT COUNT(*) FROM reservations res 
               WHERE res.room_id = r.id 
               AND res.reservation_date = ? 
               AND ? BETWEEN res.start_hour AND res.end_hour
               AND res.status = 'approved') as is_booked
              FROM rooms r 
              LEFT JOIN room_photos p ON r.id = p.room_id AND p.is_primary = 1
              WHERE r.is_active = 1";

$conditions = [];
$params = [$current_date, $current_time];
$types = "ss";

// 1. Search by room name
if (!empty($search)) {
    $conditions[] = "r.room_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

// 2. Minimum capacity
if ($minCapacity > 0) {
    $conditions[] = "r.capacity >= ?";
    $params[] = $minCapacity;
    $types .= "i";
}

// 3. Facility filter (harus memiliki SEMUA fasilitas yang dipilih)
if (!empty($selectedFacilities)) {
    foreach ($selectedFacilities as $fac) {
        $conditions[] = "r.facility LIKE ?";
        $params[] = "%" . $fac . "%";
        $types .= "s";
    }
}

// 4. Availability filter
if ($availability === 'available') {
    $conditions[] = "(SELECT COUNT(*) FROM reservations res 
                      WHERE res.room_id = r.id 
                      AND res.reservation_date = ? 
                      AND ? BETWEEN res.start_hour AND res.end_hour
                      AND res.status = 'approved') = 0";
    $params[] = $current_date;
    $params[] = $current_time;
    $types .= "ss";
} elseif ($availability === 'booked') {
    $conditions[] = "(SELECT COUNT(*) FROM reservations res 
                      WHERE res.room_id = r.id 
                      AND res.reservation_date = ? 
                      AND ? BETWEEN res.start_hour AND res.end_hour
                      AND res.status = 'approved') > 0";
    $params[] = $current_date;
    $params[] = $current_time;
    $types .= "ss";
}

if (count($conditions) > 0) {
    $sql_rooms .= " AND " . implode(" AND ", $conditions);
}

$stmt = mysqli_prepare($conn, $sql_rooms);
if (!$stmt) {
    die("SQL Error: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result_rooms = mysqli_stmt_get_result($stmt);
$rooms = mysqli_fetch_all($result_rooms, MYSQLI_ASSOC);

// SVG icons
$svg_people = '<svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 10.6667V8.8C0 8.42222 0.0972222 8.075 0.291667 7.75833C0.486111 7.44167 0.744444 7.2 1.06667 7.03333C1.75556 6.68889 2.45556 6.43056 3.16667 6.25833C3.87778 6.08611 4.6 6 5.33333 6C6.06667 6 6.78889 6.08611 7.5 6.25833C8.21111 6.43056 8.91111 6.68889 9.6 7.03333C9.92222 7.2 10.1806 7.44167 10.375 7.75833C10.5694 8.075 10.6667 8.42222 10.6667 8.8V10.6667H0ZM12 10.6667V8.66667C12 8.17778 11.8639 7.70833 11.5917 7.25833C11.3194 6.80833 10.9333 6.42222 10.4333 6.1C11 6.16667 11.5333 6.28056 12.0333 6.44167C12.5333 6.60278 13 6.8 13.4333 7.03333C13.8333 7.25556 14.1389 7.50278 14.35 7.775C14.5611 8.04722 14.6667 8.34444 14.6667 8.66667V10.6667H12ZM5.33333 5.33333C4.6 5.33333 3.97222 5.07222 3.45 4.55C2.92778 4.02778 2.66667 3.4 2.66667 2.66667C2.66667 1.93333 2.92778 1.30556 3.45 0.783333C3.97222 0.261111 4.6 0 5.33333 0C6.06667 0 6.69444 0.261111 7.21667 0.783333C7.73889 1.30556 8 1.93333 8 2.66667C8 3.4 7.73889 4.02778 7.21667 4.55C6.69444 5.07222 6.06667 5.33333 5.33333 5.33333ZM12 2.66667C12 3.4 11.7389 4.02778 11.2167 4.55C10.6944 5.07222 10.0667 5.33333 9.33333 5.33333C9.21111 5.33333 9.05556 5.31944 8.86667 5.29167C8.67778 5.26389 8.52222 5.23333 8.4 5.2C8.7 4.84444 8.93056 4.45 9.09167 4.01667C9.25278 3.58333 9.33333 3.13333 9.33333 2.66667C9.33333 2.2 9.25278 1.75 9.09167 1.31667C8.93056 0.883333 8.7 0.488889 8.4 0.133333C8.55556 0.0777778 8.71111 0.0416667 8.86667 0.025C9.02222 0.00833333 9.17778 0 9.33333 0C10.0667 0 10.6944 0.261111 11.2167 0.783333C11.7389 1.30556 12 1.93333 12 2.66667ZM1.33333 9.33333H9.33333V8.8C9.33333 8.67778 9.30278 8.56667 9.24167 8.46667C9.18056 8.36667 9.1 8.28889 9 8.23333C8.4 7.93333 7.79444 7.70833 7.18333 7.55833C6.57222 7.40833 5.95556 7.33333 5.33333 7.33333C4.71111 7.33333 4.09444 7.40833 3.48333 7.55833C2.87222 7.70833 2.26667 7.93333 1.66667 8.23333C1.56667 8.28889 1.48611 8.36667 1.425 8.46667C1.36389 8.56667 1.33333 8.67778 1.33333 8.8V9.33333ZM5.33333 4C5.7 4 6.01389 3.86944 6.275 3.60833C6.53611 3.34722 6.66667 3.03333 6.66667 2.66667C6.66667 2.3 6.53611 1.98611 6.275 1.725C6.01389 1.46389 5.7 1.33333 5.33333 1.33333C4.96667 1.33333 4.65278 1.46389 4.39167 1.725C4.13056 1.98611 4 2.3 4 2.66667C4 3.03333 4.13056 3.34722 4.39167 3.60833C4.65278 3.86944 4.96667 4 5.33333 4Z" fill="#444653"/></svg>';

$svg_video = '<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.33333 10.6667C0.966667 10.6667 0.652778 10.5361 0.391667 10.275C0.130556 10.0139 0 9.7 0 9.33333V1.33333C0 0.966667 0.130556 0.652778 0.391667 0.391667C0.652778 0.130556 0.966667 0 1.33333 0H9.33333C9.7 0 10.0139 0.130556 10.275 0.391667C10.5361 0.652778 10.6667 0.966667 10.6667 1.33333V4.33333L13.3333 1.66667V9L10.6667 6.33333V9.33333C10.6667 9.7 10.5361 10.0139 10.275 10.275C10.0139 10.5361 9.7 10.6667 9.33333 10.6667H1.33333ZM1.33333 9.33333H9.33333V1.33333H1.33333V9.33333ZM1.33333 9.33333V1.33333V9.33333Z" fill="#444653"/></svg>';

// Fungsi render kartu ruangan (tombol Detail ke actions/room/room_action.php)
function renderRoomCard(array $row, string $people_svg, string $video_svg): string {
    $img = !empty($row['photo']) ? 'uploads/' . $row['photo'] : "rooms/auditorium-lt6-1.jpg";
    $status_label = $row['is_booked'] > 0 ? "Sedang Dipakai" : "Tersedia";
    $status_class = $row['is_booked'] > 0 ? "status-booked" : "status-available";
    
    return '
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="room-card">
            <div class="room-card-img">
                <img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($row['room_name']) . '" loading="lazy">
                <span class="room-availability-badge ' . $status_class . '">' . $status_label . '</span>
            </div>
            <div class="room-card-body">
                <h3 class="room-name">' . htmlspecialchars($row['room_name']) . '</h3>
                <p class="room-desc">' . htmlspecialchars($row['short_description']) . '</p>
                <div class="room-tags">
                    <span class="room-tag">' . $people_svg . ' ' . $row['capacity'] . ' Orang</span>
                    <span class="room-tag">' . $video_svg . ' ' . htmlspecialchars($row['facility']) . '</span>
                </div>
                <div class="room-card-footer">
                    <a href="actions/room/room_action.php?id=' . $row['id'] . '" class="btn-room-detail">Detail</a>
                </div>
            </div>
        </div>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satset Meeting Room - Cari Ruangan Produktif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ========== FILTER BAR ========== */
        .filter-bar {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .filter-bar .form-label {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }
        .filter-bar .form-check-label {
            font-size: 0.85rem;
            color: #334155;
        }
        .filter-bar .btn-primary {
            background-color: #2563eb;
            border: none;
            font-weight: 600;
            transition: 0.2s;
        }
        .filter-bar .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
        }
        /* Alert filter aktif */
        .alert-info {
            background-color: #eef2ff;
            border-color: #c7d2fe;
            color: #1e40af;
            border-radius: 0.75rem;
            font-size: 0.9rem;
        }
        .alert-info a {
            color: #1e3a8a;
            font-weight: 600;
            text-decoration: none;
        }
        .alert-info a:hover {
            text-decoration: underline;
        }
        /* Responsive checkbox group */
        @media (max-width: 768px) {
            .filter-bar .d-flex {
                flex-direction: column;
                gap: 0.5rem !important;
            }
        }
        .tab-pane {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

<!-- Daftar Ruangan dengan Filter -->
<section class="rooms-section" id="daftar-ruangan">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="section-title mb-3">Daftar Ruangan</h2>
      <p class="section-subtitle mx-auto">
        Pilih ruangan yang sesuai dengan kebutuhan rapat atau acaramu
      </p>
    </div>

    <!-- FILTER FORM -->
    <div class="filter-bar mb-5 p-4">
      <form method="GET" action="" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label for="capacity" class="form-label fw-semibold">Kapasitas</label>
          <select class="form-select" id="capacity" name="capacity">
            <option value="0" <?= $minCapacity == 0 ? 'selected' : '' ?>>Semua kapasitas</option>
            <option value="10" <?= $minCapacity == 10 ? 'selected' : '' ?>>10+ orang</option>
            <option value="20" <?= $minCapacity == 20 ? 'selected' : '' ?>>20+ orang</option>
            <option value="30" <?= $minCapacity == 30 ? 'selected' : '' ?>>30+ orang</option>
            <option value="50" <?= $minCapacity == 50 ? 'selected' : '' ?>>50+ orang</option>
            <option value="100" <?= $minCapacity == 100 ? 'selected' : '' ?>>100+ orang</option>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold d-block">Fasilitas</label>
          <div class="d-flex flex-wrap gap-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="facilities[]" value="Projector" id="facProyektor"
                <?= in_array('Projector', $selectedFacilities) ? 'checked' : '' ?>>
              <label class="form-check-label" for="facProyektor">Proyektor</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="facilities[]" value="AC" id="facAC"
                <?= in_array('AC', $selectedFacilities) ? 'checked' : '' ?>>
              <label class="form-check-label" for="facAC">AC</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="facilities[]" value="Sound System" id="facSound"
                <?= in_array('Sound System', $selectedFacilities) ? 'checked' : '' ?>>
              <label class="form-check-label" for="facSound">Sound System</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="facilities[]" value="Whiteboard" id="facWhiteboard"
                <?= in_array('Whiteboard', $selectedFacilities) ? 'checked' : '' ?>>
              <label class="form-check-label" for="facWhiteboard">Whiteboard</label>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <label for="availability" class="form-label fw-semibold">Ketersediaan</label>
          <select class="form-select" id="availability" name="availability">
            <option value="">Semua</option>
            <option value="available" <?= $availability == 'available' ? 'selected' : '' ?>>Tersedia sekarang</option>
            <option value="booked" <?= $availability == 'booked' ? 'selected' : '' ?>>Sedang dipakai</option>
          </select>
        </div>

        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100 py-2">Cari Ruangan</button>
        </div>
      </form>
    </div>

    <!-- Alert filter aktif -->
    <?php if (!empty($selectedFacilities)): ?>
      <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
        <strong>Filter fasilitas aktif:</strong> <?= implode(', ', $selectedFacilities) ?>
        <a href="hero.php" class="float-end text-decoration-none me-4">Reset filter</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Tabs Gedung -->
    <ul class="nav nav-tabs pb-2 border-0 justify-content-start gap-2 flex-wrap" id="roomTab" role="tablist">
      <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#panel-pasca" type="button" role="tab">Pascasarjana</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-d4" type="button" role="tab">Gedung D4</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-rektor" type="button" role="tab">Rektorat</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-lab" type="button" role="tab">Laboratorium</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-perpus" type="button" role="tab">Perpustakaan</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-gsg" type="button" role="tab">Serbaguna</button></li>
    </ul>

    <div class="tab-content" id="roomTabContent">
    <?php 
    $mapping = [
        'pasca'  => 1,
        'd4'     => 2,
        'rektor' => 3,
        'lab'    => 4,
        'perpus' => 5,
        'gsg'    => 6
    ];
    $first = true;
    foreach ($mapping as $tabId => $idGedung): ?>
      <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="panel-<?= $tabId ?>" role="tabpanel">
        <div class="row g-4">
          <?php 
          $found = 0;
          foreach ($rooms as $room) {
              if ($room['building_id'] == $idGedung) {
                  echo renderRoomCard($room, $svg_people, $svg_video);
                  $found++;
              }
          }
          if ($found === 0) {
              echo "<div class='col-12 text-center text-muted py-5'><i class='fas fa-building fa-2x mb-2 d-block'></i><p>Tidak ada ruangan tersedia di gedung ini.</p></div>";
          }
          ?>
        </div>
      </div>
    <?php $first = false; endforeach; ?>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>