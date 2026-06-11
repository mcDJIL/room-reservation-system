<?php

include './actions/room/index.php';

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
        <a href="./index.php#daftar-ruangan" class="float-end text-decoration-none me-4">Reset filter</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Tabs Gedung -->
    <ul class="nav nav-tabs pb-2 border-0 justify-content-start gap-2 flex-wrap" id="roomTab" role="tablist">
      <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#panel-pasca" type="button" role="tab">Pascasarjana</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-d4" type="button" role="tab">Gedung D4</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-saw" type="button" role="tab">Gedung SAW</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#panel-sc" type="button" role="tab">Student Center</button></li>
    </ul>

    <div class="tab-content" id="roomTabContent">
    <?php 
    $mapping = [
        'pasca'  => 1,
        'd4'     => 2,
        'saw' => 3,
        'sc'    => 4,
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

      console.log(room);

      modalLabel.textContent = room.room_name;
      document.getElementById('room-detail-name').textContent = room.room_name;
      document.getElementById('room-detail-photo').src = room.photo_url;
      document.getElementById('room-detail-short').textContent = room.short_description || '-';
      document.getElementById('room-detail-capacity').textContent = `${room.capacity} orang`;
      document.getElementById('room-detail-building').textContent = room.building_name;
      document.getElementById('room-detail-building-card').textContent = room.building_name;

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