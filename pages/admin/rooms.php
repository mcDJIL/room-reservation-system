<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$active = 'ruangan';
$crumbs = 'Manajemen | Ruangan';
?>
<!DOCTYPE html>
<html lang="en">

<head>
        <?php include __DIR__ . '/../../includes/header.php'; ?>
        <?php require_once dirname(__DIR__, 2) . '/actions/room/read.php'; ?>
</head>

<body data-active="<?php echo htmlspecialchars($active); ?>" data-crumbs="<?php echo htmlspecialchars($crumbs); ?>">
    <div class="shell">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        <div class="main">
            <?php include __DIR__ . '/../../includes/navbar.php'; ?>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <h1 class="hero-title">Ruangan</h1>
                    </div>
                    <div class="hero-actions">
                        <button id="btn-add" class="btn btn-primary d-flex s">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Data
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
            <th class="sorted-asc">Nama <span class="sort"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6" /></svg></span></th>
            <th>Gedung</th>
            <th>Kapasitas</th>
            <th>Aktif/Tidak Aktif</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $room): ?>
                <?php 
                    $id = htmlspecialchars($room['id']);
                    $name = htmlspecialchars($room['room_name']);
                    $building = htmlspecialchars($room['building_name'] ?? 'Tanpa Gedung');
                    $capacity = htmlspecialchars($room['capacity']);
                    $status_text = $room['is_active'] == 1 ? 'Aktif' : 'Tidak Aktif';
                    $status_class = $room['is_active'] == 1 ? 'success' : 'danger';
                ?>
                <tr class="data-row" data-id="<?= $id ?>" data-name="<?= $name ?>" data-building="<?= $building ?>" data-capacity="<?= $capacity ?>" data-status="<?= $status_text ?>">
                    <td>
                        <div class="fw-bold"><?= $name ?></div>
                    </td>
                    <td><span class="badge primary"><?= $building ?></span></td>
                    <td><?= $capacity ?> Orang</td>
                    <td><span class="badge <?= $status_class ?> dot"><?= $status_text ?></span></td>
                    <td>
                        <div class="data-cell-actions">
                            <button class="btn--icon btn-view" aria-label="View" data-id="<?= $id ?>">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                            <button class="btn--icon btn-edit" aria-label="Edit" data-id="<?= $id ?>">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                            <button class="btn--icon btn-delete" aria-label="Delete" data-id="<?= $id ?>">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">Tidak ada data ruangan.</td>
            </tr>
        <?php endif; ?>
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
                        <h5 class="modal-title" id="modalAddLabel">Tambah Ruangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="../../actions/room/create.php" method="post" id="form-add">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Ruangan</label>
                                    <input type="text" class="form-control form-control-lg" id="add-name" name="room_name" placeholder="Contoh: Auditorium Lt.6" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gedung</label>
                                    <select class="form-select" id="add-building" name="building_id" required>
                                        <option value="1">Gedung Pascasarjana</option>
                                        <option value="2">Gedung D4</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kapasitas</label>
                                    <input type="number" class="form-control" id="add-capacity" name="capacity" min="0">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Fasilitas (pisahkan dengan koma)</label>
                                    <input type="text" class="form-control" id="add-facility" name="facility" placeholder="Projector, AC, Mikrofon">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Ringkasan singkat</label>
                                    <input type="text" class="form-control" id="add-short" name="short_description" maxlength="255" placeholder="Satu kalimat ringkasan">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Deskripsi lengkap</label>
                                    <textarea class="form-control" id="add-detail" name="detail_description" rows="4" placeholder="Deskripsi lengkap ruangan"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="add-status" name="is_active">
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
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
                        <h5 class="modal-title" id="modalDetailLabel">Detail Ruangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nama:</strong> <span id="d-name"></span></p>
                        <p><strong>Gedung:</strong> <span id="d-building"></span></p>
                        <p><strong>Kapasitas:</strong> <span id="d-capacity"></span></p>
                        <p><strong>Status:</strong> <span id="d-status"></span></p>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Galeri Foto Ruangan</label>
                            <div id="photo-gallery" class="photo-gallery">
                                <!-- Thumbnails appended here -->
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unggah Foto</label>
                            <input class="form-control" type="file" id="photo-input" accept="image/*" multiple>
                            <div class="small text-muted mt-1">Unggah beberapa foto sekaligus. Pilih satu foto sebagai primary setelah upload.</div>
                            <button class="btn btn-sm btn-primary mt-2" id="photo-upload-btn">Upload Foto</button>
                        </div>
                    </div>
                    <div class="modal-footer">
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
                        <h5 class="modal-title" id="modalEditLabel">Edit Ruangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method=update.php id="form-edit">
                        <div class="modal-body">
                            <input type="hidden" id="e-id" name="id">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" id="e-name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gedung</label>
                                <input type="text" class="form-control" id="e-building" name="building" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" id="e-capacity" name="capacity" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="e-status" name="status">
                                    <option>Aktif</option>
                                    <option>Tidak Aktif</option>
                                </select>
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
                    <form action="../../actions/room/delete.php" method="post" id="form-delete">
                        <input type="hidden" id="del-id" name="id" value="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin menghapus <strong id="del-name"></strong>? Aksi ini
                                tidak dapat dikembalikan.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger" id="del-confirm">Hapus
                                Permanen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/../../includes/script.php'; ?>
</body>

</html>