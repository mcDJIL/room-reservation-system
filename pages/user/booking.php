<?php
session_start();

if(!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
  header("Location: ../../pages/auth/login.php");
  exit();
}
// date_default_timezone_set('Asia/Jakarta');
$page_title = 'Reservasi Ruangan – SatSet';

// $conn = mysqli_connect("localhost", "root", "", "db_satset");

// if (!$conn) {
//     die("Koneksi ke database gagal: " . mysqli_connect_error());
// }

// // Ambil tanggal hari ini untuk filter data
// $tanggal_hari_ini = date('Y-m-d');

// // Query untuk mengambil reservasi yang aktif hari ini
// $query = "SELECT keperluan, mulai, selesai FROM reservasi 
//           WHERE tanggal = '$tanggal_hari_ini' 
//           ORDER BY mulai ASC";
// $result = mysqli_query($conn, $query);

// // Simpan data dari database ke dalam array PHP
// $list_agenda = [];
// while ($row = mysqli_fetch_assoc($result)) {
//     $list_agenda[] = $row;
// }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/css/user/landing.css">
  <link rel="stylesheet" href="../../assets/css/user/booking.css">
</head>
<body>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="landing-navbar navbar navbar-expand-lg">
  <div class="container-fluid px-0">
    <a class="navbar-brand" href="../../index.php" style="color: #164d6d;">
      <img src="../../assets/images/logo.png" alt="SatSet" height="40" style="margin-right:6px;" onerror="this.style.display='none'">
      SatSet
    </a>

    <button class="navbar-toggler border-0 shadow-none" type="button"
            data-bs-toggle="collapse" data-bs-target="#landingNav"
            aria-controls="landingNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="landingNav">
      <ul class="navbar-nav mx-auto gap-lg-3">
        <li class="nav-item">
          <a class="nav-link" href="../../index.php">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../index.php#alur-peminjaman">Alur Peminjaman</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../index.php#daftar-ruangan">Ruangan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="../../pages/user/booking.php">Reservasi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../pages/user/history.php">Riwayat</a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
        <a href="../../pages/auth/register.php" class="nav-register">Register</a>
        <div class="nav-divider d-none d-lg-block"></div>
        <a href="../../pages/auth/login.php" class="btn-login">Login</a>
      </div>
    </div>
  </div>
</nav>

