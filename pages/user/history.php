<?php
session_start();
include '../../actions/history/read.php';

$page_title = 'Riwayat Reservasiku - SatSet';
$is_logged_in = isset($_SESSION['is_login']) && $_SESSION['is_login'] === true;

function esc($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_date_id($value)
{
  if (empty($value)) {
    return '-';
  }

  $timestamp = strtotime($value);
  if ($timestamp === false) {
    return esc($value);
  }

  return date('d M Y', $timestamp);
}

function status_label($status)
{
  if ($status === 'approved') {
    return 'Disetujui';
  }

  if ($status === 'rejected') {
    return 'Ditolak';
  }

  return 'Menunggu';
}

function status_class($status)
{
  if ($status === 'approved') {
    return 'status-approved';
  }

  if ($status === 'rejected') {
    return 'status-rejected';
  }

  return 'status-pending';
}

function build_history_query($page, $search_term, $filter_date, $filter_status)
{
  $params = [];

  if ($search_term !== '') {
    $params['q'] = $search_term;
  }

  if ($filter_date !== '') {
    $params['date'] = $filter_date;
  }

  if ($filter_status !== '') {
    $params['status'] = $filter_status;
  }

  $params['page'] = $page;

  return '?' . http_build_query($params);
}

$start_item = $total_rows > 0 ? (($current_page - 1) * $per_page) + 1 : 0;
$end_item = min($current_page * $per_page, $total_rows);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($page_title) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/css/user/landing.css">
  <link rel="stylesheet" href="../../assets/css/user/history.css">
</head>
<body>

<?php $current_page_file = basename($_SERVER['PHP_SELF']); ?>
<nav class="landing-navbar navbar navbar-expand-lg">
  <div class="container-fluid px-0">
    <a class="navbar-brand" href="../../index.php" style="color: #164d6d;">
      <img src="../../assets/images/logo.png" alt="SatSet" height="40" style="margin-right:6px;" onerror="this.style.display='none'">
      SatSet
    </a>

    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav" aria-controls="landingNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="landingNav">
      <ul class="navbar-nav mx-auto gap-lg-3">
        <li class="nav-item"><a class="nav-link" href="../../index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="../../index.php#alur-peminjaman">Alur Peminjaman</a></li>
        <li class="nav-item"><a class="nav-link" href="../../index.php#daftar-ruangan">Ruangan</a></li>
        <li class="nav-item"><a class="nav-link" href="../../pages/user/booking.php">Reservasi</a></li>
        <li class="nav-item"><a class="nav-link active" href="../../pages/user/history.php">Riwayat</a></li>
      </ul>

      <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a class="nav-link me-3" href="../admin/dashboard.php">Dashboard</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="actions/auth/logout.php" class="btn btn-danger">Logout</a>
        <?php else: ?>
          <a href="../../pages/auth/register.php" class="nav-register">Register</a>
          <div class="nav-divider d-none d-lg-block"></div>
          <a href="../../pages/auth/login.php" class="btn-login">Login</a>
        <?php endif;?>
      </div>
    </div>
  </div>
</nav>

<main class="history-page">
  <div class="history-container">
    <div class="history-header">
      <h1 class="history-title">Riwayat Reservasiku</h1>
      <p class="history-subtitle">Kelola dan pantau status pemesanan ruangan Anda.</p>
    </div>

    <form class="filter-bar" method="get" action="">
      <div class="filter-field">
        <label class="filter-label" for="cariRuangan">Cari Ruangan / ID</label>
        <div class="filter-input-wrapper">
          <svg class="filter-input-icon" width="18" height="18" viewBox="0 0 18 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.6 18L10.3 11.7C9.8 12.1 9.225 12.4167 8.575 12.65C7.925 12.8833 7.23333 13 6.5 13C4.68333 13 3.14583 12.3708 1.8875 11.1125C0.629167 9.85417 0 8.31667 0 6.5C0 4.68333 0.629167 3.14583 1.8875 1.8875C3.14583 0.629167 4.68333 0 6.5 0C8.31667 0 9.85417 0.629167 11.1125 1.8875C12.3708 3.14583 13 4.68333 13 6.5C13 7.23333 12.8833 7.925 12.65 8.575C12.4167 9.225 12.1 9.8 11.7 10.3L18 16.6L16.6 18ZM6.5 11C7.75 11 8.8125 10.5625 9.6875 9.6875C10.5625 8.8125 11 7.75 11 6.5C11 5.25 10.5625 4.1875 9.6875 3.3125C8.8125 2.4375 7.75 2 6.5 2C5.25 2 4.1875 2.4375 3.3125 3.3125C2.4375 4.1875 2 5.25 2 6.5C2 7.75 2.4375 8.8125 3.3125 9.6875C4.1875 10.5625 5.25 11 6.5 11Z" fill="#757684"/>
          </svg>
          <input class="filter-input" type="text" id="cariRuangan" name="q" value="<?= esc($search_term) ?>" placeholder="Nama ruangan atau ID reservasi...">
        </div>
      </div>

      <div class="filter-field">
        <label class="filter-label" for="filterTanggal">Tanggal</label>
        <div class="filter-input-wrapper">
          <svg class="filter-input-icon" width="18" height="18" viewBox="0 0 18 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 20C1.45 20 0.979167 19.8042 0.5875 19.4125C0.195833 19.0208 0 18.55 0 18V4C0 3.45 0.195833 2.97917 0.5875 2.5875C0.979167 2.19583 1.45 2 2 2H3V0H5V2H13V0H15V2H16C16.55 2 17.0208 2.19583 17.4125 2.5875C17.8042 2.97917 18 3.45 18 4V18C18 18.55 17.8042 19.0208 17.4125 19.4125C17.0208 19.8042 16.55 20 16 20H2ZM2 18H16V8H2V18ZM2 6H16V4H2V6Z" fill="#757684"/>
          </svg>
          <input class="filter-input" type="date" id="filterTanggal" name="date" value="<?= esc($filter_date) ?>">
        </div>
      </div>

      <div class="filter-field">
        <label class="filter-label" for="filterStatus">Status</label>
        <div class="filter-select-wrapper">
          <select class="filter-select" id="filterStatus" name="status">
            <option value="">Semua Status</option>
            <option value="approved" <?= $filter_status === 'approved' ? 'selected' : '' ?>>Disetujui</option>
            <option value="waiting" <?= $filter_status === 'waiting' ? 'selected' : '' ?>>Menunggu</option>
            <option value="rejected" <?= $filter_status === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
          </select>
          <svg class="select-chevron-icon" width="21" height="21" viewBox="0 0 21 21" fill="none">
            <path d="M6.3 8.4L10.5 12.6L14.7 8.4" stroke="#6B7280" stroke-width="1.575" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>

      <div class="filter-action d-flex gap-2">
        <button class="btn-filter" type="submit">Filter</button>
        <a class="btn-filter btn-reset-filter" href="history.php">Reset</a>
      </div>
    </form>

    <div class="reservation-table-card">
      <div class="table-responsive">
        <table class="reservation-table">
          <thead>
            <tr>
              <th class="col-ruangan">Ruangan</th>
              <th class="col-tanggal">Tanggal</th>
              <th class="col-waktu">Waktu</th>
              <th class="col-lokasi">Lokasi</th>
              <th class="col-status">Status</th>
              <th class="col-aksi">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($is_logged_in && $total_rows > 0): ?>
              <?php foreach ($history_rows as $row): ?>
                <tr>
                  <td>
                    <span class="room-name-text"><?= esc($row['room_name']) ?></span>
                    <span class="room-capacity-text">Kapasitas: <?= esc($row['capacity']) ?> Orang</span>
                  </td>
                  <td><?= esc(format_date_id($row['reservation_date'])) ?></td>
                  <td><?= esc(substr($row['start_hour'], 0, 5)) ?> - <?= esc(substr($row['end_hour'], 0, 5)) ?></td>
                  <td>
                    <span class="location-cell"><?= esc($row['building_name']) ?></span>
                  </td>
                  <td>
                    <span class="status-badge <?= esc(status_class($row['status'])) ?>">
                      <?= esc(status_label($row['status'])) ?>
                    </span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <?php if ($row['status'] === 'approved'): ?>
                        <a href="../../pages/user/proof.php?id=<?= esc($row['reservation_id']) ?>" class="btn-cetak-bukti" target="_blank" rel="noopener noreferrer">Cetak Bukti</a>
                      <?php else: ?>
                        <span class="btn-cetak-bukti btn-cetak-bukti-disabled">Menunggu Approval</span>
                      <?php endif; ?>
                      <button type="button" class="btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal<?= esc($row['reservation_id']) ?>">Detail</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php elseif (!$is_logged_in): ?>
              <tr class="empty-state-row">
                <td class="empty-state-cell" colspan="6">
                  <div class="empty-state-card">
                    <h3 class="empty-state-title">Belum ada riwayat reservasi</h3>
                    <p class="empty-state-text">Silakan login untuk melihat riwayat pemesanan ruangan Anda.</p>
                    <a href="../../pages/auth/login.php" class="empty-state-action">Login sekarang</a>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <tr class="empty-state-row">
                <td class="empty-state-cell" colspan="6">
                  <div class="empty-state-card">
                    <h3 class="empty-state-title">Data tidak ditemukan</h3>
                    <p class="empty-state-text">Tidak ada riwayat yang sesuai filter.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($is_logged_in): ?>
      <div class="table-pagination">
        <span class="pagination-info">
          Menampilkan <strong><?= esc($start_item) ?></strong> hingga <strong><?= esc($end_item) ?></strong> dari <strong><?= esc($total_rows) ?></strong> data
        </span>

        <?php if ($total_pages > 1): ?>
        <div class="pagination-controls">
          <?php $prev_page = max(1, $current_page - 1); ?>
          <a class="page-btn page-btn-nav <?= $current_page <= 1 ? 'disabled' : '' ?>" <?= $current_page <= 1 ? '' : 'href="' . esc(build_history_query($prev_page, $search_term, $filter_date, $filter_status)) . '"' ?> aria-label="Halaman sebelumnya">&lt;</a>

          <?php for ($p = 1; $p <= $total_pages; $p++): ?>
            <a class="page-btn <?= $p === $current_page ? 'page-btn-active' : '' ?>" href="<?= esc(build_history_query($p, $search_term, $filter_date, $filter_status)) ?>"><?= esc($p) ?></a>
          <?php endfor; ?>

          <?php $next_page = min($total_pages, $current_page + 1); ?>
          <a class="page-btn page-btn-nav <?= $current_page >= $total_pages ? 'disabled' : '' ?>" <?= $current_page >= $total_pages ? '' : 'href="' . esc(build_history_query($next_page, $search_term, $filter_date, $filter_status)) . '"' ?> aria-label="Halaman berikutnya">&gt;</a>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php if ($is_logged_in && $total_rows > 0): ?>
  <?php foreach ($history_rows as $row): ?>
    <div class="modal fade" id="detailModal<?= esc($row['reservation_id']) ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Reservasi #<?= esc($row['reservation_id']) ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="detail-row"><strong>Ruangan:</strong> <?= esc($row['room_name']) ?></div>
            <div class="detail-row"><strong>Lokasi:</strong> <?= esc($row['building_name']) ?></div>
            <div class="detail-row"><strong>Tanggal:</strong> <?= esc(format_date_id($row['reservation_date'])) ?></div>
            <div class="detail-row"><strong>Waktu:</strong> <?= esc(substr($row['start_hour'], 0, 5)) ?> - <?= esc(substr($row['end_hour'], 0, 5)) ?></div>
            <div class="detail-row"><strong>Status:</strong> <?= esc(status_label($row['status'])) ?></div>
            <div class="detail-row"><strong>Keperluan:</strong> <?= esc($row['reason']) ?></div>
            <div class="detail-row"><strong>Dibuat:</strong> <?= esc(format_date_id($row['created_at'])) ?></div>
            <div class="detail-row"><strong>Disetujui oleh:</strong> <?= esc($row['approved_by_name'] ?: '-') ?></div>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include '../../includes/user/footer_landing.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
