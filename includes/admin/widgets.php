<?php
include __DIR__ . '/../../includes/admin/mini_log_query.php';
include __DIR__ . '/../../includes/admin/calendar_query.php';
?>

<section class="container-fluid px-0 mt-4">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0 cal-main h-100">
        <div class="card-header bg-white border-0">
          <small class="text-uppercase text-secondary fw-semibold">Jadwal</small>
          <h6 class="mb-0 mt-1">Kalender Jadwal</h6>
        </div>
        <div class="card-body d-flex flex-column" style="min-height: clamp(420px, 62vh, 760px);">
          <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
            <div class="d-flex flex-wrap gap-2" role="group" aria-label="Navigasi kalender">
              <button type="button" class="btn btn-sm btn-outline-secondary cal-nav-btn px-3 text-center"><i class="fa fa-caret-left"></i></button>
              <button type="button" class="btn btn-sm btn-outline-secondary cal-nav-btn px-3 text-center"><i class="fa fa-caret-right"></i></button>
            </div>

            <div class="fw-semibold cal-month text-nowrap">Mei <span class="yr">2026</span></div>

            <button type="button" class="btn btn-sm btn-primary cal-today-btn">Hari Ini</button>
          </div>

          <div class="d-flex gap-2 flex-wrap mb-3">
            <button type="button" class="btn btn-sm btn-outline-secondary cal-view-tab">Bulan</button>
            <button type="button" class="btn btn-sm btn-outline-secondary cal-view-tab">Minggu</button>
            <button type="button" class="btn btn-sm btn-outline-secondary cal-view-tab">Hari</button>
            <button type="button" class="btn btn-sm btn-outline-secondary cal-view-tab">Agenda</button>
          </div>

          <div data-fc style="flex: 1 1 auto; min-height: 0;"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
          <small class="text-uppercase text-secondary fw-semibold">Log</small>
          <h6 class="mb-0 mt-1">Mini Log Aktivitas</h6>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush small">

            <?php while($row = mysqli_fetch_assoc($qLog)) : ?>

            <?php
            $time = date('H:i', strtotime($row['created_at']));

            if ($row['status'] === 'approved' && $row['admin_name']) {
                $text = "{$row['admin_name']} menyetujui peminjaman {$row['room_name']} dari {$row['user_name']}";
            }
            elseif ($row['status'] === 'rejected' && $row['admin_name']) {
                $text = "{$row['admin_name']} menolak peminjaman {$row['room_name']} dari {$row['user_name']}";
            }
            else {
                $text = "{$row['user_name']} mengajukan peminjaman {$row['room_name']}";
            }
            ?>

            <li class="list-group-item px-0">
                <span class="text-secondary"><?= $time ?></span>
                · <?= htmlspecialchars($text) ?>
            </li>

            <?php endwhile; ?>

            </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
window.calendarEvents = <?= json_encode($calendarEvents ?? []) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>


<script src="../../assets/js/calendar.js"></script>