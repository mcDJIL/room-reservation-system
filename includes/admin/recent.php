<?php include __DIR__ . '/recent_query.php'; ?>

<div class="container-fluid px-0 mt-4">
  <section class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-3 pb-0">
      <small class="text-uppercase text-secondary fw-semibold">Aktivitas</small>
      <h5 class="mb-0 mt-1">5 Reservasi Terbaru</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama</th>
              <th>Ruangan</th>
              <th>Tanggal</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
              <?php while($row = mysqli_fetch_assoc($qRecent)) : ?>

              <tr>

                <td class="fw-semibold">
                  <?= $row['user_name']; ?>
                </td>

                <td>
                  <?= $row['room_name']; ?>
                </td>

                <td>
                  <?= $row['reservation_date']; ?>
                </td>

                <td>

                  <?php if($row['status'] == 'waiting') : ?>

                    <span class="badge text-bg-warning">
                      Menunggu
                    </span>

                  <?php elseif($row['status'] == 'approved') : ?>

                    <span class="badge text-bg-success">
                      Disetujui
                    </span>

                  <?php else : ?>

                    <span class="badge text-bg-danger">
                      Ditolak
                    </span>

                  <?php endif; ?>

                </td>

              </tr>

              <?php endwhile; ?>
            </tbody>
        </table>
      </div>
    </div>
  </section>
</div>