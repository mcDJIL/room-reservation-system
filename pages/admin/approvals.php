<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$active = 'peminjaman';
$crumbs = 'Manajemen | Peminjaman';
?>
<!DOCTYPE html>
<html lang="en">

<head>
        <?php include __DIR__ . '/../../includes/header.php'; ?>
</head>

<body data-active="<?php echo htmlspecialchars($active); ?>" data-crumbs="<?php echo htmlspecialchars($crumbs); ?>">
    <div class="shell">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        <div class="main">
            <?php include __DIR__ . '/../../includes/navbar.php'; ?>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <h1 class="hero-title">Peminjaman</h1>
                    </div>
                    <div class="hero-actions">
                        <button id="btn-add" class="btn btn-primary d-flex s">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Pinjaman
                        </button>
                    </div>
                </section>

                <div class="grid">
                    <section class="col-12 card">
                        <div class="data-toolbar">
                            <div class="data-toolbar-left">
                                <div class="input-icon" style="flex: 1; max-width: 320px;">
                                    <span class="ico"><svg viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="7" />
                                            <path d="m21 21-4.3-4.3" />
                                        </svg></span>
                                    <input class="input" type="search"
                                        placeholder="Search users by name, email, or ID...">
                                </div>
                            </div>
                            <div class="data-toolbar-right">
                                <select class="select"
                                    style="width: auto; padding: 7px 28px 7px 10px; font-size: 12px;">
                                    <option>All status</option>
                                    <option>Active</option>
                                    <option>Pending</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <table class="data-table" style="margin: 0 22px; min-width: 900px;">
                                <thead>
                                    <tr>
                                        <th class="sorted-asc">Nama Peminjam <span class="sort"><svg viewBox="0 0 24 24">
                                                    <path d="m6 9 6 6 6-6" />
                                                </svg></span></th>
                                        <th>Gedung</th>
                                        <th>Ruangan</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="data-row is-selected"
                                        data-id="1"
                                        data-user-id="2"
                                        data-user-name="Dina Prameswari"
                                        data-building="Gedung Pascasarjana"
                                        data-room-id="1"
                                        data-room-name="Auditorium Lt. 6"
                                        data-date="2026-05-20"
                                        data-start="08:00"
                                        data-end="11:00"
                                        data-reason="Seminar proposal tugas akhir."
                                        data-status="waiting"
                                        data-approved-by="Admin Utama">
                                        <td>
                                            <div class="fw-bold">Dina Prameswari</div>
                                            <div class="small text-muted">dina.prameswari@rentroom.test</div>
                                        </td>
                                        <td><span class="badge primary">Gedung Pascasarjana</span></td>
                                        <td>Auditorium Lt. 6</td>
                                        <td>2026-05-20</td>
                                        <td>08:00 - 11:00</td>
                                        <td><span class="badge warning dot">Waiting</span></td>
                                        <td>
                                            <div class="data-cell-actions">
                                                <button class="btn--icon btn-view" aria-label="View" data-id="1">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                                <button class="btn--icon btn-edit" aria-label="Edit" data-id="1">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </button>
                                                <button class="btn--icon btn-delete" aria-label="Delete" data-id="1">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="data-foot">
                            <div class="data-foot-info">
                                <span>Showing <strong style="color: var(--t-base);">1–15</strong> of <strong
                                        style="color: var(--t-base);">142</strong></span>
                                <select class="select">
                                    <option>15 per page</option>
                                    <option>25 per page</option>
                                    <option>50 per page</option>
                                    <option>100 per page</option>
                                </select>
                            </div>
                            <div class="pager">
                                <button class="pager-btn" disabled="disabled" aria-label="Previous">
                                    <svg viewBox="0 0 24 24">
                                        <path d="m15 18-6-6 6-6" />
                                    </svg>
                                </button>
                                <button class="pager-btn is-active">1</button>
                                <button class="pager-btn">2</button>
                                <button class="pager-btn">3</button>
                                <button class="pager-btn">…</button>
                                <button class="pager-btn">10</button>
                                <button class="pager-btn" aria-label="Next">
                                    <svg viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>

        <!-- Bootstrap modals -->

        <!-- Add Modal -->
        <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddLabel">Tambah Peminjaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-add">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Peminjam</label>
                                    <select class="form-select" id="add-user" name="user_id" required>
                                        <option value="2">Dina Prameswari</option>
                                        <option value="3">Bima Pratama</option>
                                        <option value="4">Salsabila Rahman</option>
                                        <option value="5">Fahri Maulana</option>
                                        <option value="6">Nabila Zahra</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ruangan</label>
                                    <select class="form-select" id="add-room" name="room_id" required>
                                        <option value="1">Auditorium Lt. 6</option>
                                        <option value="2">Ruang Sidang Pascasarjana</option>
                                        <option value="3">Ruang Kelas D4-101</option>
                                        <option value="4">Mini Teater D4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="add-date" name="reservation_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jam Mulai</label>
                                    <input type="time" class="form-control" id="add-start" name="start_hour" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jam Selesai</label>
                                    <input type="time" class="form-control" id="add-end" name="end_hour" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alasan</label>
                                    <textarea class="form-control" id="add-reason" name="reason" rows="4" placeholder="Jelaskan kebutuhan peminjaman"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="add-status" name="status">
                                        <option value="waiting">Waiting</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Ruangan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetailLabel">Detail Peminjaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="detail-card">
                            <p><strong>Nama Peminjam:</strong> <span id="d-name"></span></p>
                            <p><strong>Email:</strong> <span id="d-email"></span></p>
                            <p><strong>Gedung:</strong> <span id="d-building"></span></p>
                            <p><strong>Ruangan:</strong> <span id="d-room"></span></p>
                            <p><strong>Tanggal:</strong> <span id="d-date"></span></p>
                            <p><strong>Jam:</strong> <span id="d-time"></span></p>
                            <p><strong>Alasan:</strong> <span id="d-reason"></span></p>
                            <p><strong>Status:</strong> <span id="d-status"></span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" id="btn-reject">Tolak</button>
                        <button type="button" class="btn btn-success" id="btn-approve">Setuju</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Peminjaman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-edit">
                        <div class="modal-body">
                            <input type="hidden" id="e-id" name="id">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Peminjam</label>
                                    <select class="form-select" id="e-user" name="user_id" required>
                                        <option value="2">Dina Prameswari</option>
                                        <option value="3">Bima Pratama</option>
                                        <option value="4">Salsabila Rahman</option>
                                        <option value="5">Fahri Maulana</option>
                                        <option value="6">Nabila Zahra</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ruangan</label>
                                    <select class="form-select" id="e-room" name="room_id" required>
                                        <option value="1">Auditorium Lt. 6</option>
                                        <option value="2">Ruang Sidang Pascasarjana</option>
                                        <option value="3">Ruang Kelas D4-101</option>
                                        <option value="4">Mini Teater D4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="e-date" name="reservation_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jam Mulai</label>
                                    <input type="time" class="form-control" id="e-start" name="start_hour" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jam Selesai</label>
                                    <input type="time" class="form-control" id="e-end" name="end_hour" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alasan</label>
                                    <textarea class="form-control" id="e-reason" name="reason" rows="4"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="e-status" name="status">
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

        <!-- Delete Modal -->
        <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus peminjaman milik <strong id="del-name"></strong>? Aksi ini
                            tidak dapat dikembalikan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="del-confirm">Hapus
                            Permanen</button>
                    </div>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/../../includes/script.php'; ?>
</body>

</html>