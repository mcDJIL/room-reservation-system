// Shared admin modal handlers for rooms and approvals pages.
(function(){
    const modalAddEl = document.getElementById('modalAdd');
    const modalDetailEl = document.getElementById('modalDetail');
    const modalEditEl = document.getElementById('modalEdit');
    const modalDeleteEl = document.getElementById('modalDelete');

    const modalAdd = modalAddEl ? new bootstrap.Modal(modalAddEl) : null;
    const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
    const modalEdit = modalEditEl ? new bootstrap.Modal(modalEditEl) : null;
    const modalDelete = modalDeleteEl ? new bootstrap.Modal(modalDeleteEl) : null;

    const isRoomsPage = !!document.getElementById('add-room') || !!document.getElementById('add-building') && !!document.getElementById('add-facility');
    const isApprovalsPage = !!document.getElementById('add-user') || !!document.getElementById('add-date');

    function statusLabel(status) {
        const value = String(status || '').toLowerCase();
        if (value === 'approved') return 'Approved';
        if (value === 'rejected') return 'Rejected';
        return 'Waiting';
    }

    function statusClass(status) {
        const value = String(status || '').toLowerCase();
        if (value === 'approved') return 'success';
        if (value === 'rejected') return 'danger';
        return 'warning';
    }

    if (btnExists('btn-add') && modalAdd) {
        document.getElementById('btn-add').addEventListener('click', () => {
            const form = document.getElementById('form-add');
            if (form) form.reset();
            modalAdd.show();
        });
    }

    const formAdd = document.getElementById('form-add');
    if (formAdd) formAdd.addEventListener('submit', function(e){
        e.preventDefault();
        alert(isApprovalsPage ? 'Peminjaman disubmit (implementasikan penyimpanan)' : 'Form tambah disubmit (implementasikan penyimpanan)');
        if (modalAdd) modalAdd.hide();
    });

    document.querySelectorAll('.btn-view').forEach(btn => btn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="' + id + '"]');
        if (!row || !modalDetail) return;

        if (isApprovalsPage) {
            const userName = row.dataset.userName || row.dataset.name || '';
            const userEmail = row.dataset.userEmail || '';
            const building = row.dataset.building || '';
            const roomName = row.dataset.roomName || '';
            const date = row.dataset.date || '';
            const start = row.dataset.start || '';
            const end = row.dataset.end || '';
            const reason = row.dataset.reason || '';
            const status = row.dataset.status || 'waiting';

            document.getElementById('d-name').textContent = userName;
            document.getElementById('d-email').textContent = userEmail;
            document.getElementById('d-building').textContent = building;
            document.getElementById('d-room').textContent = roomName;
            document.getElementById('d-date').textContent = date;
            document.getElementById('d-time').textContent = (start || '-') + ' - ' + (end || '-');
            document.getElementById('d-reason').textContent = reason;
            document.getElementById('d-status').textContent = statusLabel(status);

            const approveBtn = document.getElementById('btn-approve');
            const rejectBtn = document.getElementById('btn-reject');
            if (approveBtn) approveBtn.dataset.id = id;
            if (rejectBtn) rejectBtn.dataset.id = id;
            modalDetail.show();
            return;
        }

        document.getElementById('d-name').textContent = row.dataset.name || '';
        document.getElementById('d-building').textContent = row.dataset.building || '';
        document.getElementById('d-capacity').textContent = row.dataset.capacity || '';
        document.getElementById('d-status').textContent = statusLabel(row.dataset.status || '');
        modalDetail.show();
    }));

    document.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="' + id + '"]');
        if (!row || !modalEdit) return;

        document.getElementById('e-id').value = id;

        if (isApprovalsPage) {
            const userSelect = document.getElementById('e-user');
            const roomSelect = document.getElementById('e-room');
            if (userSelect) userSelect.value = row.dataset.userId || '';
            if (roomSelect) roomSelect.value = row.dataset.roomId || '';
            document.getElementById('e-date').value = row.dataset.date || '';
            document.getElementById('e-start').value = row.dataset.start || '';
            document.getElementById('e-end').value = row.dataset.end || '';
            document.getElementById('e-reason').value = row.dataset.reason || '';
            document.getElementById('e-status').value = row.dataset.status || 'waiting';
            modalEdit.show();
            return;
        }

        document.getElementById('e-name').value = row.dataset.name || '';
        document.getElementById('e-building').value = row.dataset.building || '';
        document.getElementById('e-capacity').value = row.dataset.capacity || '';
        document.getElementById('e-status').value = row.dataset.status || '';
        modalEdit.show();
    }));

    const formEdit = document.getElementById('form-edit');
    if (formEdit) formEdit.addEventListener('submit', function(e){
        e.preventDefault();
        const id = document.getElementById('e-id').value;
        if (!id) return;
        const payload = new URLSearchParams();
        if (isApprovalsPage) {
            // approvals handling not implemented here
            alert('Perubahan peminjaman disimpan (belum diimplementasikan)');
            if (modalEdit) modalEdit.hide();
            return;
        }

        payload.append('id', id);
        payload.append('name', document.getElementById('e-name').value || '');
        payload.append('building', document.getElementById('e-building').value || '');
        payload.append('capacity', document.getElementById('e-capacity').value || '0');
        payload.append('status', document.getElementById('e-status').value || 'Tidak Aktif');

        fetch('../../actions/room/update.php', {
            method: 'POST',
            body: payload,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(r => r.json()).then(data => {
            if (!data || !data.success) {
                alert('Gagal menyimpan: ' + (data && data.message ? data.message : 'Unknown'));
                return;
            }
            const row = document.querySelector('tr[data-id="' + id + '"]');
            if (!row) return;
            const room = data.room || {};
            row.dataset.name = room.room_name || '';
            row.dataset.building = room.building_name || '';
            row.dataset.capacity = room.capacity || '';
            row.dataset.status = (room.is_active == 1) ? 'Aktif' : 'Tidak Aktif';

            // update table cells
            const nameCell = row.querySelector('td:nth-child(1) .fw-bold');
            if (nameCell) nameCell.textContent = room.room_name || '';
            const buildingCell = row.querySelector('td:nth-child(2) .badge');
            if (buildingCell) buildingCell.textContent = room.building_name || 'Tanpa Gedung';
            const capacityCell = row.querySelector('td:nth-child(3)');
            if (capacityCell) capacityCell.textContent = (room.capacity || 0) + ' Orang';
            const statusBadge = row.querySelector('td:nth-child(4) .badge');
            if (statusBadge) {
                statusBadge.classList.remove('success','danger','primary','warning');
                statusBadge.classList.add((room.is_active == 1) ? 'success' : 'danger');
                statusBadge.textContent = (room.is_active == 1) ? 'Aktif' : 'Tidak Aktif';
            }

            if (modalEdit) modalEdit.hide();
        }).catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan');
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => btn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="' + id + '"]');
        if (!row || !modalDelete) return;
        document.getElementById('del-name').textContent = isApprovalsPage ? (row.dataset.userName || row.dataset.name || '') : (row.dataset.name || '');
        const delId = document.getElementById('del-id');
        if (delId) delId.value = id;
        modalDelete.show();
    }));

    const formDelete = document.getElementById('form-delete');
    if (formDelete) formDelete.addEventListener('submit', function(e){
        e.preventDefault();
        const id = document.getElementById('del-id').value;
        if (!id) return;
        if (isApprovalsPage) {
            alert('Penghapusan peminjaman belum diimplementasikan');
            if (modalDelete) modalDelete.hide();
            return;
        }

        const payload = new URLSearchParams();
        payload.append('id', id);
        fetch('../../actions/room/delete.php', {
            method: 'POST',
            body: payload,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(r => r.json()).then(data => {
            if (!data || !data.success) {
                alert('Gagal menghapus: ' + (data && data.message ? data.message : 'Unknown'));
                return;
            }
            const row = document.querySelector('tr[data-id="' + id + '"]');
            if (row) row.remove();
            if (modalDelete) modalDelete.hide();
        }).catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan saat menghapus');
        });
    });

    // Approve / Reject actions in detail modal
    const approveBtn = document.getElementById('btn-approve');
    if (approveBtn) approveBtn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="' + id + '"]');
        if (!row) return;
        row.dataset.status = 'approved';
        setStatusBadge(row, 'approved');
        document.getElementById('d-status').textContent = statusLabel('approved');
        if (modalDetail) modalDetail.hide();
    });

    const rejectBtn = document.getElementById('btn-reject');
    if (rejectBtn) rejectBtn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="' + id + '"]');
        if (!row) return;
        row.dataset.status = 'rejected';
        setStatusBadge(row, 'rejected');
        document.getElementById('d-status').textContent = statusLabel('rejected');
        if (modalDetail) modalDetail.hide();
    });

    function setStatusBadge(row, status) {
        const badge = row.querySelector('.badge.success, .badge.warning, .badge.danger, .badge.primary');
        if (!badge) return;
        badge.classList.remove('success', 'warning', 'danger', 'primary');
        const cls = statusClass(status);
        badge.classList.add(cls);
        badge.textContent = statusLabel(status);
    }

    function btnExists(id) { return !!document.getElementById(id); }
})();
