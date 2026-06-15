<?php
$active = 'laporan';
$crumbs = 'Laporan | Cetak Laporan';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once __DIR__ . '/../../actions/report/report_action.php';

$printQuery = $_SERVER['QUERY_STRING'] ?? '';
$printUrl = 'report-print.php' . ($printQuery !== '' ? '?' . $printQuery : '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
</head>

<body data-active="<?php echo htmlspecialchars($active); ?>" data-crumbs="<?php echo htmlspecialchars($crumbs); ?>" class="report-page">
    <div class="shell">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        <div class="main">
            <?php include __DIR__ . '/../../includes/navbar.php'; ?>
            <main class="content">
                <section class="hero no-print">
                    <div class="hero-text">
                        <h1 class="hero-title">Cetak Laporan Reservasi</h1>
                        <p class="hero-subtitle">Laporan detail peminjaman ruangan berdasarkan filter tanggal, bulan, atau tahun.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="<?php echo htmlspecialchars($printUrl); ?>" target="_blank" rel="noopener" class="btn btn-primary">
                            <svg viewBox="0 0 24 24"><path d="M6 9V3h12v6"/><path d="M6 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><path d="M6 14h12v7H6z"/></svg>
                            Cetak
                        </a>
                    </div>
                </section>

                <section class="card report-filters no-print">
                    <form method="get" class="report-filter-form">
                        <div class="report-filter-grid">
                            <div>
                                <label class="form-label">Kata Kunci</label>
                                <input class="form-control" type="search" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama, email, gedung, ruangan, alasan...">
                            </div>
                            <div>
                                <label class="form-label">Jenis Filter</label>
                                <select class="form-select" name="filter_type">
                                    <option value="all" <?php echo $filterType === 'all' ? 'selected' : ''; ?>>Semua Data</option>
                                    <option value="date" <?php echo $filterType === 'date' ? 'selected' : ''; ?>>Tanggal</option>
                                    <option value="month" <?php echo $filterType === 'month' ? 'selected' : ''; ?>>Bulan</option>
                                    <option value="year" <?php echo $filterType === 'year' ? 'selected' : ''; ?>>Tahun</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Tanggal</label>
                                <input class="form-control" type="date" name="filter_date" value="<?php echo htmlspecialchars($filterDate); ?>">
                            </div>
                            <div>
                                <label class="form-label">Bulan</label>
                                <input class="form-control" type="month" name="filter_month" value="<?php echo htmlspecialchars($filterMonth); ?>">
                            </div>
                            <div>
                                <label class="form-label">Tahun</label>
                                <select class="form-select" name="filter_year">
                                    <?php foreach ($yearOptions as $yearOption): ?>
                                        <option value="<?php echo htmlspecialchars($yearOption); ?>" <?php echo $filterYear === $yearOption ? 'selected' : ''; ?>><?php echo htmlspecialchars($yearOption); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="report-filter-actions">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            <a href="report.php" class="btn btn-outline-secondary">Reset</a>
                            <a href="<?php echo htmlspecialchars($printUrl); ?>" target="_blank" rel="noopener" class="btn btn-outline-dark">Cetak Hasil Filter</a>
                        </div>
                    </form>
                </section>

                <section class="card report-sheet">
                    <div class="report-header">
                        <div>
                            <div class="report-kicker">RentRoom Admin Report</div>
                            <h2 class="report-title">Laporan Reservasi Ruangan</h2>
                            <p class="report-meta">Filter aktif: <strong><?php echo htmlspecialchars($filterLabel); ?></strong></p>
                        </div>
                        <div class="report-meta-box">
                            <div>Dibuat: <strong><?php echo date('d M Y H:i'); ?></strong></div>
                            <div>Total data: <strong><?php echo $summaryTotal; ?></strong></div>
                        </div>
                    </div>

                    <div class="report-stats">
                        <div class="report-stat stat-total">
                            <span>Total Reservasi</span>
                            <strong><?php echo $summaryTotal; ?></strong>
                        </div>
                        <div class="report-stat stat-approved">
                            <span>Approved</span>
                            <strong><?php echo $summaryApproved; ?></strong>
                        </div>
                        <div class="report-stat stat-waiting">
                            <span>Waiting</span>
                            <strong><?php echo $summaryWaiting; ?></strong>
                        </div>
                        <div class="report-stat stat-rejected">
                            <span>Rejected & Cancelled</span>
                            <strong><?php echo $summaryRejected; ?></strong>
                        </div>
                    </div>

                    <div class="table-responsive report-table-wrap">
                        <table class="table report-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Peminjam</th>
                                    <th>Email</th>
                                    <th>Gedung</th>
                                    <th>Ruangan</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Disetujui Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($filteredReservations)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">Tidak ada data reservasi yang cocok dengan filter.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($filteredReservations as $index => $row): ?>
                                        <?php
                                            $status = $row['status'] ?? 'waiting';
                                            $statusClass = report_status_class($status);
                                            $statusText = report_status_label($status);
                                            $timeRange = report_time_range($row);
                                        ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($row['user_name'] ?? '-'); ?></div>
                                                <div class="small text-muted">ID #<?php echo htmlspecialchars((string)($row['user_id'] ?? '-')); ?></div>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['user_email'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['building_name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['room_name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($row['reservation_date']))); ?></td>
                                            <td><?php echo htmlspecialchars($timeRange); ?></td>
                                            <td class="report-reason"><?php echo htmlspecialchars($row['reason'] ?? '-'); ?></td>
                                            <td><span class="badge <?php echo $statusClass; ?> dot"><?php echo htmlspecialchars($statusText); ?></span></td>
                                            <td><?php echo htmlspecialchars($row['approved_by_name'] ?? '-'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>

        <?php include __DIR__ . '/../../includes/script.php'; ?>
</body>

</html>