<main class="booking-page">
  <div class="booking-container">

    <h1 class="booking-page-title">Reservasi Ruangan</h1>

    <div class="row g-4 booking-layout">

      <!-- ===== LEFT: Booking Form ===== -->
      <div class="col-12 col-lg-4 d-flex flex-column gap-4">

        <!-- Detail Pemesanan Card -->
        <div class="booking-form-card">
          <h3 class="form-section-title">Detail Pemesanan</h3>

          <form class="booking-form" action="../../actions/booking/booking_action.php" method="POST">

            <!-- Pilih Gedung -->
            <div class="form-field-group">
              <label class="field-label" for="gedung">Pilih Gedung</label>
              <div class="select-wrapper">
                <select class="field-select" id="gedung" name="gedung" required>
                  <option value="" disabled selected hidden>Pilih gedung</option>
                  <option value="hq">Gedung Pusat (HQ)</option>
                  <option value="branch1">Gedung Cabang 1</option>
                  <option value="branch2">Gedung Cabang 2</option>
                </select>
                <span class="error-message">Harap isi bidang ini</span>
                <span class="select-chevron">
                  <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                    <path d="M6 7.4L0 1.4L1.4 0L6 4.6L10.6 0L12 1.4L6 7.4Z" fill="#505F76"/>
                  </svg>
                </span>
              </div>
            </div>

            <!-- Pilih Ruangan -->
            <div class="form-field-group">
              <label class="field-label" for="ruangan">Pilih Ruangan</label>
              <div class="select-wrapper">
                <select class="field-select" id="ruangan" name="ruangan" required>
                  <option value="" disabled selected hidden>Pilih ruangan</option>
                  <option value="borobudur">Ruang Meeting Borobudur (8 Org)</option>
                  <option value="prambanan">Ruang Meeting Prambanan (12 Org)</option>
                  <option value="dieng">Ruang Meeting Dieng (6 Org)</option>
                </select>
                <span class="error-message">Harap isi bidang ini</span>
                <span class="select-chevron">
                  <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                    <path d="M6 7.4L0 1.4L1.4 0L6 4.6L10.6 0L12 1.4L6 7.4Z" fill="#505F76"/>
                  </svg>
                </span>
              </div>
            </div>

            <!-- Tanggal -->
            <div class="form-field-group">
              <label class="field-label" for="tanggal">Tanggal</label>
              <input class="field-input" type="date" id="tanggal" name="tanggal"
                     value="<?= date('Y-m-d'); ?>" min="<?= date('Y-m-d'); ?>">
            </div>

            <!-- Waktu Mulai & Selesai -->
            <div class="row g-3 mb-0">
              <div class="col-6">
                <div class="form-field-group mb-0">
                  <label class="field-label" for="mulai">Mulai</label>
                  <input class="field-input" type="time" id="mulai" name="mulai" min="08:00" max="18:00" required>
                </div>
              </div>
              <div class="col-6">
                <div class="form-field-group mb-0">
                  <label class="field-label" for="selesai">Selesai</label>
                  <input class="field-input" type="time" id="selesai" name="selesai" min="08:00" max="18:00" required>
                </div>
              </div>
            </div>

            <button type="submit" class="btn-konfirmasi">Konfirmasi Pemesanan</button>

          </form>
        </div>

        <!-- Room Preview Card -->
        <div class="room-preview-card">
          <div class="room-preview-img">
            <img src="../../assets/static/images/bg.jpg" alt="Ruang Borobudur">
          </div>
          <div class="room-preview-info">
            <div class="room-preview-header">
              <h4 class="room-preview-name">Ruang Borobudur</h4>
              <span class="availability-badge available">&#9679; TERSEDIA</span>
            </div>
            <div class="room-preview-tags">
              <span class="room-feature-tag">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                  <path d="M0 7V6.08125C0 5.66319 0.213889 5.32292 0.641667 5.06042C1.06944 4.79792 1.63333 4.66667 2.33333 4.66667C2.45972 4.66667 2.58125 4.6691 2.69792 4.67396C2.81458 4.67882 2.92639 4.69097 3.03333 4.71042C2.89722 4.91458 2.79514 5.12847 2.72708 5.35208C2.65903 5.57569 2.625 5.80903 2.625 6.05208V7H0ZM3.5 7V6.05208C3.5 5.74097 3.58507 5.4566 3.75521 5.19896C3.92535 4.94132 4.16597 4.71528 4.47708 4.52083C4.78819 4.32639 5.16007 4.18056 5.59271 4.08333C6.02535 3.98611 6.49444 3.9375 7 3.9375C7.51528 3.9375 7.98924 3.98611 8.42188 4.08333C8.85451 4.18056 9.22639 4.32639 9.5375 4.52083C9.84861 4.71528 10.0868 4.94132 10.2521 5.19896C10.4174 5.4566 10.5 5.74097 10.5 6.05208V7H3.5ZM11.375 7V6.05208C11.375 5.79931 11.3434 5.56111 11.2802 5.3375C11.217 5.11389 11.1222 4.90486 10.9958 4.71042C11.1028 4.69097 11.2122 4.67882 11.324 4.67396C11.4358 4.6691 11.55 4.66667 11.6667 4.66667C12.3667 4.66667 12.9306 4.79549 13.3583 5.05312C13.7861 5.31076 14 5.65347 14 6.08125V7H11.375ZM7 3.5C6.51389 3.5 6.10069 3.32986 5.76042 2.98958C5.42014 2.64931 5.25 2.23611 5.25 1.75C5.25 1.25417 5.42014 0.838542 5.76042 0.503125C6.10069 0.167708 6.51389 0 7 0C7.49583 0 7.91146 0.167708 8.24687 0.503125C8.58229 0.838542 8.75 1.25417 8.75 1.75C8.75 2.23611 8.58229 2.64931 8.24687 2.98958C7.91146 3.32986 7.49583 3.5 7 3.5Z" fill="#505F76"/>
                </svg>
                8 Orang
              </span>
              <span class="room-feature-tag">
                <svg width="14" height="10" viewBox="0 0 14 10" fill="none">
                  <path d="M7 9.91667C6.59167 9.91667 6.24653 9.77569 5.96458 9.49375C5.68264 9.21181 5.54167 8.86667 5.54167 8.45833C5.54167 8.05 5.68264 7.70486 5.96458 7.42292C6.24653 7.14097 6.59167 7 7 7C7.40833 7 7.75347 7.14097 8.03542 7.42292C8.31736 7.70486 8.45833 8.05 8.45833 8.45833C8.45833 8.86667 8.31736 9.21181 8.03542 9.49375C7.75347 9.77569 7.40833 9.91667 7 9.91667ZM3.70417 6.62083L2.47917 5.36667C3.05278 4.79306 3.72604 4.33854 4.49896 4.00313C5.27187 3.66771 6.10556 3.5 7 3.5C7.89444 3.5 8.72812 3.67014 9.50104 4.01042C10.274 4.35069 10.9472 4.8125 11.5208 5.39583L10.2958 6.62083C9.86806 6.19306 9.37222 5.85764 8.80833 5.61458C8.24444 5.37153 7.64167 5.25 7 5.25C6.35833 5.25 5.75556 5.37153 5.19167 5.61458C4.62778 5.85764 4.13194 6.19306 3.70417 6.62083ZM1.225 4.14167L0 2.91667C0.894444 2.00278 1.93958 1.28819 3.13542 0.772917C4.33125 0.257639 5.61944 0 7 0C8.38056 0 9.66875 0.257639 10.8646 0.772917C12.0604 1.28819 13.1056 2.00278 14 2.91667L12.775 4.14167C12.0264 3.39306 11.1587 2.80729 10.1719 2.38438C9.18507 1.96146 8.12778 1.75 7 1.75C5.87222 1.75 4.81493 1.96146 3.82812 2.38438C2.84132 2.80729 1.97361 3.39306 1.225 4.14167Z" fill="#505F76"/>
                </svg>
                WiFi
              </span>
              <span class="room-feature-tag">
                <svg width="12" height="11" viewBox="0 0 12 11" fill="none">
                  <path d="M3.5 10.5V9.33333H1.16667C0.845833 9.33333 0.571181 9.2191 0.342708 8.99063C0.114236 8.76215 0 8.4875 0 8.16667V1.16667C0 0.845833 0.114236 0.571181 0.342708 0.342708C0.571181 0.114236 0.845833 0 1.16667 0H10.5C10.8208 0 11.0955 0.114236 11.324 0.342708C11.5524 0.571181 11.6667 0.845833 11.6667 1.16667V8.16667C11.6667 8.4875 11.5524 8.76215 11.324 8.99063C11.0955 9.2191 10.8208 9.33333 10.5 9.33333H8.16667V10.5H3.5ZM1.16667 8.16667H10.5V1.16667H1.16667V8.16667Z" fill="#505F76"/>
                </svg>
                Projector
              </span>
            </div>
          </div>
        </div>

      </div>

      <!-- ===== RIGHT: Schedule Visualizer ===== -->
      <div class="col-12 col-lg-8">
        <div class="schedule-card">

          <!-- Schedule Header -->
          <div class="schedule-header">
            <div class="schedule-header-info">
              <h3 class="schedule-title">Jadwal Ruangan</h3>
              <p class="schedule-date" id="schedule-date-display"></p>
            </div>
            <div class="schedule-nav-buttons">
              <button class="schedule-nav-btn" aria-label="Hari sebelumnya">
                <svg width="8" height="12" viewBox="0 0 8 12" fill="none">
                  <path d="M6 12L0 6L6 0L7.4 1.4L2.8 6L7.4 10.6L6 12Z" fill="#505F76"/>
                </svg>
              </button>
              <button class="schedule-nav-btn" aria-label="Hari berikutnya">
                <svg width="8" height="12" viewBox="0 0 8 12" fill="none">
                  <path d="M4.6 6L0 1.4L1.4 0L7.4 6L1.4 12L0 10.6L4.6 6Z" fill="#505F76"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Timeline Grid -->
          <div class="timeline-scroll-wrapper">
            <div class="timeline-grid">

              <!-- Time Labels Column -->
              <div class="timeline-labels">
                <div class="time-slot-label">08:00</div>
                <div class="time-slot-label">09:00</div>
                <div class="time-slot-label">10:00</div>
                <div class="time-slot-label">11:00</div>
                <div class="time-slot-label">12:00</div>
                <div class="time-slot-label">13:00</div>
                <div class="time-slot-label">14:00</div>
                <div class="time-slot-label">15:00</div>
                <div class="time-slot-label">16:00</div>
                <div class="time-slot-label">17:00</div>
                <div class="time-slot-label">18:00</div>
              </div>

              <!-- Events Area -->
              <div class="timeline-events-area">
                <!-- Hour grid lines -->
                <div class="timeline-hour-lines">
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                  <div class="hour-line"></div>
                </div>

                <!-- Current Time Indicator: 11:00 = 3 hours after 08:00 → top: 3 × 64px = 192px -->
                <div class="current-time-indicator" id="live-time-indicator">
                  <span class="current-time-badge" id="live-time-badge">00:00 SEKARANG</span>
                </div>

                <!-- Booked: Weekly Sync - Marketing 09:00–10:30
                     top = 1×64 = 64px, height = 1.5×64 = 96px -->
                <div class="timeline-event booked-event"
                     style="top: 64px; height: 96px;">
                  <p class="event-title">Weekly Sync – Marketing</p>
                  <p class="event-meta">09:00 – 10:30 &bull; Rina Pratama</p>
                </div>

                <!-- Booked: Review Budget 12:30–13:30
                     top = 4.5×64 = 288px, height = 1×64 = 64px -->
                <div class="timeline-event booked-event"
                     style="top: 288px; height: 64px;">
                  <p class="event-title">Review Budget</p>
                  <p class="event-meta">12:30 – 13:30 &bull; Budi Santoso</p>
                </div>

                <!-- Selected: Pilihan Anda 15:00–16:30
                     top = 7×64 = 448px, height = 1.5×64 = 96px -->
                <div class="timeline-event selected-event"
                     style="top: 448px; height: 96px;">
                  <span class="selected-badge">PILIHAN ANDA</span>
                  <span class="selected-time">00:00 – 00:00</span>
                </div>

              </div><!-- /.timeline-events-area -->
            </div><!-- /.timeline-grid -->
          </div><!-- /.timeline-scroll-wrapper -->

          <!-- Legend -->
          <div class="schedule-legend">
            <div class="legend-item">
              <span class="legend-swatch available-swatch"></span>
              <span class="legend-label">Tersedia</span>
            </div>
            <div class="legend-item">
              <span class="legend-swatch booked-swatch"></span>
              <span class="legend-label">Terisi/Booked</span>
            </div>
            <div class="legend-item">
              <span class="legend-swatch selected-swatch"></span>
              <span class="legend-label">Pilihan Anda</span>
            </div>
          </div>

        </div><!-- /.schedule-card -->
      </div><!-- /.col-lg-8 -->

    </div><!-- /.row -->
  </div><!-- /.booking-container -->
