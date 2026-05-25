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
$image_placeholder = "data:image/svg+xml;utf8," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500"><rect width="800" height="500" fill="#e2e8f0"/><rect x="70" y="70" width="660" height="360" rx="28" fill="#f8fafc" stroke="#cbd5e1" stroke-width="8"/><path d="M170 340l110-120 90 92 70-70 140 148H170z" fill="#cbd5e1"/><circle cx="290" cy="185" r="38" fill="#cbd5e1"/><text x="400" y="455" text-anchor="middle" font-family="Arial, sans-serif" font-size="28" fill="#64748b">Gambar ruangan belum tersedia</text></svg>');

// Fungsi render kartu ruangan (tombol Detail ke actions/room/room_action.php)
function renderRoomCard(array $row, string $people_svg, string $video_svg, string $image_placeholder): string {
  $img = !empty($row['photo']) ? 'uploads/' . $row['photo'] : $image_placeholder;
    
    return '
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="room-card">
            <div class="room-card-img">
          <img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($row['room_name']) . '" loading="lazy" onerror="this.onerror=null;this.src=\'' . htmlspecialchars($image_placeholder, ENT_QUOTES) . '\';">
            </div>
            <div class="room-card-body">
                <h3 class="room-name">' . htmlspecialchars($row['room_name']) . '</h3>
                <p class="room-desc">' . htmlspecialchars($row['short_description']) . '</p>
                <div class="room-tags">
                    <span class="room-tag">' . $people_svg . ' ' . $row['capacity'] . ' Orang</span>
                    <span class="room-tag">' . $video_svg . ' ' . htmlspecialchars($row['facility']) . '</span>
                </div>
                <div class="room-card-footer">
                  <button type="button" class="btn-room-detail" data-room-id="' . (int) $row['id'] . '">Detail</button>
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
                  echo renderRoomCard($room, $svg_people, $svg_video, $image_placeholder);
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

<!-- Modal Detail Ruangan -->
<div class="modal fade room-detail-modal" id="roomDetailModal" tabindex="-1" aria-labelledby="roomDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <div class="room-detail-kicker">Detail Ruangan</div>
          <h5 class="modal-title mb-0" id="roomDetailModalLabel">Memuat detail...</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 p-lg-5">
        <div id="room-detail-loading" class="room-detail-loading">
          <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status" aria-hidden="true"></div>
            <div>Memuat detail ruangan...</div>
          </div>
        </div>

        <div id="room-detail-error" class="alert alert-danger d-none mb-0" role="alert"></div>

        <div id="room-detail-content" class="d-none">
          <div class="row g-4 align-items-start">
            <div class="col-lg-6">
              <div class="room-detail-hero mb-3">
                <img id="room-detail-photo" src="" alt="Detail ruangan" onerror="this.onerror=null;this.src='<?= htmlspecialchars($image_placeholder, ENT_QUOTES) ?>'">
              </div>
              <div id="room-detail-status" class="room-detail-status mb-3"></div>
              <p id="room-detail-short" class="text-secondary mb-0"></p>
            </div>

            <div class="col-lg-6">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-3 flex-wrap">
                <div>
                  <h3 id="room-detail-name" class="h2 mb-1"></h3>
                  <p id="room-detail-building" class="text-muted mb-0"></p>
                </div>
              </div>

              <div class="room-detail-meta mb-4">
                <div class="room-detail-meta-card">
                  <div class="room-detail-meta-label">Kapasitas</div>
                  <div id="room-detail-capacity" class="fw-semibold"></div>
                </div>
                <div class="room-detail-meta-card">
                  <div class="room-detail-meta-label">Gedung</div>
                  <div id="room-detail-building-card" class="fw-semibold"></div>
                </div>
              </div>

              <div class="mb-4">
                <div class="room-detail-meta-label mb-2">Fasilitas</div>
                <div id="room-detail-facilities" class="room-detail-facilities"></div>
              </div>

              <div id="room-detail-long-wrap" class="mb-4 d-none">
                <div class="room-detail-meta-label mb-2">Deskripsi Lengkap</div>
                <p id="room-detail-long" class="text-secondary mb-0"></p>
              </div>

              <div class="room-detail-actions d-flex gap-2 flex-wrap">
                <a id="room-detail-booking" href="#" class="btn btn-primary">Booking Sekarang</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('click', async (event) => {
    const trigger = event.target.closest('.btn-room-detail');
    if (!trigger) {
      return;
    }

    event.preventDefault();

    const roomId = trigger.getAttribute('data-room-id');
    const modalElement = document.getElementById('roomDetailModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const loading = document.getElementById('room-detail-loading');
    const errorBox = document.getElementById('room-detail-error');
    const content = document.getElementById('room-detail-content');
    const modalLabel = document.getElementById('roomDetailModalLabel');

    const resetState = () => {
      modalLabel.textContent = 'Memuat detail...';
      loading.classList.remove('d-none');
      errorBox.classList.add('d-none');
      content.classList.add('d-none');
    };

    const showError = (message) => {
      loading.classList.add('d-none');
      content.classList.add('d-none');
      errorBox.textContent = message;
      errorBox.classList.remove('d-none');
    };

    resetState();
    modal.show();

    try {
      const response = await fetch(`actions/room/room_action.php?id=${encodeURIComponent(roomId)}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const payload = await response.json();

      if (!response.ok || !payload.success) {
        showError(payload.message || 'Gagal memuat detail ruangan.');
        return;
      }

      const room = payload.room;

      modalLabel.textContent = room.room_name;
      document.getElementById('room-detail-name').textContent = room.room_name;
      document.getElementById('room-detail-photo').src = room.photo_url;
      document.getElementById('room-detail-short').textContent = room.short_description || '-';
      document.getElementById('room-detail-capacity').textContent = `${room.capacity} orang`;
      document.getElementById('room-detail-building').textContent = room.building_name;
      document.getElementById('room-detail-building-card').textContent = room.building_name;

      const status = document.getElementById('room-detail-status');
      status.className = `room-detail-status bg-${room.status_class} bg-opacity-10 text-${room.status_class}`;
      status.textContent = room.status_label;

      const facilities = document.getElementById('room-detail-facilities');
      facilities.innerHTML = '';
      if (room.facilities && room.facilities.length > 0) {
        room.facilities.forEach((facility) => {
          const badge = document.createElement('span');
          badge.className = 'room-detail-facility';
          badge.textContent = facility;
          facilities.appendChild(badge);
        });
      } else {
        const empty = document.createElement('span');
        empty.className = 'text-muted';
        empty.textContent = 'Belum ada fasilitas yang dicatat.';
        facilities.appendChild(empty);
      }

      const longWrap = document.getElementById('room-detail-long-wrap');
      const longDescription = (room.long_description || '').trim();
      if (longDescription) {
        document.getElementById('room-detail-long').textContent = longDescription;
        longWrap.classList.remove('d-none');
      } else {
        longWrap.classList.add('d-none');
      }

      document.getElementById('room-detail-booking').href = room.booking_url;
      const bookingBtn = document.getElementById('room-detail-booking');
      bookingBtn.onclick = function(e) {
        e.preventDefault();
        const url = this.href;
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.5s ease';
        setTimeout(function() {
          window.location.href = url;
        }, 500);
      };

      loading.classList.add('d-none');
      errorBox.classList.add('d-none');
      content.classList.remove('d-none');
    } catch (error) {
      showError('Tidak bisa memuat detail ruangan. Coba lagi sebentar.');
    }
  });
</script>
</body>
</html>