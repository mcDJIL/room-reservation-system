// Modal handlers for rooms page (moved from pages/admin/rooms.php)
(function(){
    // Bootstrap modal elements
    const modalAddEl = document.getElementById('modalAdd');
    const modalDetailEl = document.getElementById('modalDetail');
    const modalEditEl = document.getElementById('modalEdit');
    const modalDeleteEl = document.getElementById('modalDelete');

    const modalAdd = modalAddEl ? new bootstrap.Modal(modalAddEl) : null;
    const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
    const modalEdit = modalEditEl ? new bootstrap.Modal(modalEditEl) : null;
    const modalDelete = modalDeleteEl ? new bootstrap.Modal(modalDeleteEl) : null;

    // Add
    const btnAdd = document.getElementById('btn-add');
    if (btnAdd && modalAdd) btnAdd.addEventListener('click', () => {
        // reset form
        const form = document.getElementById('form-add');
        if (form) form.reset();
        modalAdd.show();
    });
    const formAdd = document.getElementById('form-add');
    if (formAdd) formAdd.addEventListener('submit', function(e){
        e.preventDefault();
        // TODO: send to server
        alert('Form tambah disubmit (implementasikan penyimpanan)');
        modalAdd.hide();
    });

    // View
    document.querySelectorAll('.btn-view').forEach(btn=>btn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="'+id+'"]');
        if (!row || !modalDetail) return;
        document.getElementById('d-name').textContent = row.dataset.name || '';
        document.getElementById('d-building').textContent = row.dataset.building || '';
        document.getElementById('d-capacity').textContent = row.dataset.capacity || '';
        document.getElementById('d-status').textContent = row.dataset.status || '';
        modalDetail.show();
    }));

    // Edit
    document.querySelectorAll('.btn-edit').forEach(btn=>btn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="'+id+'"]');
        if (!row || !modalEdit) return;
        document.getElementById('e-id').value = id;
        document.getElementById('e-name').value = row.dataset.name || '';
        document.getElementById('e-building').value = row.dataset.building || '';
        document.getElementById('e-capacity').value = row.dataset.capacity || '';
        document.getElementById('e-status').value = row.dataset.status || '';
        modalEdit.show();
    }));
    const formEdit = document.getElementById('form-edit');
    if (formEdit) formEdit.addEventListener('submit', function(e){
        e.preventDefault();
        // TODO: send update to server
        alert('Perubahan disimpan (implementasikan penyimpanan)');
        if (modalEdit) modalEdit.hide();
    });

    // Delete
    document.querySelectorAll('.btn-delete').forEach(btn=>btn.addEventListener('click', function(){
        const id = this.dataset.id;
        const row = document.querySelector('tr[data-id="'+id+'"]');
        if (!row || !modalDelete) return;
        document.getElementById('del-name').textContent = row.dataset.name || '';
        // store id on confirm button
        const delConfirm = document.getElementById('del-confirm');
        if (delConfirm) delConfirm.dataset.id = id;
        modalDelete.show();
    }));
    const delConfirmBtn = document.getElementById('del-confirm');
    if (delConfirmBtn) delConfirmBtn.addEventListener('click', function(){
        const id = this.dataset.id;
        // TODO: send delete request to server
        alert('Item dihapus (implementasikan penghapusan)');
        if (modalDelete) modalDelete.hide();
    });
})();
