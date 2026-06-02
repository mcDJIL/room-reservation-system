<?php
// File ini tidak digunakan langsung oleh halaman admin/rooms.php.
// Filter ruangan sekarang diproses di actions/room/read.php saat pages/admin/rooms.php dimuat.

$filter = isset($_GET['status']) ? trim($_GET['status']) : '';
if ($filter === 'aktif' || $filter === 'tidak_aktif') {
    header('Location: ../../pages/admin/rooms.php?status=' . urlencode($filter));
    exit;
}

header('Location: ../../pages/admin/rooms.php');
exit;
?>