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
                        <button type="button" id="btn-add" class="btn btn-primary d-flex" data-bs-toggle="modal"
                            data-bs-target="#modalAdd">
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
                                        placeholder="Cari berdasarkan nama ruangan, gedung, atau fasilitas..."
                                        id="search-input">
                                </div>
                            </div>
                            <div class="data-toolbar-right">
                                <form id="status-form" style="display: inline;">
                                    <select name="status" id="status-select" class="select"
                                        style="width: auto; padding: 7px 28px 7px 10px; font-size: 12px;">
                                        <option value="" <?= !isset($_GET['status']) || $_GET['status'] === '' ? 'selected' : '' ?>>Semua Status</option>
                                        <option value="aktif" <?= isset($_GET['status']) && $_GET['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="tidak_aktif" <?= isset($_GET['status']) && $_GET['status'] === 'tidak_aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div>
                            <table class="data-table" style="margin: 0 22px; min-width: 900px;">
                                <thead>
                                    <tr>
                                        <th class="sorted-asc">Nama Ruangan<span class="sort"><svg viewBox="0 0 24 24">
                                                    <path d="m6 9 6 6 6-6" />
                                                </svg></span></th>
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
                                            <tr class="data-row" data-id="<?= $id ?>" data-name="<?= $name ?>"
                                                data-building="<?= $building ?>" data-capacity="<?= $capacity ?>"
                                                    data-status="<?= $status_text ?>" data-active="<?= $room['is_active'] ?>">
                                                <td>
                                                    <div class="fw-bold"><?= $name ?></div>
                                                </td>
                                                <td><span class="badge primary"><?= $building ?></span></td>
                                                <td><?= $capacity ?> Orang</td>
                                                <td><span class="badge <?= $status_class ?> dot"><?= $status_text ?></span></td>
                                                <td>
                                                    <div class="data-cell-actions">
                                                        <button class="btn--icon btn-view" aria-label="View"
                                                            data-id="<?= $id ?>">
                                                            <i class="fa-regular fa-eye"></i>
                                                        </button>
                                                        <button class="btn--icon btn-edit" aria-label="Edit"
                                                            data-id="<?= $id ?>">
                                                            <i class="fa-regular fa-pen-to-square"></i>
                                                        </button>
                                                        <button class="btn--icon btn-delete" aria-label="Delete"
                                                            data-id="<?= $id ?>">
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
                                <span>Showing <strong style="color: var(--t-base);"
                                        id="pag-from"><?= $pagination['from'] ?></strong>–<strong
                                        style="color: var(--t-base);" id="pag-to"><?= $pagination['to'] ?></strong> of
                                    <strong style="color: var(--t-base);"
                                        id="pag-total"><?= $pagination['total_rows'] ?></strong></span>
                                <select class="select" id="per-page-select">
                                    <option value="15" <?= $per_page === 15 ? 'selected' : '' ?>>15 per page</option>
                                    <option value="25" <?= $per_page === 25 ? 'selected' : '' ?>>25 per page</option>
                                    <option value="50" <?= $per_page === 50 ? 'selected' : '' ?>>50 per page</option>
                                    <option value="100" <?= $per_page === 100 ? 'selected' : '' ?>>100 per page</option>
                                </select>
                            </div>
                            <div class="pager" id="pager-container">
                                <button class="pager-btn pager-prev" id="pager-prev" <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?> aria-label="Previous">
                                    <svg viewBox="0 0 24 24">
                                        <path d="m15 18-6-6 6-6" />
                                    </svg>
                                </button>
                                <div id="pager-pages"></div>
                                <button class="pager-btn pager-next" id="pager-next" <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?> aria-label="Next">
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
            // Real-time search functionality
            const searchInput = document.getElementById('search-input');
            const statusFilter = document.querySelector('select[name="status"]');
            const perPageSelect = document.getElementById('per-page-select');
            const tbody = document.querySelector('table tbody');
            const pagerContainer = document.getElementById('pager-container');
            const pagerPages = document.getElementById('pager-pages');
            const pagFrom = document.getElementById('pag-from');
            const pagTo = document.getElementById('pag-to');
            const pagTotal = document.getElementById('pag-total');
            const pagerPrev = document.getElementById('pager-prev');
            const pagerNext = document.getElementById('pager-next');
            const initialPagination = <?= json_encode($pagination) ?>;

            let currentPage = initialPagination.current_page || 1;
            let perPage = initialPagination.per_page || 15;
            let searchTimeout;

            function renderPagination(pagination) {
                pagerPages.innerHTML = '';
                const total = pagination.total_pages;
                const current = pagination.current_page;

                const pages = [];

                if (total <= 7) {
                    for (let i = 1; i <= total; i++) pages.push(i);
                } else {
                    pages.push(1);

                    const start = Math.max(2, current - 1);
                    const end = Math.min(total - 1, current + 1);

                    if (start > 2) pages.push('...');
                    for (let i = start; i <= end; i++) pages.push(i);
                    if (end < total - 1) pages.push('...');

                    pages.push(total);
                }

                // Tombol angka halaman
                pages.forEach((pageItem) => {
                    if (pageItem === '...') {
                        const gap = document.createElement('span');
                        gap.className = 'pager-gap';
                        gap.textContent = '...';
                        pagerPages.appendChild(gap);
                        return;
                    }

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'pager-btn' + (pageItem === current ? ' is-active' : '');
                    btn.textContent = pageItem;
                    btn.setAttribute('aria-label', `Halaman ${pageItem}`);
                    btn.addEventListener('click', () => goToPage(pageItem));
                    pagerPages.appendChild(btn);
                });

                // Update prev/next buttons
                pagerPrev.disabled = current <= 1;
                pagerNext.disabled = current >= total;

                // Update info
                pagFrom.textContent = pagination.from;
                pagTo.textContent = pagination.to;
                pagTotal.textContent = pagination.total_rows;

                currentPage = current;
            }

            async function performSearch(query = '', status = '', page = 1) {
                try {
                    const params = new URLSearchParams();
                    if (query) params.append('q', query);
                    if (status) params.append('status', status);
                    params.append('page', page);
                    params.append('per_page', perPage);

                    const response = await fetch('../../actions/room/search.php?' + params.toString());
                    const result = await response.json();

                    if (result.success) {
                        const data = result.data;
                        const pagination = result.pagination;

                        if (data.length === 0 && page === 1) {
                            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Tidak ada data ruangan.</td></tr>';
                            pagerPages.innerHTML = '';
                            pagerPrev.disabled = true;
                            pagerNext.disabled = true;
                            pagFrom.textContent = '0';
                            pagTo.textContent = '0';
                            pagTotal.textContent = '0';
                            return;
                        }

                        tbody.innerHTML = data.map(room => {
                            const statusText = room.is_active == 1 ? 'Aktif' : 'Tidak Aktif';
                            const statusClass = room.is_active == 1 ? 'success' : 'danger';
                            const building = room.building_name || 'Tanpa Gedung';

                            return `<tr class="data-row" data-id="${room.id}" data-name="${room.room_name}" data-building="${building}" data-capacity="${room.capacity}" data-status="${statusText}" data-active="${room.is_active}">
                            <td><div class="fw-bold">${room.room_name}</div></td>
                            <td><span class="badge primary">${building}</span></td>
                            <td>${room.capacity} Orang</td>
                            <td><span class="badge ${statusClass} dot">${statusText}</span></td>
                            <td>
                                <div class="data-cell-actions">
                                    <button class="btn--icon btn-view" aria-label="View" data-id="${room.id}">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    <button class="btn--icon btn-edit" aria-label="Edit" data-id="${room.id}">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn--icon btn-delete" aria-label="Delete" data-id="${room.id}">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                        }).join('');

                        renderPagination(pagination);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Error mengambil data.</td></tr>';
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Error melakukan pencarian.</td></tr>';
                }
            }

            function goToPage(page) {
                const query = searchInput ? searchInput.value : '';
                const status = statusFilter ? statusFilter.value : '';
                performSearch(query, status, page);
            }

            // Search on input dengan debounce
            if (searchInput) {
                searchInput.addEventListener('keyup', () => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const query = searchInput.value;
                        const status = statusFilter ? statusFilter.value : '';
                        performSearch(query, status, 1);
                    }, 300);
                });
            }

            // Search on status filter change
            if (statusFilter) {
                statusFilter.addEventListener('change', () => {
                    const query = searchInput ? searchInput.value : '';
                    const status = statusFilter.value;
                    performSearch(query, status, 1);
                });
            }

            // Per-page change
            if (perPageSelect) {
                perPageSelect.addEventListener('change', () => {
                    perPage = parseInt(perPageSelect.value);
                    const query = searchInput ? searchInput.value : '';
                    const status = statusFilter ? statusFilter.value : '';
                    performSearch(query, status, 1);
                });
            }

            // Prev button
            if (pagerPrev) {
                pagerPrev.type = 'button';
                pagerPrev.addEventListener('click', () => {
                    if (currentPage > 1) goToPage(currentPage - 1);
                });
            }

            // Next button
            if (pagerNext) {
                pagerNext.type = 'button';
                pagerNext.addEventListener('click', () => {
                    goToPage(currentPage + 1);
                });
            }

            if (perPageSelect) {
                perPageSelect.value = String(perPage);
            }

            renderPagination(initialPagination);

            const modalDetailEl = document.getElementById('modalDetail');
            const modalEditEl = document.getElementById('modalEdit');
            const modalDeleteEl = document.getElementById('modalDelete');

            function roomPhotoUrl(photoPath) {
                if (!photoPath) {
                    return 'data:image/svg+xml;utf8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500"><rect width="800" height="500" fill="#e2e8f0"/><rect x="70" y="70" width="660" height="360" rx="28" fill="#f8fafc" stroke="#cbd5e1" stroke-width="8"/><path d="M170 340l110-120 90 92 70-70 140 148H170z" fill="#cbd5e1"/><circle cx="290" cy="185" r="38" fill="#cbd5e1"/><text x="400" y="455" text-anchor="middle" font-family="Arial, sans-serif" font-size="28" fill="#64748b">Gambar ruangan belum tersedia</text></svg>');
                }
                if (photoPath.startsWith('http://') || photoPath.startsWith('https://') || photoPath.startsWith('data:') || photoPath.startsWith('../') || photoPath.startsWith('/')) {
                    return photoPath;
                }
                return '../../assets/images/' + photoPath.replace(/^\/+/, '');
            }

            function renderRoomGallery(photos, roomId) {
                const gallery = document.getElementById('photo-gallery');
                if (!gallery) return;

                gallery.classList.remove('photo-gallery--empty');
                if (!photos || !photos.length) {
                    gallery.innerHTML = '<div class="photo-gallery-empty">Belum ada foto ruangan.</div>';
                    gallery.classList.add('photo-gallery--empty');
                    return;
                }

                gallery.innerHTML = photos.map((photo) => {
                    const isPrimary = Number(photo.is_primary) === 1;
                    const imgSrc = roomPhotoUrl(photo.photo_url || photo.photo);
                    return `
                        <div class="photo-card ${isPrimary ? 'is-primary' : ''}" data-photo-id="${photo.id}">
                            <img src="${imgSrc}" alt="Foto ruangan" loading="lazy">
                            <div class="photo-card-body">
                                <div class="photo-card-meta">
                                    <span class="photo-card-badge ${isPrimary ? 'primary' : ''}">${isPrimary ? 'Primary' : 'Foto'}</span>
                                </div>
                                <div class="photo-card-actions">
                                    <button type="button" class="btn btn-outline-primary photo-icon-btn btn-set-primary" data-room-id="${roomId}" data-photo-id="${photo.id}" aria-label="Jadikan primary" title="Jadikan primary">
                                        <i class="fa-solid fa-star"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger photo-icon-btn btn-delete-photo" data-room-id="${roomId}" data-photo-id="${photo.id}" aria-label="Hapus foto" title="Hapus foto">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                }).join('');

                gallery.querySelectorAll('.btn-set-primary').forEach((button) => {
                    button.addEventListener('click', function () {
                        const payload = new URLSearchParams({ room_id: this.dataset.roomId, photo_id: this.dataset.photoId });
                        fetch('../../actions/room/set_primary_photo.php', {
                            method: 'POST',
                            body: payload,
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                if (!data.success) {
                                    alert(data.message || 'Gagal mengubah primary');
                                    return;
                                }
                                if (window.showToast) {
                                    window.showToast(data.message || 'Foto utama berhasil diubah', 'success');
                                }
                                loadRoomGallery(this.dataset.roomId);
                            })
                            .catch(() => alert('Terjadi kesalahan jaringan'));
                    });
                });

                gallery.querySelectorAll('.btn-delete-photo').forEach((button) => {
                    button.addEventListener('click', function () {
                        if (!window.confirm('Hapus foto ini?')) return;
                        const payload = new URLSearchParams({ room_id: this.dataset.roomId, photo_id: this.dataset.photoId });
                        fetch('../../actions/room/delete_photo.php', {
                            method: 'POST',
                            body: payload,
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                if (!data.success) {
                                    alert(data.message || 'Gagal menghapus foto');
                                    return;
                                }
                                if (window.showToast) {
                                    window.showToast(data.message || 'Foto berhasil dihapus', 'success');
                                }
                                loadRoomGallery(this.dataset.roomId);
                            })
                            .catch(() => alert('Terjadi kesalahan jaringan'));
                    });
                });
            }

            function loadRoomGallery(roomId) {
                const gallery = document.getElementById('photo-gallery');
                if (!gallery || !roomId) return;

                gallery.classList.add('photo-gallery--empty');
                gallery.innerHTML = '<div class="photo-gallery-empty">Memuat foto...</div>';

                fetch('../../actions/room/photos.php?room_id=' + encodeURIComponent(roomId))
                    .then((response) => response.json())
                    .then((data) => {
                        if (!data.success) {
                            renderRoomGallery([], roomId);
                            return;
                        }
                        renderRoomGallery(data.photos || [], roomId);
                    })
                    .catch(() => {
                        renderRoomGallery([], roomId);
                    });
            }

            function openRoomDetail(row) {
                if (!modalDetailEl || !row) return;

                document.getElementById('d-name').textContent = row.dataset.name || '';
                document.getElementById('d-building').textContent = row.dataset.building || '';
                document.getElementById('d-capacity').textContent = row.dataset.capacity || '';
                const isActive = row.dataset.active;
                document.getElementById('d-status').textContent = typeof isActive !== 'undefined' ? (String(isActive) === '1' ? 'Aktif' : 'Tidak Aktif') : (row.dataset.status || '');

                const photoBtn = document.getElementById('photo-upload-btn');
                if (photoBtn) photoBtn.dataset.roomId = row.dataset.id || '';

                loadRoomGallery(row.dataset.id || '');
                bootstrap.Modal.getOrCreateInstance(modalDetailEl).show();
            }

            function openRoomEdit(row) {
                if (!modalEditEl || !row) return;

                document.getElementById('e-id').value = row.dataset.id || '';
                document.getElementById('e-name').value = row.dataset.name || '';
                document.getElementById('e-building').value = row.dataset.building || '';
                document.getElementById('e-capacity').value = row.dataset.capacity || '';
                document.getElementById('e-status').value = String(row.dataset.active) === '1' ? 'Aktif' : 'Tidak Aktif';

                bootstrap.Modal.getOrCreateInstance(modalEditEl).show();
            }

            function openRoomDelete(row) {
                if (!modalDeleteEl || !row) return;

                document.getElementById('del-id').value = row.dataset.id || '';
                document.getElementById('del-name').textContent = row.dataset.name || '';

                bootstrap.Modal.getOrCreateInstance(modalDeleteEl).show();
            }

            function refreshCurrentRooms() {
                const query = searchInput ? searchInput.value : '';
                const status = statusFilter ? statusFilter.value : '';
                performSearch(query, status, currentPage);
            }

            if (tbody) {
                tbody.addEventListener('click', (event) => {
                    const actionButton = event.target.closest('.btn-view, .btn-edit, .btn-delete');
                    if (!actionButton) return;

                    event.preventDefault();
                    event.stopPropagation();

                    const row = actionButton.closest('tr.data-row');
                    if (!row) return;

                    if (actionButton.classList.contains('btn-view')) {
                        openRoomDetail(row);
                        return;
                    }

                    if (actionButton.classList.contains('btn-edit')) {
                        openRoomEdit(row);
                        return;
                    }

                    openRoomDelete(row);
                }, true);
            }

            const formEdit = document.getElementById('form-edit');
            if (formEdit) {
                formEdit.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const payload = new URLSearchParams({
                        id: document.getElementById('e-id')?.value || '',
                        name: document.getElementById('e-name')?.value || '',
                        building: document.getElementById('e-building')?.value || '',
                        capacity: document.getElementById('e-capacity')?.value || '0',
                        status: document.getElementById('e-status')?.value || 'Tidak Aktif'
                    });

                    fetch('../../actions/room/update.php', {
                        method: 'POST',
                        body: payload,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (!data.success) {
                                alert(data.message || 'Gagal memperbarui ruangan');
                                return;
                            }

                            if (window.showToast) {
                                window.showToast(data.message || 'Ruangan berhasil diperbarui', 'success');
                            }

                            bootstrap.Modal.getOrCreateInstance(modalEditEl).hide();
                            refreshCurrentRooms();
                        })
                        .catch(() => alert('Terjadi kesalahan jaringan'));
                });
            }

            const formDelete = document.getElementById('form-delete');
            if (formDelete) {
                formDelete.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const payload = new URLSearchParams({ id: document.getElementById('del-id')?.value || '' });

                    fetch('../../actions/room/delete.php', {
                        method: 'POST',
                        body: payload,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (!data.success) {
                                alert(data.message || 'Gagal menghapus ruangan');
                                return;
                            }

                            if (window.showToast) {
                                window.showToast(data.message || 'Ruangan berhasil dihapus', 'success');
                            }

                            bootstrap.Modal.getOrCreateInstance(modalDeleteEl).hide();
                            refreshCurrentRooms();
                        })
                        .catch(() => alert('Terjadi kesalahan jaringan'));
                });
            }
            });
        </script>

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
                                    <input type="text" class="form-control form-control-lg" id="add-name"
                                        name="room_name" placeholder="Contoh: Auditorium Lt.6" required>
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
                                    <input type="text" class="form-control" id="add-facility" name="facility"
                                        placeholder="Projector, AC, Mikrofon">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Ringkasan singkat</label>
                                    <input type="text" class="form-control" id="add-short" name="short_description"
                                        maxlength="255" placeholder="Satu kalimat ringkasan">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Deskripsi lengkap</label>
                                    <textarea class="form-control" id="add-detail" name="detail_description" rows="4"
                                        placeholder="Deskripsi lengkap ruangan"></textarea>
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
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
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
                            <div id="photo-gallery" class="photo-gallery photo-gallery--empty">
                                <div class="photo-gallery-empty">Memuat foto...</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unggah Foto</label>
                            <input class="form-control" type="file" id="photo-input" accept="image/*" multiple>
                            <div class="small text-muted mt-1">Unggah beberapa foto sekaligus (maks. 20MB per file, total request menyesuaikan server). Pilih satu foto sebagai
                                primary setelah upload.</div>
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
                    <form action="../../actions/room/update.php" method="post" id="form-edit">
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