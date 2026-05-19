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
        alert(isApprovalsPage ? 'Perubahan peminjaman disimpan (implementasikan penyimpanan)' : 'Perubahan disimpan (implementasikan penyimpanan)');
        if (modalEdit) modalEdit.hide();
    });

    document.querySelectorAll('.btn-delete').forEach(btn => btn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="' + id + '"]');
        if (!row || !modalDelete) return;
        document.getElementById('del-name').textContent = isApprovalsPage ? (row.dataset.userName || row.dataset.name || '') : (row.dataset.name || '');
        const delConfirm = document.getElementById('del-confirm');
        if (delConfirm) delConfirm.dataset.id = id;
        modalDelete.show();
    }));

    const delConfirmBtn = document.getElementById('del-confirm');
    if (delConfirmBtn) delConfirmBtn.addEventListener('click', function(){
        alert(isApprovalsPage ? 'Peminjaman dihapus (implementasikan penghapusan)' : 'Item dihapus (implementasikan penghapusan)');
        if (modalDelete) modalDelete.hide();
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
