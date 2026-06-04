<?php
  session_start();

  if(!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: ../../pages/auth/login.php");
    exit();
  }
  $page_title = 'Reservasi Ruangan – SatSet';

  require_once '../../config/connection.php';
  $list_gedung="SELECT id, name FROM buildings ORDER BY name ASC";
  $result_gedung=mysqli_query($conn, $list_gedung);

  $list_ruangan="SELECT id, building_id, room_name, capacity FROM rooms WHERE is_active=1 ORDER BY room_name ASC";
  $result_ruangan=mysqli_query($conn, $list_ruangan);

  $gedung_ruangan=[];
  if ($result_ruangan) {
    while ($row=mysqli_fetch_assoc($result_ruangan)) {
      $gedung_ruangan[]=$row;
    }
  }
  $preselect_room_id=isset($_GET['room_id'])?intval($_GET['room_id']):0;

  $tanggal_hari_ini=date('Y-m-d');

  $list_agenda="SELECT room_id, reason, start_hour, end_hour, reservation_date FROM reservations WHERE status='approved' ORDER BY start_hour ASC";
  $result_agenda=mysqli_query($conn, $list_agenda);

  $agenda=[];
  if ($result_agenda) {
    while ($row=mysqli_fetch_assoc($result_agenda)) {
      $row['start_hour']=substr($row['start_hour'], 0, 5);
      $row['end_hour']=substr($row['end_hour'], 0, 5);
      $agenda[]=$row;
    }
  }

  $image_placeholder="data:image/svg+xml;utf8," . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500"><rect width="800" height="500" fill="#e2e8f0"/><rect x="70" y="70" width="660" height="360" rx="28" fill="#f8fafc" stroke="#cbd5e1" stroke-width="8"/><path d="M170 340l110-120 90 92 70-70 140 148H170z" fill="#cbd5e1"/><circle cx="290" cy="185" r="38" fill="#cbd5e1"/><text x="400" y="455" text-anchor="middle" font-family="Arial, sans-serif" font-size="28" fill="#64748b">Gambar ruangan belum tersedia</text></svg>');
  function renderRoomPicture(string $placeholder):string {
    return '
      <div class="room-preview-card" id="room-preview-card" style="display:none;">
        <div class="room-preview-img">
          <img id="room-preview-photo" src="" alt="" data-placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES) . '" onerror="this.onerror=null;this.src=this.dataset.placeholder;">
        </div>
        <div class="room-preview-info">
          <div class="room-preview-header">
            <h4 class="room-preview-name" id="room-preview-name"></h4>
          </div>
          <div class="room-preview-tags" id="room-preview-tags"></div>
        </div>
      </div>
    ';
  }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservasi · SatSet</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/css/user/landing.css">
  <link rel="stylesheet" href="../../assets/css/user/booking.css">
  <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
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
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <a class="nav-link me-3" href="../admin/dashboard.php">Dashboard</a>
          <?php endif; ?>
          <?php if(isset($_SESSION['user_id'])):?>
            <a href="../../actions/auth/logout.php" class="btn btn-danger">Logout</a>
          <?php else:?>
            <a href="../../pages/auth/register.php" class="nav-register">Register</a>
            <div class="nav-divider d-none d-lg-block"></div>
            <a href="../../pages/auth/login.php" class="btn-login">Login</a>
          <?php endif;?>
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

            <form class="booking-form" id="bookingForm" action="../../actions/booking/booking_action.php" method="POST">

              <!-- Pilih Gedung -->
              <div class="form-field-group">
                <label class="field-label" for="gedung">Pilih Gedung</label>
                <div class="select-wrapper">
                  <select class="field-select" id="gedung" name="gedung" required>
                    <option value="" disabled selected hidden>Pilih gedung</option>
                    <?php
                      if ($result_gedung && mysqli_num_rows($result_gedung) > 0) {
                        while ($gedung = mysqli_fetch_assoc($result_gedung)) {
                          echo "<option value='" . $gedung['id'] . "'>" . $gedung['name'] . "</option>";
                        }
                      }
                    ?>
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
                <div class="select-wrapper hide-arrow">
                  <select class="field-select" id="ruangan" name="ruangan" required disabled>
                    <option value="" disabled selected hidden>Pilih gedung terlebih dahulu</option>
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
                <div class="select-wrapper">
                  <input class="field-input" type="date" id="tanggal" name="tanggal"
                    <?php
                      $hourNow=(int)date('H');
                      $dateDefault=$hourNow>=17?date('Y-m-d', strtotime('+1 day')):date('Y-m-d');
                      $dateMinimum=$dateDefault;
                    ?>  
                    value="<?= $dateDefault; ?>" min="<?= $dateMinimum; ?>">
                </div>
              </div>

              <!-- Waktu Mulai & Selesai -->
              <div class="row g-3 mb-0">
                <div class="col-6">
                  <div class="form-field-group mb-0">
                    <label class="field-label">Mulai</label>
                    <div class="time-picker-wrapper" id="mulai-wrapper">
                      <div class="time-picker-display" id="mulai-display">
                        <span id="mulai-display-text">--:--</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#505F76" stroke-width="2">
                          <circle cx="12" cy="12" r="10"/>
                          <polyline points="12 6 12 12 16 14"/>
                        </svg>
                      </div>
                      <div class="time-picker-dropdown" id="mulai-dropdown">
                        <div class="time-col" id="mulai-jam-col">
                          <div class="time-col-header">JAM</div>
                        </div>
                        <div class="time-divider"></div>
                        <div class="time-col" id="mulai-menit-col">
                          <div class="time-col-header">MENIT</div>
                        </div>
                      </div>
                      <input type="hidden" id="mulai" name="mulai">
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-field-group mb-0">
                    <label class="field-label">Selesai</label>
                    <div class="time-picker-wrapper" id="selesai-wrapper">
                      <div class="time-picker-display" id="selesai-display">
                        <span id="selesai-display-text">--:--</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#505F76" stroke-width="2">
                          <circle cx="12" cy="12" r="10"/>
                          <polyline points="12 6 12 12 16 14"/>
                        </svg>
                      </div>
                      <div class="time-picker-dropdown" id="selesai-dropdown">
                        <div class="time-col" id="selesai-jam-col">
                          <div class="time-col-header">JAM</div>
                        </div>
                        <div class="time-divider"></div>
                        <div class="time-col" id="selesai-menit-col">
                          <div class="time-col-header">MENIT</div>
                        </div>
                      </div>
                      <input type="hidden" id="selesai" name="selesai">
                    </div>
                  </div>
                </div>
                <p class="hint-message">
                  <svg width="15" height="15" viewBox="0 0 25 25" fill="none">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" fill="#00288E"/>
                  </svg>
                  <a href="#" class="terms-link" id="terms-link">Ketentuan Layanan</a>
                </p>
              </div>

              <!-- Keperluan -->
              <div class="form-field-group">
                <label class="field-label" for="keperluan">Keperluan</label>
                <div class="select-wrapper">
                  <input class="field-input" type="text" id="keperluan" name="keperluan" placeholder="Contoh: Workshop, Rapat, dll." required>
                  <span class="error-message">Harap isi bidang ini</span>
                </div>
              </div>

              <button type="submit" class="btn-konfirmasi">Konfirmasi Pemesanan</button>

            </form>
          </div>

          <!-- Room Preview Card -->
          <?= renderRoomPicture($image_placeholder);?>
        </div>

        <!-- ===== RIGHT: Schedule Visualizer ===== -->
        <div class="col-12 col-lg-8" style="min-width:0;">
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
                  </div>

                  <!-- Current Time Indicator: 11:00 = 3 hours after 08:00 → top: 3 × 64px = 192px -->
                  <div class="current-time-indicator" id="live-time-indicator">
                    <span class="current-time-badge" id="live-time-badge">00:00 SEKARANG</span>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php if (isset($_GET['status']) && $_GET['status']==='berhasil'): ?>
    <script>
      Swal.fire({
        position:"top",
        icon:"success",
        title:"Reservasi Berhasil!",
        text:"Permintaan Anda sedang menunggu persetujuan.",
        width:"360px",
        showConfirmButton:false,
        timer:3000
      }).then(function(){
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '0';
        setTimeout(function() {
          window.location.href = '../../pages/user/history.php';
        }, 500);
      });
    </script>
    <?php elseif (isset($_GET['status']) && $_GET['status']==='gagal'): ?>
      <script>
        Swal.fire({
          position:"top",
          icon:"error",
          title:"Reservasi Gagal!",
          text:"Terjadi kesalahan, silakan coba lagi.",
          width:"360px",
          showConfirmButton:false,
          timer:3000
        });
      </script>
  <?php endif;?>

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

        const [y, m, d]=tanggalInput.value.split('-').map(Number);
        const dateObj=new Date(y, m-1, d);
        
        const opsiFormat = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        const tanggalFormatIndo = dateObj.toLocaleDateString('id-ID', opsiFormat);

        dateDisplay.textContent = tanggalFormatIndo;
      }
      
      function timeToMinutes(t) {
        const [h, m] = t.split(':').map(Number);
        return h * 60 + m;
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

      const formatWaktu = (h, m) => String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
      let ubahManual=false;
      let sudahUbah=false;

      function updateLiveIndicator() {
        if (!timeIndicator || !timeBadge) return;

        const hariIni='<?= $tanggal_hari_ini?>';
        const tanggalPilihan=tanggalInput?tanggalInput.value:hariIni;
        const ishariIni=tanggalPilihan===hariIni;

        if (ishariIni) {
          const sekarangLive = new Date();
          const jamLive = sekarangLive.getHours();
          const menitLive = sekarangLive.getMinutes();

          const jamFmt = String(jamLive).padStart(2, '0');
          const menitFmt = String(menitLive).padStart(2, '0');
          timeBadge.textContent = `${jamFmt}:${menitFmt} SEKARANG`;

          if (jamLive < GRID_START) {
            timeBadge.textContent = '08:00';
            timeBadge.style.transform = 'translateY(0)';
            timeIndicator.style.display = 'flex';
            timeIndicator.style.top = '0px';
          } else if (jamLive >= GRID_START && jamLive < 19) {
            timeBadge.textContent = `${jamFmt}:${menitFmt} SEKARANG`;
            const totalMenitSekarang = (jamLive * 60) + menitLive;
            const totalMenitMulaiGrid = GRID_START * 60;
            const selisihMenit = totalMenitSekarang - totalMenitMulaiGrid;
            const posisiTopPx = (selisihMenit / 60) * HOUR_PX;
            timeIndicator.style.display = 'flex';
            timeIndicator.style.top = posisiTopPx + 'px';
            timeBadge.style.transform = posisiTopPx < 24 ? 'translateY(0)' : 'translateY(-100%)';
          } else {
            timeIndicator.style.display = 'none';
          }
        } else {
          timeBadge.textContent='08:00';
          timeBadge.style.transform='translateY(0)';
          timeIndicator.style.display='flex';
          timeIndicator.style.top='0px';
        }

        if (ishariIni && !ubahManual) {
          const sekarang = new Date();
          let jam = sekarang.getHours();
          let menit = sekarang.getMinutes();

          if (jam < 8 || jam >= 18) { jam = 8; menit = 0; }

          mulaiInput.value = formatWaktu(jam, menit);

          let totalSelesai = (jam * 60) + menit + 60;
          let jamSelesai = Math.floor(totalSelesai / 60) % 24;
          let menitSelesai = totalSelesai % 60;
          if (jamSelesai > 18 || (jamSelesai === 18 && menitSelesai > 0)) { jamSelesai = 18; menitSelesai = 0; }
          selesaiInput.value = formatWaktu(jamSelesai, menitSelesai);
        }

        updateSelectedBlock();
      }
      updateLiveIndicator();
      setInterval(updateLiveIndicator, 1000);

      function resetWaktuDefault() {
        const hariIni='<?= $tanggal_hari_ini?>';
        const ishariIni=tanggalInput.value===hariIni;
        
        const sekarang = new Date();
        let jam = ishariIni?sekarang.getHours():8;
        let menit = ishariIni?sekarang.getMinutes():0;

        if (jam < 8 || jam >= 18) {
          jam = 8;
          menit = 0;
        }

        mulaiInput.value=formatWaktu(jam, menit);

        let totalMenitSelesai = (jam * 60) + menit + 60;
        let jamSelesai = Math.floor(totalMenitSelesai / 60) % 24;
        let menitSelesai = totalMenitSelesai % 60;
        selesaiInput.value = formatWaktu(jamSelesai, menitSelesai);

        updateSelectedBlock();
      }

      const btnPrev=document.querySelector('[aria-label="Hari sebelumnya"]');
      const btnNext=document.querySelector('[aria-label="Hari berikutnya"]');

      function updateDateButtons() {
        if (!btnPrev || !tanggalInput) return;
        const tanggalMin="<?= $dateMinimum; ?>";
        if (tanggalInput.value<=tanggalMin) btnPrev.classList.add('disabled');
        else btnPrev.classList.remove('disabled');
      }

      if (btnPrev && btnNext) {
        btnPrev.addEventListener('click', function() {
          if (this.classList.contains('disabled')) return;
          const [y, m, d]=tanggalInput.value.split('-').map(Number);
          const prev=new Date(y, m-1, d-1);
          tanggalInput.value=prev.getFullYear() + '-' + String(prev.getMonth()+1).padStart(2, '0') + '-' + String(prev.getDate()).padStart(2, '0');
          //getFullYear untuk mendapat tahun dengan format 4 digit penuh
          //getMonth perlu +1 karena javascript menganggap bulan sebagai array, jadi index januari adalah 0
          //getDate normal lah ya
          //padStart menyetting agar masukan dengan format 2 digit, kalau angkanya masih satuan, akan ditambahkan 0 didepannya
          updateDateDisplay();
          refreshCalendar();
          updateDateButtons();
        });
        btnNext.addEventListener('click', function() {
          const [y, m, d]=tanggalInput.value.split('-').map(Number);
          const next=new Date(y, m-1, d+1);
          tanggalInput.value=next.getFullYear() + '-' + String(next.getMonth()+1).padStart(2, '0') + '-' + String(next.getDate()).padStart(2, '0');
          //getFullYear untuk mendapat tahun dengan format 4 digit penuh
          //getMonth perlu +1 karena javascript menganggap bulan sebagai array, jadi index januari adalah 0
          //getDate normal lah ya
          //padStart menyetting agar masukan dengan format 2 digit, kalau angkanya masih satuan, akan ditambahkan 0 didepannya
          updateDateDisplay();
          refreshCalendar();
          updateDateButtons();
        });
      }

      updateDateButtons();
      resetWaktuDefault();
      updateDateDisplay();

      document.querySelectorAll('.booking-form [required]').forEach(function (field) {
          field.addEventListener('invalid', function (event) {
            event.preventDefault();
            this.closest('.booking-form').classList.add('was-validated');
          });
      });

      const listRuangan = <?= json_encode($gedung_ruangan); ?>;
      const listAgenda = <?= json_encode($agenda); ?>;
      const gedungSelected = document.getElementById('gedung');
      const ruanganSelected = document.getElementById('ruangan');
      if (gedungSelected && ruanganSelected) {
        gedungSelected.addEventListener('change', function() {
          const idGedungSelected = this.value;
          const wrapperRuangan = ruanganSelected.closest('.select-wrapper');
          
          this.closest('.booking-form').classList.remove('was-validated');
          
          ruanganSelected.disabled = false;
          if (wrapperRuangan) { wrapperRuangan.classList.remove('hide-arrow'); }
          
          ruanganSelected.innerHTML = '<option value="" disabled selected hidden>Pilih ruangan</option>';
          
          const ruanganCocok = listRuangan.filter(room => room.building_id == idGedungSelected);
          if (ruanganCocok.length == 0) {
            ruanganSelected.innerHTML = '<option value="" disabled selected hidden>Tidak ada ruangan tersedia</option>';
            return;
          }
          
          ruanganCocok.forEach(room => {
            const option = document.createElement('option');
            option.value = room.id;
            option.textContent = `${room.room_name} (${room.capacity} org)`;
            ruanganSelected.appendChild(option);
          });
        });
      }

      // ======= ROOM PREVIEW ======= //
      if(ruanganSelected) {
        ruanganSelected.addEventListener('change', async function () {
          const id=this.value;
          const card=document.getElementById('room-preview-card');
          if (!id) {
            card.style.display='none';
            return;
          }
          try {
            const res=await fetch('../../actions/room/room_action.php?id=' + encodeURIComponent(id), {
              headers:{'X-Requested-With': 'XMLHttpRequest'}
            });
            const data=await res.json();
            if (!res.ok || !data.success) {
              card.style.display='none';
              return;
            }
            const room=data.room;
            const placeholder=document.getElementById('room-preview-photo').dataset.placeholder;
            document.getElementById('room-preview-photo').src=room.photo_url || placeholder;
            document.getElementById('room-preview-name').textContent=room.room_name;
            const tagsEl=document.getElementById('room-preview-tags');
            tagsEl.innerHTML='';
            (room.facilities || []).forEach(function(f) {
              const span=document.createElement('span');
              span.className='room-detail-facility';
              span.textContent=f;
              tagsEl.appendChild(span);
            });
            card.style.display='';
          }
          catch(e) {document.getElementById('room-preview-card').style.display='none';}
        });
      }
      // ======= END ROOM PREVIEW ======= //

      if(ruanganSelected) {
        ruanganSelected.addEventListener('change', function() {
          refreshCalendar();
        });
      }

      let roomId = ruanganSelected?ruanganSelected.value:null; //ternary operator
      let tanggal = tanggalInput?tanggalInput.value:'<?= $tanggal_hari_ini; ?>'; //ternary operator
      function refreshCalendar() {
        roomId = ruanganSelected ? ruanganSelected.value : null; //ternary operator
        tanggal = tanggalInput ? tanggalInput.value : ''; //ternary operator

        console.log('roomId:', roomId);         // ← tambah
        console.log('tanggal:', tanggal);       // ← tambah
        console.log('listAgenda:', listAgenda); // ← tambah
        const filtered = listAgenda.filter(a => //listAgenda dimisalkan 'a'
          a.room_id == roomId && a.reservation_date == tanggal //difilter dengan kondisi tersebut
        );
        console.log('filtered:', filtered);
        renderAgenda(filtered); //mengirim listAgenda yang sudah difilter ke fungsi renderAgenda
        updateLiveIndicator();
        if (!ubahManual) {
          resetWaktuDefault();
        }
        cekBentrok();
      }

      document.getElementById('terms-link').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
          title:'Ketentuan Layanan',
          customClass:{
            title:'swal-title-custom'
          },
          width:'450px',
          html:`
            <div style="text-align:left; font-size:14px; color:#191C1E; line-height:1.7;">
              <p>Sebelum melakukan reservasi, harap perhatikan ketentuan berikut:</p>
              <ol style="padding-left:20px;">
                <li>Durasi minimal reservasi adalah 1 jam.</li>
                <li>Ruangan tersedia mulai pukul 08:00 hingga 18:00 WIB.</li>
                <li>Durasi yang dipilih sudah mencakup waktu antisipasi perpanjangan acara.</li>
                <li>Pembatalan jadwal dapat dilakukan maksimal 2 jam sebelum waktu reservasi dimulai.</li>
                <li>Pengguna ruangan wajib menjaga kebersihan dan kelengkapan fasilitas ruangan.</li>
              </ol>
            </div>
          `,
          confirmButtonText:'Saya Mengerti',
          confirmButtonColor:'#00288E',
        });
      });

      let bentrokTimer=null;
      function cekBentrok() {
        const mulai=mulaiInput.value;
        const selesai=selesaiInput.value;
        const bentrokMsg=document.getElementById('bentrok-message');

        mulaiInput.classList.remove('bentrok');
        selesaiInput.classList.remove('bentrok');
        if (bentrokMsg) bentrokMsg.style.display='none';
        document.querySelectorAll('.booked-event').forEach(ex => ex.classList.remove('bentrok-event'));
        
        if (!roomId || !mulai || !selesai) return;

        const filtered=listAgenda.filter(a => a.room_id==roomId && a.reservation_date==tanggal);

        let adaBentrok=false;
        filtered.forEach(item => {
          const bentrok=item.start_hour < selesai && item.end_hour > mulai;
          if (bentrok) {
            adaBentrok=true;
            document.querySelectorAll('.booked-event').forEach(ex => {
              const metaEx=ex.querySelector('.event-meta');
            });
          }
        });

        if (adaBentrok) {
          mulaiInput.classList.add('bentrok');
          selesaiInput.classList.add('bentrok');
          if (bentrokMsg) bentrokMsg.style.display='block';

          clearTimeout(bentrokTimer);
          bentrokTimer=setTimeout(function() {
            Swal.fire({
              icon:'warning',
              title:'Jadwal Bentrok',
              text:'Maaf, jadwal yang Anda pilih bertabrakan dengan acara lain. Silakan sesuaikan kembali waktu atau ruangan Anda.',
              confirmButtonText:'Atur Ulang Jadwal',
              confirmButtonColor:'#00288E',
              width:'450px',
            });
          }, 500);
        } else clearTimeout(bentrokTimer);

        return adaBentrok;
      }
      
      //javascript tidak memperhatikan urutan fungsi
      if (tanggalInput) {
        tanggalInput.addEventListener('change', function() {
          ubahManual=false;
          updateDateDisplay();
          refreshCalendar();
          resetWaktuDefault();
          updateDateButtons();
        });
      }

      const preselectRoomId = <?= $preselect_room_id ?>;
      if (preselectRoomId && gedungSelected && ruanganSelected) {
        const targetRoom = listRuangan.find(r => r.id == preselectRoomId);
        if (targetRoom) {
          gedungSelected.value = targetRoom.building_id;
          const wrapperRuangan = ruanganSelected.closest('.select-wrapper');
          ruanganSelected.disabled = false;
          if (wrapperRuangan) wrapperRuangan.classList.remove('hide-arrow');
          ruanganSelected.innerHTML = '<option value="" disabled selected hidden>Pilih ruangan</option>';
          listRuangan
            .filter(r => r.building_id == targetRoom.building_id)
            .forEach(function(r) {
              const opt = document.createElement('option');
              opt.value = r.id;
              opt.textContent = r.room_name + ' (' + r.capacity + ' org)';
              ruanganSelected.appendChild(opt);
            });
          ruanganSelected.value = preselectRoomId;
          ruanganSelected.dispatchEvent(new Event('change'));
        }
      }

      if (!preselectRoomId) {
        refreshCalendar();
      }

      function renderAgenda(agendaData) { //membuat blok agenda yang sudah ada
        document.querySelectorAll('.booked-event').forEach(el => el.remove()); //membersihkan tampilan kalender

        const eventsArea = document.querySelector('.timeline-events-area');
        if (!eventsArea) return; //kalau tidak ada agenda, langsung keluar fungsi

        agendaData.forEach(item => {
          const [sh, sm] = item.start_hour.split(':').map(Number); //memisahkan jam dan menit
          const [eh, em] = item.end_hour.split(':').map(Number); //memisahkan jam dan menit

          const topPx    = ((sh + sm / 60) - GRID_START) * HOUR_PX; //membuat garis atas blok agenda
          const heightPx = ((eh + em / 60) - (sh + sm / 60)) * HOUR_PX; //membuat tinggi blok agenda

          const div = document.createElement('div');
          div.className = 'timeline-event booked-event';
          div.style.top    = topPx + 'px';
          div.style.height = heightPx + 'px';
          div.innerHTML = `
            <p class="event-title">${item.reason}</p>
            <p class="event-meta">${item.start_hour} – ${item.end_hour}</p>
          `; //menampilkan keperluan, jam mulai dan jam selesai agenda
          eventsArea.appendChild(div); //mengirim eventsArea dan menampilkannya sesuai format yang ada di div
        });
      }

      function buatTimePicker(prefix, jamMin, jamMax, onChangeCb) {
        const display = document.getElementById(prefix + '-display');
        const displayTxt = document.getElementById(prefix + '-display-text');
        const dropdown = document.getElementById(prefix + '-dropdown');
        const jamCol = document.getElementById(prefix + '-jam-col');
        const menitCol = document.getElementById(prefix + '-menit-col');
        const hiddenInput = document.getElementById(prefix);

        let selectedJam = null;
        let selectedMenit = null;

        // Render jam
        for (let j = jamMin; j <= jamMax; j++) {
          const el = document.createElement('div');
          el.className = 'time-option';
          el.textContent = String(j).padStart(2, '0');
          el.dataset.val = j;
          el.addEventListener('click', function() {
            selectedJam = j;
            jamCol.querySelectorAll('.time-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            // Disable menit 01-59 jika jam = 18
            const hariIniJamKlik = '<?= $tanggal_hari_ini ?>';
            const isHariIniJamKlik = tanggalInput && tanggalInput.value === hariIniJamKlik;
            const jamSkrgJamKlik  = new Date().getHours();
            const menitSkrgJamKlik = new Date().getMinutes();
            menitCol.querySelectorAll('.time-option').forEach(function(opt) {
              const menitVal = parseInt(opt.dataset.val);
              let nonaktif=false;
              if (selectedJam === 18 && menitVal > 0) nonaktif = true;
              if (isHariIniJamKlik && selectedJam === jamSkrgJamKlik && menitVal <= menitSkrgJamKlik) nonaktif = true;
              if (isHariIniJamKlik && selectedJam < jamSkrgJamKlik) nonaktif = true;
              opt.classList.toggle('disabled', nonaktif);
            });

            // Jika menit yang terpilih sekarang tidak valid, reset ke 00
            if (selectedJam === 18 && selectedMenit > 0) {
              selectedMenit = 0;
              menitCol.querySelectorAll('.time-option').forEach(o => {
                o.classList.toggle('selected', parseInt(o.dataset.val) === 0);
              });
            }
            updateDisplay();
            if (onChangeCb) onChangeCb();
          });
          jamCol.appendChild(el);
        }

        // Render menit
        for (let m = 0; m < 60; m++) {
          const el = document.createElement('div');
          el.className = 'time-option';
          el.textContent = String(m).padStart(2, '0');
          el.dataset.val = m;
          el.addEventListener('click', function() {
            selectedMenit = m;
            menitCol.querySelectorAll('.time-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            updateDisplay();
            if (onChangeCb) onChangeCb();
          });
          menitCol.appendChild(el);
        }

        function updateDisplay() {
          if (selectedJam !== null && selectedMenit !== null) {
            const val = formatWaktu(selectedJam, selectedMenit);
            displayTxt.textContent = val;
            hiddenInput.value = val;
          }
        }

        // Toggle dropdown
        display.addEventListener('click', function() {
          const isOpen = dropdown.classList.contains('open');
          // Tutup semua picker lain
          document.querySelectorAll('.time-picker-dropdown.open').forEach(d => {
            d.classList.remove('open');
            d.previousElementSibling && d.previousElementSibling.classList.remove('open');
          });
          if (!isOpen) {
            dropdown.classList.add('open');
            display.classList.add('open');
            // Scroll ke selected
            const hariIni = '<?= $tanggal_hari_ini ?>';
            const isHariIni = tanggalInput && tanggalInput.value === hariIni;
            jamCol.querySelectorAll('.time-option').forEach(el => {
              const j = parseInt(el.dataset.val);
              if (isHariIni && j < new Date().getHours()) {
                el.classList.add('disabled');
              } else {
                el.classList.remove('disabled');
              }
            });
            const selectedJamEl = jamCol.querySelector('.time-option.selected');
            const selectedJamVal = selectedJamEl ? parseInt(selectedJamEl.dataset.val) : null;
            menitCol.querySelectorAll('.time-option').forEach(function(opt) {
              const menitVal = parseInt(opt.dataset.val);
              let nonaktif = false;
              if (selectedJamVal === 18 && menitVal > 0) nonaktif = true;
              if (isHariIni && selectedJamVal !== null && selectedJamVal === jamSkrgBuka && menitVal <= menitSkrgBuka) nonaktif = true;
              if (isHariIni && selectedJamVal !== null && selectedJamVal < jamSkrgBuka) nonaktif = true;
              opt.classList.toggle('disabled', nonaktif);
            });
            setTimeout(() => {
              const selJam = jamCol.querySelector('.selected');
              if (selJam) selJam.scrollIntoView({ block: 'center' });
              const selMenit = menitCol.querySelector('.selected');
              if (selMenit) selMenit.scrollIntoView({ block: 'center' });
            }, 50);
          }
        });

        // Tutup kalau klik di luar
        document.addEventListener('click', function(e) {
          if (!display.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
            display.classList.remove('open');
          }
        });

        // API untuk set nilai dari luar
        return {
          setValue: function(val) {
            if (!val) return;
            const [j, m] = val.split(':').map(Number);
            selectedJam = j;
            selectedMenit = m;
            jamCol.querySelectorAll('.time-option').forEach(o => {
              o.classList.toggle('selected', parseInt(o.dataset.val) === j);
            });
            menitCol.querySelectorAll('.time-option').forEach(o => {
              o.classList.toggle('selected', parseInt(o.dataset.val) === m);
            });
            updateDisplay();
          },
          getValue: function() {
            if (selectedJam === null || selectedMenit === null) return '';
            return formatWaktu(selectedJam, selectedMenit);
          }
        };
      }

      // Inisialisasi kedua picker
      const pickerMulai = buatTimePicker('mulai', 8, 18, function() {
        ubahManual = true;
        if (!sudahUbah && mulaiInput.value) {
          const [j, m] = mulaiInput.value.split(':').map(Number);
          let ts = j * 60 + m + 60;
          let js = Math.floor(ts / 60), ms = ts % 60;
          if (js > 18 || (js === 18 && ms > 0)) { js = 18; ms = 0; }
          pickerSelesai.setValue(formatWaktu(js, ms));
        }
        updateSelectedBlock();
        cekBentrok();
      });
      const pickerSelesai = buatTimePicker('selesai', 8, 18, function() {
        sudahUbah = true;
        updateSelectedBlock();
        cekBentrok();
      });

      const bookingForm = document.querySelector('.booking-form');
      if (bookingForm) {
        bookingForm.addEventListener('submit', function(event) {
          if (!this.checkValidity()) {
            event.preventDefault();
            this.classList.add('was-validated');
          }
          if (cekBentrok()) {
            event.preventDefault();
            return;
          }
        });
      }
    })();
  </script>
  </body>
</html>