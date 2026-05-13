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
						<canvas id="trendChart" data-chart-key="dashboard-monthly"></canvas>
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
						<canvas id="topRoomsChart" data-chart-key="devices-doughnut"></canvas>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>