</main>

<?php include '../../includes/user/footer_landing.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Sync selected time range to timeline on time input change
  (function () {
    const mulaiInput = document.getElementById('mulai');
    const selesaiInput = document.getElementById('selesai');
    const tanggalInput = document.getElementById('tanggal');
    const dateDisplay = document.getElementById('schedule-date-display');
    const selectedEvent = document.querySelector('.selected-event');
    const timeIndicator = document.getElementById('live-time-indicator');
    const timeBadge = document.getElementById('live-time-badge');
    const HOUR_PX = 64;
    const GRID_START = 8; // 08:00

    if (!mulaiInput || !selesaiInput) return;

    function updateDateDisplay() {
      if (!tanggalInput || !dateDisplay || !tanggalInput.value) return;

      const dateObj = new Date(tanggalInput.value);
      
      const opsiFormat = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
      const tanggalFormatIndo = dateObj.toLocaleDateString('id-ID', opsiFormat);

      dateDisplay.textContent = tanggalFormatIndo;
    }
    
    function timeToMinutes(t) {
      const [h, m] = t.split(':').map(Number);
      return h * 60 + m;
    }

    function validasiBatasanWaktu(input) {
      if (!input.value) return;
      
      const [jam, menit] = input.value.split(':').map(Number);
      
      if (jam >= 19 || jam < 8) {
        if (jam >= 19) {
          input.value = "18:00";
        } else {
          input.value = "08:00";
        }
      }
      else if (jam === 18 && menit > 0) {
        input.value = "18:00";
      }
    }

    function updateSelectedBlock() {
      if (!mulaiInput.value || !selesaiInput.value) return;
      const startMin = timeToMinutes(mulaiInput.value);
      const endMin   = timeToMinutes(selesaiInput.value);
      if (endMin <= startMin) return;

      const topPx    = ((startMin / 60) - GRID_START) * HOUR_PX;
      const heightPx = ((endMin - startMin) / 60) * HOUR_PX;

      if (selectedEvent) {
        selectedEvent.style.top    = topPx + 'px';
        selectedEvent.style.height = heightPx + 'px';

        const fmt = (t) => {
            const [h, m] = t.split(':');
            return h + ':' + m;
        };
        const timeBadge = selectedEvent.querySelector('.selected-time');
        if (timeBadge) {
          timeBadge.textContent = fmt(mulaiInput.value) + ' – ' + fmt(selesaiInput.value);
        }
      }
      if (heightPx < 70) {
        selectedEvent.classList.add('is-row');
      } else {
        selectedEvent.classList.remove('is-row');
      }
    }

    function updateLiveIndicator() {
      if (!timeIndicator || !timeBadge) return;

      const sekarangLive = new Date();
      const jamLive = sekarangLive.getHours();
      const menitLive = sekarangLive.getMinutes();

      const jamFmt = String(jamLive).padStart(2, '0');
      const menitFmt = String(menitLive).padStart(2, '0');
      timeBadge.textContent = `${jamFmt}:${menitFmt} SEKARANG`;

      const totalMenitSekarang = (jamLive * 60) + menitLive;
      const totalMenitMulaiGrid = GRID_START * 60; // 08:00 = 480 menit

      if (jamLive >= GRID_START && jamLive < 19) {
        timeIndicator.style.display = 'flex';

        const selisihMenit = totalMenitSekarang - totalMenitMulaiGrid;
        
        const posisiTopPx = (selisihMenit / 60) * HOUR_PX;
        timeIndicator.style.top = posisiTopPx + 'px';
      } else {
        timeIndicator.style.display = 'none';
      }
    }
    updateLiveIndicator();
    setInterval(updateLiveIndicator, 60000);
    const sekarang = new Date();
    let jam = sekarang.getHours();
    let menit = sekarang.getMinutes();

    if (jam < 8 || jam >= 18) {
      jam = 8;
      menit = 0;
    } else {
      if (menit < 30) {
        menit = 30;
      } else {
        menit = 0;
        jam = (jam + 1) % 24;
      }
    }

    const formatWaktu = (h, m) => String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');

    const waktuMulaiString = formatWaktu(jam, menit);
    mulaiInput.value = waktuMulaiString;

    let totalMenitSelesai = (jam * 60) + menit + 90;
    let jamSelesai = Math.floor(totalMenitSelesai / 60) % 24;
    let menitSelesai = totalMenitSelesai % 60;

    selesaiInput.value = formatWaktu(jamSelesai, menitSelesai);

    updateSelectedBlock();
    updateDateDisplay();

    mulaiInput.addEventListener('change', updateSelectedBlock);
    selesaiInput.addEventListener('change', updateSelectedBlock);

    if (tanggalInput) {
      tanggalInput.addEventListener('change', updateDateDisplay);
    }

    document.querySelectorAll('.booking-form [required]').forEach(function (field) {
        field.addEventListener('invalid', function (event) {
        event.preventDefault();
        this.closest('.booking-form').classList.add('was-validated');
        });
    });
  })();
</script>
</body>
</html>
