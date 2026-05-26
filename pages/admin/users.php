<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$active = 'pengguna';
$crumbs = 'Manajemen | Pengguna';

include __DIR__ . '/../../config/connection.php';

if (isset($_POST['deactivate_user']) && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    if ($user_id != ($_SESSION['user_id'] ?? 0)) {
        $update_sql = "UPDATE users SET is_active = 0 WHERE id = $user_id";
        mysqli_query($conn, $update_sql);
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . urlencode($_GET['status'] ?? 'active'));
    exit;
}

if (isset($_POST['activate_user']) && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $update_sql = "UPDATE users SET is_active = 1 WHERE id = $user_id";
    mysqli_query($conn, $update_sql);
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . urlencode($_GET['status'] ?? 'active'));
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role   = isset($_GET['role']) ? trim($_GET['role']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'active'; // active / inactive

$where = [];
if ($status === 'active') {
    $where[] = "is_active = 1";
} else {
    $where[] = "is_active = 0";
}

if ($search !== '') {
    $escaped_search = mysqli_real_escape_string($conn, $search);
    $where[] = "(name LIKE '%$escaped_search%' OR email LIKE '%$escaped_search%' OR id LIKE '%$escaped_search%')";
}

if ($role !== '' && in_array($role, ['admin', 'user'])) {
    $escaped_role = mysqli_real_escape_string($conn, $role);
    $where[] = "role = '$escaped_role'";
}

$where_clause = 'WHERE ' . implode(' AND ', $where);

$sql = "SELECT id, name, email, role, is_active FROM users $where_clause ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

$total_rows = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    <style>
        .badge-active { background: #2ecc71; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; }
        .badge-inactive { background: #e74c3c; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; }
        .btn-activate { background: #3498db; color: white; border: none; }
    </style>
</head>

<body data-active="<?php echo htmlspecialchars($active); ?>" data-crumbs="<?php echo htmlspecialchars($crumbs); ?>">
    <div class="shell">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        <div class="main">
            <?php include __DIR__ . '/../../includes/navbar.php'; ?>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <h1 class="hero-title">Pengguna</h1>
                    </div>
                </section>

                <div class="grid">
                    <section class="col-12 card">
                        <!-- Search & Filter Form -->
                        <form method="GET" action="" class="data-toolbar">
                            <div class="data-toolbar-left">
                                <div class="input-icon" style="flex: 1; max-width: 320px;">
                                    <span class="ico">
                                        <svg viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="7" />
                                            <path d="m21 21-4.3-4.3" />
                                        </svg>
                                    </span>
                                    <input class="input" type="search" name="search" placeholder="Cari nama, email, atau ID..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                            <div class="data-toolbar-right">
                                <select class="select" name="role" style="width: auto; padding: 7px 28px 7px 10px; font-size: 12px;" onchange="this.form.submit()">
                                    <option value="">Semua role</option>
                                    <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>User</option>
                                </select>
                                <select class="select" name="status" style="width: auto; padding: 7px 28px 7px 10px; font-size: 12px; margin-left: 10px;" onchange="this.form.submit()">
                                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Nonaktif</option>
                                </select>
                            </div>
                        </form>

                        <div>
                            <table class="data-table" style="margin: 0 22px; min-width: 900px;">
                                <thead>
                                    <tr>
                                        <th class="sorted-asc">Nama <span class="sort"><svg viewBox="0 0 24 24">
                                                    <path d="m6 9 6 6 6-6" />
                                                </svg></span></th>
                                        <th>Email <span class="sort"><svg viewBox="0 0 24 24">
                                                    <path d="m6 9 6 6 6-6" />
                                                </svg></span></th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($total_rows > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr class="data-row">
                                                <td>
                                                    <div class="data-cell-user">
                                                        <div class="data-cell-user-name">
                                                            <?php echo htmlspecialchars($row['name']); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo ucfirst(htmlspecialchars($row['role'])); ?></td>
                                                <td>
                                                    <?php if ($row['is_active']): ?>
                                                        <span class="badge-active">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge-inactive">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="data-cell-actions">
                                                        <?php if ($row['is_active']): ?>
                                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Nonaktifkan user ini?')">
                                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                                <button type="submit" name="deactivate_user" class="btn--icon btn-deactivate" aria-label="Nonaktifkan">
                                                                    <i class="fa-regular fa-trash-can"></i> Nonaktifkan
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Aktifkan user ini kembali?')">
                                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                                <button type="submit" name="activate_user" class="btn--icon btn-activate" aria-label="Aktifkan">
                                                                    <i class="fa-regular fa-check-circle"></i> Aktifkan
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center;">
                                                Tidak ada data user <?php echo $status === 'active' ? 'aktif' : 'nonaktif'; ?>.
                                                <?php echo ($search || $role) ? ' Coba ubah filter pencarian.' : ''; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="data-foot">
                            <div class="data-foot-info">
                                <span>Menampilkan <strong><?php echo $total_rows; ?></strong> user <?php echo $status === 'active' ? 'aktif' : 'nonaktif'; ?></span>
                            </div>
                            <!-- Pagination sederhana (masih statis) -->
                            <div class="pager">
                                <button class="pager-btn" disabled="disabled" aria-label="Previous">
                                    <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6" /></svg>
                                </button>
                                <button class="pager-btn is-active">1</button>
                                <button class="pager-btn">2</button>
                                <button class="pager-btn">3</button>
                                <button class="pager-btn">…</button>
                                <button class="pager-btn">10</button>
                                <button class="pager-btn" aria-label="Next">
                                    <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6" /></svg>
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>

        <?php include __DIR__ . '/../../includes/script.php'; ?>
    </body>

</html>

<?php
mysqli_close($conn);
?>