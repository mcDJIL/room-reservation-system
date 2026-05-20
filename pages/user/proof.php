<?php
session_start();
include '../../actions/history/proof_read.php';

function esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_date_local($value)
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Bukti Reservasi - SatSet</title>
  <link rel="stylesheet" href="../../assets/css/user/proof.css">
</head>
<body>
  <main class="proof-page">
    <?php if ($proof_error !== ''): ?>
      <section class="proof-card error-card">
        <h1>Gagal Memuat Bukti</h1>
        <p><?= esc($proof_error) ?></p>
        <a class="btn-back" href="history.php">Kembali ke Riwayat</a>
      </section>
    <?php else: ?>
      <section class="proof-card">
        <header class="proof-header">
          <h1>Bukti Reservasi Ruangan</h1>
          <p>SatSet - Sistem Reservasi Ruangan</p>
        </header>

        <div class="proof-grid">
          <div><span>ID Reservasi</span><strong>#<?= esc($proof_data['reservation_id']) ?></strong></div>
          <div><span>Nama Pemesan</span><strong><?= esc($proof_data['user_name']) ?></strong></div>
          <div><span>Ruangan</span><strong><?= esc($proof_data['room_name']) ?></strong></div>
          <div><span>Lokasi</span><strong><?= esc($proof_data['building_name']) ?></strong></div>
          <div><span>Tanggal</span><strong><?= esc(format_date_local($proof_data['reservation_date'])) ?></strong></div>
          <div><span>Waktu</span><strong><?= esc(substr($proof_data['start_hour'], 0, 5)) ?> - <?= esc(substr($proof_data['end_hour'], 0, 5)) ?></strong></div>
          <div><span>Status</span><strong class="status-approved">Disetujui</strong></div>
          <div><span>Disetujui Oleh</span><strong><?= esc($proof_data['approved_by_name'] ?: '-') ?></strong></div>
        </div>

        <section class="proof-reason">
          <h2>Keperluan</h2>
          <p><?= esc($proof_data['reason'] ?: '-') ?></p>
        </section>

        <footer class="proof-footer">
          <span>Dibuat pada <?= esc(format_date_local($proof_data['created_at'])) ?></span>
          <div class="proof-actions no-print">
            <button type="button" onclick="window.print()">Cetak</button>
            <a href="history.php">Kembali</a>
          </div>
        </footer>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
