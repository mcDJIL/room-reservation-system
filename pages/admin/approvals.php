<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$active = 'peminjaman';
$crumbs = 'Manajemen | Peminjaman';

require_once __DIR__ . '/../../config/connection.php';

// ── Search / filter / pagination ──────────────────────────────────────────────
$q       = trim($_GET['q']      ?? '');
$status_f = trim($_GET['status'] ?? '');
$page    = max(1, intval($_GET['page'] ?? 1));
$per     = 15;

$allowed_status = ['waiting', 'approved', 'rejected'];
if (!in_array($status_f, $allowed_status, true)) $status_f = '';

$where  = '1';
$types  = '';
$params = [];

if ($q !== '') {
    $where   .= " AND (u.name LIKE ? OR u.email LIKE ? OR rm.room_name LIKE ? OR b.name LIKE ?)";
    $like     = '%' . $q . '%';
    $types   .= 'ssss';
    $params   = array_merge($params, [$like, $like, $like, $like]);
}
if ($status_f !== '') {
    $where  .= ' AND r.status = ?';
    $types  .= 's';
    $params[] = $status_f;
}

$from = "FROM reservations r
         JOIN users u  ON u.id  = r.user_id
         JOIN rooms rm ON rm.id = r.room_id
         JOIN buildings b ON b.id = rm.building_id
         WHERE $where";

// total count
$cnt_sql  = "SELECT COUNT(*) $from";
$cnt_stmt = $conn->prepare($cnt_sql);
if ($types) $cnt_stmt->bind_param($types, ...$params);
$cnt_stmt->execute();
$total = $cnt_stmt->get_result()->fetch_row()[0];
$cnt_stmt->close();

$total_pages = max(1, (int) ceil($total / $per));
$page        = min($page, $total_pages);
$offset      = ($page - 1) * $per;

// data
$data_sql  = "SELECT r.id, u.name AS user_name, u.email AS user_email,
                     b.name AS building_name, rm.room_name,
                     r.reservation_date, r.start_hour, r.end_hour,
                     r.reason, r.status, r.user_id, r.room_id
              $from ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
$data_stmt = $conn->prepare($data_sql);
$d_types   = $types . 'ii';
$d_params  = array_merge($params, [$per, $offset]);
$data_stmt->bind_param($d_types, ...$d_params);
$data_stmt->execute();
$reservations = $data_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$data_stmt->close();

