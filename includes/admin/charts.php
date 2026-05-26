<?php
include __DIR__ . '/analytics_query.php';
?>
<div class="container-fluid px-0 mt-4">
	<div class="row g-3">
		<section class="col-lg-8">
			<div class="card shadow-sm border-0 h-100">
				<div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
					<div>
						<small class="text-uppercase text-secondary fw-semibold">Tren</small>
						<h5 class="mb-0 mt-1">Grafik Tren Peminjaman (12 bulan)</h5>
					</div>
					<span class="badge text-bg-light">Bulan ini</span>
				</div>
				<div class="card-body">
					<div class="chart-canvas-wrap" style="height:320px;">
						<canvas id="trendChart"></canvas>
					</div>
				</div>
			</div>
		</section>

		<section class="col-lg-4">
			<div class="card shadow-sm border-0 h-100">
				<div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
					<div>
						<small class="text-uppercase text-secondary fw-semibold">Favorit</small>
						<h5 class="mb-0 mt-1">Ruangan Terfavorit</h5>
					</div>
					<span class="badge text-bg-light">Top 3</span>
				</div>
				<div class="card-body d-flex align-items-center justify-content-center">
					<div class="chart-canvas-wrap" style="height:280px; width:100%; max-width:320px;">
						<canvas id="topRoomsChart"></canvas>
					</div>
				</div>
			</div>
		</section>
	</div>
<<<<<<< HEAD
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* =========================
   TREND CHART
========================= */
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($monthsMaster) ?>, 
        datasets: [{
            label: 'Jumlah Peminjaman',
            data: <?= json_encode($trendDataFilled) ?>, 
            borderWidth: 2,
            tension: 0.4,
            borderColor: '#3b82f6', 
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});


/* =========================
   PIE / DOUGHNUT CHART (PERBAIKAN HOVER)
========================= */
new Chart(document.getElementById('topRoomsChart'), {
    type: 'doughnut',
    data: {
        // Menggunakan nama ruangan dari database sebagai label data chart
        labels: <?= json_encode($roomLabel) ?>,
        datasets: [{
            data: <?= json_encode($roomData) ?>,
            borderWidth: 1,
            // Warna disesuaikan dengan tema UI gelap di screenshot Anda (Cyan, Ungu, Biru)
            backgroundColor: ['#60a5fa', '#a78bfa', '#22d3ee'],
            
            // KUNCI UTAMA: Mengunci ukuran border dan offset agar chart tetap diam/statis saat dihover
            hoverOffset: 0,
            hoverBorderWidth: 1,
            hoverBackgroundColor: ['#60a5fa', '#a78bfa', '#22d3ee']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        // Mematikan animasi perubahan skala/interaksi default Chart.js saat pointer masuk
        hover: {
            mode: null
        },
        plugins: {
            legend: {
                display: false // Tetap false karena legend bawaan Anda diatur lewat HTML eksternal
            },
            tooltip: {
                enabled: true // Tooltip bawaan tetap aktif menampilkan info data riil database
            }
        }
    }
});
</script>
=======
</div>
>>>>>>> 95b48ef92929bbc9d16204f67d6fa2fd38a3bb3b
