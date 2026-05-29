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
window.calendarEvents = <?= json_encode($calendarEvents) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const host = document.querySelector("[data-fc]");
  if (!host) return;

  const VIEW_MAP = {
    'Hari': 'timeGridDay',
    'Minggu': 'timeGridWeek',
    'Bulan': 'dayGridMonth',
    'Agenda': 'listWeek'
  };

  host.innerHTML = '';

  const calendar = new FullCalendar.Calendar(host, {
    initialView: 'dayGridMonth',
    initialDate: '2026-05-26', 
    headerToolbar: false,
    height: '100%',
    locale: 'id',
    expandRows: true,
    dayMaxEvents: 2, 
    navLinks: true, 
    dayPopoverFormat: { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' },
    fixedWeekCount: false,
    firstDay: 0,
    nowIndicator: true,
    events: window.calendarEvents, 
    dayHeaderFormat: { weekday: 'short' },
    displayEventTime: false 
  });

  calendar.render();

  // Jika layout awal membuat kalender tidak terlihat, perbaiki ukuran setelah render
  setTimeout(() => {
    try {
      calendar.updateSize();
      updateTitle();
    } catch (e) {
      // fallback silent
    }
  }, 50);

  // Pengendali Toolbar Navigasi & Judul Bulan
  const root = host.closest(".cal-main") || document;
  const monthEl = root.querySelector(".cal-month");

  const updateTitle = () => {
    if (!monthEl) return;
    const d = calendar.getDate();
    const month = d.toLocaleString("id-ID", { month: "long" });
    const year = d.getFullYear();
    monthEl.innerHTML = `${month} <span class="yr">${year}</span>`;
  };

  // Navigasi tombol Panah (Kiri & Kanan) - Bersih tanpa kloning
  root.querySelectorAll(".cal-nav-btn").forEach((btn, idx) => {
    btn.addEventListener("click", () => {
      if (idx === 0) calendar.prev();
      if (idx === 1) calendar.next();
      updateTitle();
    });
  });

  // Tombol Hari Ini
  const todayBtn = root.querySelector(".cal-today-btn");
  if (todayBtn) {
    todayBtn.addEventListener("click", () => {
      calendar.today();
      updateTitle();
    });
  }

  // Pengendali Pindah Tampilan (Bulan, Minggu, Hari, Agenda)
  root.querySelectorAll(".cal-view-tab").forEach((tab) => {
    tab.addEventListener("click", () => {
      const label = tab.textContent.trim();
      const view = VIEW_MAP[label] || "dayGridMonth";
      
      // Atur class aktif pada tab pembungkus bootstrap
      root.querySelectorAll(".cal-view-tab").forEach((t) => t.classList.toggle("is-active", t === tab));
      
      calendar.changeView(view);
      updateTitle();
    });
  });

  // Inisialisasi judul pertama kali saat halaman dimuat
  setTimeout(updateTitle, 50);
});
</script>