// dropdowns for modals
$users = $conn->query("SELECT id, name FROM users WHERE role='user' ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$rooms = $conn->query("SELECT r.id, r.room_name, b.name AS building_name
                       FROM rooms r JOIN buildings b ON b.id=r.building_id
                       WHERE r.is_active=1 ORDER BY r.room_name")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include __DIR__ . '/../../includes/header.php'; ?></head>
<body data-active="<?= htmlspecialchars($active) ?>" data-crumbs="<?= htmlspecialchars($crumbs) ?>">
<div class="shell">
    <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
    <div class="main">
        <?php include __DIR__ . '/../../includes/navbar.php'; ?>
        <main class="content">
            <section class="hero">
                <div class="hero-text"><h1 class="hero-title">Peminjaman</h1></div>
            </section>
            <div class="grid">
                <section class="col-12 card">
                    <!-- Toolbar -->
                    <form method="get" id="filter-form">
                        <div class="data-toolbar">
                            <div class="data-toolbar-left">
                                <div class="input-icon" style="flex:1;max-width:320px;">
                                    <span class="ico"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></span>
                                    <input class="input" type="search" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama, email, ruangan...">
                                </div>
                            </div>
                            <div class="data-toolbar-right">
                                <select class="select" name="status" onchange="this.form.submit()" style="width:auto;padding:7px 28px 7px 10px;font-size:12px;">
                                    <option value="">Semua status</option>
                                    <option value="waiting"  <?= $status_f==='waiting'  ? 'selected':'' ?>>Waiting</option>
                                    <option value="approved" <?= $status_f==='approved' ? 'selected':'' ?>>Approved</option>
                                    <option value="rejected" <?= $status_f==='rejected' ? 'selected':'' ?>>Rejected</option>
                                </select>
                                <button type="submit" class="btn btn-primary" style="font-size:12px;padding:7px 14px;">Cari</button>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div>
                        <table class="data-table" style="margin:0 22px;min-width:900px;">
                            <thead>
                                <tr>
                                    <th>Nama Peminjam</th>
                                    <th>Gedung</th>
                                    <th>Ruangan</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reservations as $row):
                                $sc    = $row['status']==='approved' ? 'success' : ($row['status']==='rejected' ? 'danger' : 'warning');
                                $start = substr($row['start_hour'], 0, 5);
                                $end   = substr($row['end_hour'],   0, 5);
                            ?>
                            <tr class="data-row"
                                data-id="<?= $row['id'] ?>"
                                data-user-id="<?= $row['user_id'] ?>"
                                data-user-name="<?= htmlspecialchars($row['user_name']) ?>"
                                data-user-email="<?= htmlspecialchars($row['user_email']) ?>"
                                data-building="<?= htmlspecialchars($row['building_name']) ?>"
                                data-room-id="<?= $row['room_id'] ?>"
                                data-room-name="<?= htmlspecialchars($row['room_name']) ?>"
                                data-date="<?= $row['reservation_date'] ?>"
                                data-start="<?= $start ?>"
                                data-end="<?= $end ?>"
                                data-reason="<?= htmlspecialchars($row['reason']) ?>"
                                data-status="<?= $row['status'] ?>">
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['user_name']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($row['user_email']) ?></div>
                                </td>
                                <td><span class="badge primary"><?= htmlspecialchars($row['building_name']) ?></span></td>
                                <td><?= htmlspecialchars($row['room_name']) ?></td>
                                <td><?= $row['reservation_date'] ?></td>
                                <td><?= $start ?> - <?= $end ?></td>
                                <td><span class="badge <?= $sc ?> dot"><?= ucfirst($row['status']) ?></span></td>
                                <td>
                                    <div class="data-cell-actions">
                                        <button class="btn--icon btn-view"   aria-label="View"   data-id="<?= $row['id'] ?>"><i class="fa-regular fa-eye"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($reservations)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="data-foot">
                        <div class="data-foot-info">
                            <?php
                            $from_n = $total > 0 ? ($page-1)*$per+1 : 0;
                            $to_n   = min($page*$per, $total);
                            ?>
                            <span>Showing <strong style="color:var(--t-base)"><?= $from_n ?>–<?= $to_n ?></strong> of <strong style="color:var(--t-base)"><?= $total ?></strong></span>
                        </div>
                        <?php if ($total_pages > 1): ?>
                        <div class="pager">
                            <?php
                            $qs = function($p) use ($q, $status_f) {
                                return '?' . http_build_query(array_filter(['q'=>$q,'status'=>$status_f,'page'=>$p], fn($v)=>$v!==''));
                            };
                            ?>
                            <a href="<?= $qs($page-1) ?>" class="pager-btn <?= $page<=1?'disabled':'' ?>" <?= $page<=1?'aria-disabled="true"':'' ?>>
                                <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                            </a>
                            <?php for ($p=1; $p<=$total_pages; $p++): ?>
                            <a href="<?= $qs($p) ?>" class="pager-btn <?= $p===$page?'is-active':'' ?>"><?= $p ?></a>
                            <?php endfor; ?>
                            <a href="<?= $qs($page+1) ?>" class="pager-btn <?= $page>=$total_pages?'disabled':'' ?>" <?= $page>=$total_pages?'aria-disabled="true"':'' ?>>
                                <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
        <?php include __DIR__ . '/../../includes/footer.php'; ?>
    </div>

    <!-- ── Add Modal ─────────────────────────────────────────────────────── -->
    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddLabel">Tambah Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-add">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Peminjam</label>
                                <select class="form-select" name="user_id" required>
                                    <option value="">-- Pilih Peminjam --</option>
                                    <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ruangan</label>
                                <select class="form-select" name="room_id" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    <?php foreach ($rooms as $rm): ?>
                                    <option value="<?= $rm['id'] ?>"><?= htmlspecialchars($rm['room_name']) ?> (<?= htmlspecialchars($rm['building_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="reservation_date" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" name="start_hour" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" name="end_hour" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alasan</label>
                                <textarea class="form-control" name="reason" rows="3"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="waiting">Waiting</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Detail Modal ──────────────────────────────────────────────────── -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama:</strong> <span id="d-name"></span></p>
                    <p><strong>Email:</strong> <span id="d-email"></span></p>
                    <p><strong>Gedung:</strong> <span id="d-building"></span></p>
                    <p><strong>Ruangan:</strong> <span id="d-room"></span></p>
                    <p><strong>Tanggal:</strong> <span id="d-date"></span></p>
                    <p><strong>Jam:</strong> <span id="d-time"></span></p>
                    <p><strong>Alasan:</strong> <span id="d-reason"></span></p>
                    <p><strong>Status:</strong> <span id="d-status"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" id="btn-reject">Tolak</button>
                    <button type="button" class="btn btn-success"        id="btn-approve">Setuju</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Edit Modal ────────────────────────────────────────────────────── -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-edit">
                    <input type="hidden" name="id" id="e-id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Peminjam</label>
                                <select class="form-select" name="user_id" id="e-user" required>
                                    <option value="">-- Pilih Peminjam --</option>
                                    <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ruangan</label>
                                <select class="form-select" name="room_id" id="e-room" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    <?php foreach ($rooms as $rm): ?>
                                    <option value="<?= $rm['id'] ?>"><?= htmlspecialchars($rm['room_name']) ?> (<?= htmlspecialchars($rm['building_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="reservation_date" id="e-date" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" name="start_hour" id="e-start" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" name="end_hour" id="e-end" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alasan</label>
                                <textarea class="form-control" name="reason" id="e-reason" rows="3"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="e-status">
                                    <option value="waiting">Waiting</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Delete Modal ──────────────────────────────────────────────────── -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Hapus peminjaman milik <strong id="del-name"></strong>? Aksi ini tidak dapat dikembalikan.</p>
                    <input type="hidden" id="del-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="del-confirm">Hapus Permanen</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../includes/script.php'; ?>
</body>
</html>
