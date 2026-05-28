// Shared admin modal handlers for rooms and approvals pages.
(function () {
  const modalAddEl    = document.getElementById('modalAdd');
  const modalDetailEl = document.getElementById('modalDetail');
  const modalEditEl   = document.getElementById('modalEdit');
  const modalDeleteEl = document.getElementById('modalDelete');

  const modalAdd    = modalAddEl    ? new bootstrap.Modal(modalAddEl)    : null;
  const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
  const modalEdit   = modalEditEl   ? new bootstrap.Modal(modalEditEl)   : null;
  const modalDelete = modalDeleteEl ? new bootstrap.Modal(modalDeleteEl) : null;

  // Detect which page we're on
  const isApprovalsPage = !!document.getElementById('d-email');

  // ── Helpers ────────────────────────────────────────────────────────────────
  function statusLabel(s) {
    return s === 'approved' ? 'Approved' : s === 'rejected' ? 'Rejected' : 'Waiting';
  }
  function statusClass(s) {
    return s === 'approved' ? 'success' : s === 'rejected' ? 'danger' : 'warning';
  }
  function setStatusBadge(row, status) {
    const badge = row.querySelector('.badge.success, .badge.warning, .badge.danger');
    if (!badge) return;
    badge.classList.remove('success', 'warning', 'danger');
    badge.classList.add(statusClass(status));
    badge.textContent = statusLabel(status);
  }
  function buildRow(r) {
    const start = r.start_hour.slice(0, 5);
    const end   = r.end_hour.slice(0, 5);
    const sc    = statusClass(r.status);
    const tr    = document.createElement('tr');
    tr.className = 'data-row';
    tr.dataset.id        = r.id;
    tr.dataset.userId    = r.user_id;
    tr.dataset.userName  = r.user_name;
    tr.dataset.userEmail = r.user_email;
    tr.dataset.building  = r.building_name;
    tr.dataset.roomId    = r.room_id;
    tr.dataset.roomName  = r.room_name;
    tr.dataset.date      = r.reservation_date;
    tr.dataset.start     = start;
    tr.dataset.end       = end;
    tr.dataset.reason    = r.reason;
    tr.dataset.status    = r.status;
    tr.innerHTML = `
      <td><div class="fw-bold">${r.user_name}</div><div class="small text-muted">${r.user_email}</div></td>
      <td><span class="badge primary">${r.building_name}</span></td>
      <td>${r.room_name}</td>
      <td>${r.reservation_date}</td>
      <td>${start} - ${end}</td>
      <td><span class="badge ${sc} dot">${statusLabel(r.status)}</span></td>
      <td>
        <div class="data-cell-actions">
          <button class="btn--icon btn-view"   aria-label="View"   data-id="${r.id}"><i class="fa-regular fa-eye"></i></button>
          <button class="btn--icon btn-edit"   aria-label="Edit"   data-id="${r.id}"><i class="fa-regular fa-pen-to-square"></i></button>
          <button class="btn--icon btn-delete" aria-label="Delete" data-id="${r.id}"><i class="fa-regular fa-trash-can"></i></button>
        </div>
      </td>`;
    bindRowButtons(tr);
    return tr;
  }
  function updateRowFromData(row, r) {
    const start = r.start_hour.slice(0, 5);
    const end   = r.end_hour.slice(0, 5);
    row.dataset.userId    = r.user_id;
    row.dataset.userName  = r.user_name;
    row.dataset.userEmail = r.user_email;
    row.dataset.building  = r.building_name;
    row.dataset.roomId    = r.room_id;
    row.dataset.roomName  = r.room_name;
    row.dataset.date      = r.reservation_date;
    row.dataset.start     = start;
    row.dataset.end       = end;
    row.dataset.reason    = r.reason;
    row.dataset.status    = r.status;
    row.querySelector('td:nth-child(1) .fw-bold').textContent    = r.user_name;
    row.querySelector('td:nth-child(1) .text-muted').textContent = r.user_email;
    row.querySelector('td:nth-child(2) .badge').textContent      = r.building_name;
    row.querySelector('td:nth-child(3)').textContent             = r.room_name;
    row.querySelector('td:nth-child(4)').textContent             = r.reservation_date;
    row.querySelector('td:nth-child(5)').textContent             = start + ' - ' + end;
    setStatusBadge(row, r.status);
  }

  // ── Open Add modal ─────────────────────────────────────────────────────────
  const btnAdd = document.getElementById('btn-add');
  if (btnAdd && modalAdd) {
    btnAdd.addEventListener('click', () => {
      document.getElementById('form-add').reset();
      modalAdd.show();
    });
  }

  // ── Submit Add ─────────────────────────────────────────────────────────────
  const formAdd = document.getElementById('form-add');
  if (formAdd) {
    formAdd.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!isApprovalsPage) { handleRoomAdd(this); return; }
      const fd = new FormData(this);
      fd.append('action', 'add');
      fetch('../../actions/booking/reservation_action.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (!data.success) { alert('Gagal: ' + (data.message || 'Unknown')); return; }
          const tbody = document.querySelector('#modalAdd').closest('.card')
                        ? document.querySelector('tbody') : document.querySelector('tbody');
          const empty = document.querySelector('tbody tr td[colspan]');
          if (empty) empty.closest('tr').remove();
          document.querySelector('tbody').prepend(buildRow(data.row));
          modalAdd.hide();
        })
        .catch(() => alert('Terjadi kesalahan jaringan'));
    });
  }

  // ── View / Detail ──────────────────────────────────────────────────────────
  function openDetail(id) {
    const row = document.querySelector('tr[data-id="' + id + '"]');
    if (!row || !modalDetail) return;

    const approveBtn = document.getElementById('btn-approve');
    const rejectBtn  = document.getElementById('btn-reject');

    function setApprovalButtonsVisible(visible) {
      if (approveBtn) {
        approveBtn.hidden = !visible;
        if (!visible) delete approveBtn.dataset.id;
      }
      if (rejectBtn) {
        rejectBtn.hidden = !visible;
        if (!visible) delete rejectBtn.dataset.id;
      }
    }

    if (isApprovalsPage) {
      document.getElementById('d-name').textContent    = row.dataset.userName  || '';
      document.getElementById('d-email').textContent   = row.dataset.userEmail || '';
      document.getElementById('d-building').textContent= row.dataset.building  || '';
      document.getElementById('d-room').textContent    = row.dataset.roomName  || '';
      document.getElementById('d-date').textContent    = row.dataset.date      || '';
      document.getElementById('d-time').textContent    = (row.dataset.start||'-') + ' - ' + (row.dataset.end||'-');
      document.getElementById('d-reason').textContent  = row.dataset.reason    || '';
      document.getElementById('d-status').textContent  = statusLabel(row.dataset.status);

      const canChangeStatus = row.dataset.status === 'waiting';
      setApprovalButtonsVisible(canChangeStatus);

      if (canChangeStatus) {
        if (approveBtn) approveBtn.dataset.id = id;
        if (rejectBtn) rejectBtn.dataset.id = id;
      }

      modalDetail.show();
      return;
    }

    setApprovalButtonsVisible(false);
    // rooms page detail
    document.getElementById('d-name').textContent     = row.dataset.name     || '';
    document.getElementById('d-building').textContent = row.dataset.building || '';
    document.getElementById('d-capacity').textContent = row.dataset.capacity || '';
    const isActive = row.dataset.active;
    if (typeof isActive !== 'undefined') {
      document.getElementById('d-status').textContent = String(isActive) === '1' ? 'Aktif' : 'Tidak Aktif';
    } else {
      document.getElementById('d-status').textContent = row.dataset.status || '';
    }
    const photoBtn = document.getElementById('photo-upload-btn');
    if (photoBtn) photoBtn.dataset.roomId = id;
    modalDetail.show();
  }

  // ── Approve / Reject ───────────────────────────────────────────────────────
  function sendApproval(id, action) {
    const payload = new URLSearchParams({ id, action });
    fetch('../../actions/booking/approve.php', {
      method: 'POST', body: payload,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then(r => r.json())
      .then(data => {
        if (!data.success) { alert('Gagal: ' + (data.message || 'Unknown')); return; }
        const row = document.querySelector('tr[data-id="' + id + '"]');
        if (row) { row.dataset.status = action; setStatusBadge(row, action); }
        document.getElementById('d-status').textContent = statusLabel(action);
        if (modalDetail) modalDetail.hide();
      })
      .catch(() => alert('Terjadi kesalahan jaringan'));
  }

  const approveBtn = document.getElementById('btn-approve');
  if (approveBtn) approveBtn.addEventListener('click', function () { sendApproval(this.dataset.id, 'approved'); });
  const rejectBtn  = document.getElementById('btn-reject');
  if (rejectBtn)  rejectBtn.addEventListener('click',  function () { sendApproval(this.dataset.id, 'rejected'); });

  // ── Bind row buttons (also used for dynamically added rows) ────────────────
  function bindRowButtons(tr) {
    tr.querySelector('.btn-view')  ?.addEventListener('click', function () { openDetail(this.dataset.id); });
    tr.querySelector('.btn-edit')  ?.addEventListener('click', function () { openEdit(this.dataset.id); });
    tr.querySelector('.btn-delete')?.addEventListener('click', function () { openDelete(this.dataset.id); });
  }
  document.querySelectorAll('tr.data-row').forEach(bindRowButtons);

  // ── Photo upload (rooms page) ──────────────────────────────────────────────
  const photoUploadBtn = document.getElementById('photo-upload-btn');
  if (photoUploadBtn) {
    photoUploadBtn.addEventListener('click', function (e) {
      e.preventDefault();
      const roomId = this.dataset.roomId;
      if (!roomId) { alert('Buka detail ruangan terlebih dahulu.'); return; }
      const input = document.getElementById('photo-input');
      if (!input || !input.files.length) { alert('Pilih file foto terlebih dahulu'); return; }
      const fd = new FormData();
      fd.append('room_id', roomId);
      for (let i = 0; i < input.files.length; i++) fd.append('photos[]', input.files[i]);
      this.disabled = true; this.textContent = 'Uploading...';
      fetch('../../actions/room/upload_photo.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
          if (!data.success) { alert('Gagal upload: ' + (data.message || 'Unknown')); return; }
          alert('Upload sukses');
          window.location.reload();
        })
        .catch(() => alert('Terjadi kesalahan saat upload'))
        .finally(() => { photoUploadBtn.disabled = false; photoUploadBtn.textContent = 'Upload Foto'; });
    });
  }

  // ── Rooms page: Add ────────────────────────────────────────────────────────
  function handleRoomAdd(form) {
    fetch('../../actions/room/create.php', { method: 'POST', body: new FormData(form) })
      .then(r => r.json())
      .then(data => {
        if (!data.success) { alert('Gagal: ' + (data.message || 'Unknown')); return; }
        if (modalAdd) modalAdd.hide();
        window.location.reload();
      })
      .catch(() => alert('Terjadi kesalahan jaringan'));
  }

  // ── Rooms page: Edit ───────────────────────────────────────────────────────
  function handleRoomEdit(id) {
    const payload = new URLSearchParams({
      id,
      name:     document.getElementById('e-name')?.value     || '',
      building: document.getElementById('e-building')?.value || '',
      capacity: document.getElementById('e-capacity')?.value || '0',
      status:   document.getElementById('e-status')?.value   || 'Tidak Aktif',
    });
    fetch('../../actions/room/update.php', {
      method: 'POST', body: payload,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then(r => r.json())
      .then(data => {
        if (!data.success) { alert('Gagal: ' + (data.message || 'Unknown')); return; }
        const row  = document.querySelector('tr[data-id="' + id + '"]');
        const room = data.room || {};
        if (row) {
          row.dataset.name     = room.room_name    || '';
          row.dataset.building = room.building_name|| '';
          row.dataset.capacity = room.capacity     || '';
          row.dataset.active   = room.is_active;
          row.querySelector('td:nth-child(1) .fw-bold')?.textContent && (row.querySelector('td:nth-child(1) .fw-bold').textContent = room.room_name || '');
          const bBadge = row.querySelector('td:nth-child(2) .badge');
          if (bBadge) bBadge.textContent = room.building_name || 'Tanpa Gedung';
          const capCell = row.querySelector('td:nth-child(3)');
          if (capCell) capCell.textContent = (room.capacity || 0) + ' Orang';
          const sBadge = row.querySelector('td:nth-child(4) .badge');
          if (sBadge) {
            sBadge.classList.remove('success','danger','primary','warning');
            sBadge.classList.add(room.is_active == 1 ? 'success' : 'danger');
            sBadge.textContent = room.is_active == 1 ? 'Aktif' : 'Tidak Aktif';
          }
        }
        if (modalEdit) modalEdit.hide();
      })
      .catch(() => alert('Terjadi kesalahan jaringan'));
  }

  // ── Rooms page: Delete ─────────────────────────────────────────────────────
  function handleRoomDelete(id) {
    const payload = new URLSearchParams({ id });
    fetch('../../actions/room/delete.php', {
      method: 'POST', body: payload,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then(r => r.json())
      .then(data => {
        if (!data.success) { alert('Gagal: ' + (data.message || 'Unknown')); return; }
        document.querySelector('tr[data-id="' + id + '"]')?.remove();
        if (modalDelete) modalDelete.hide();
      })
      .catch(() => alert('Terjadi kesalahan jaringan'));
  }

  // ── Rooms page: open Add modal ─────────────────────────────────────────────
  if (!isApprovalsPage && btnAdd && modalAdd) {
    btnAdd.addEventListener('click', () => {
      document.getElementById('form-add')?.reset();
      modalAdd.show();
    });
  }
})();
