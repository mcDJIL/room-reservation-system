<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$active = 'pengguna';
$crumbs = 'Manajemen | Pengguna';

include __DIR__ . '/../../config/connection.php';

// --- Handle deactivation / activation ---
if (isset($_POST['deactivate_user']) && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    if ($user_id != ($_SESSION['user_id'] ?? 0)) {
        mysqli_query($conn, "UPDATE users SET is_active = 0 WHERE id = $user_id");
        $_SESSION['flash_toast'] = ['type' => 'success', 'message' => 'User berhasil dinonaktifkan'];
    } else {
        $_SESSION['flash_toast'] = ['type' => 'warning', 'message' => 'Akun sendiri tidak bisa dinonaktifkan'];
    }
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
if (isset($_POST['activate_user']) && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    mysqli_query($conn, "UPDATE users SET is_active = 1 WHERE id = $user_id");
    $_SESSION['flash_toast'] = ['type' => 'success', 'message' => 'User berhasil diaktifkan'];
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// --- Filter & pagination parameters ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$allowed_per_page = [15, 25, 50, 100];
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 15;
if (!in_array($per_page, $allowed_per_page, true)) {
    $per_page = 15;
}

// --- Build WHERE clause ---
$where = [];
if ($status === 'active') {
    $where[] = "is_active = 1";
} elseif ($status === 'inactive') {
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
$where_clause = (count($where) > 0) ? 'WHERE ' . implode(' AND ', $where) : '';

// --- Get total rows (for pagination) ---
$count_sql = "SELECT COUNT(*) as total FROM users $where_clause";
$count_res = mysqli_query($conn, $count_sql);
$total_rows = mysqli_fetch_assoc($count_res)['total'];
$total_pages = max(1, (int) ceil($total_rows / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;

// --- Fetch data for current page ---
$sql = "SELECT id, name, email, role, is_active 
        FROM users $where_clause 
        ORDER BY name ASC 
        LIMIT $offset, $per_page";
$result = mysqli_query($conn, $sql);
if (!$result)
    die("Query error: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    <style>
        /* Additional custom badges to match peminjaman style */
        .badge-active {
            background: #2ecc71;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .badge-inactive {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .btn-activate {
            background: #3498db;
            color: white;
            border: none;
        }

        .btn-deactivate {
            background: transparent;
            border: none;
            color: #e74c3c;
        }

        .pager-btn.disabled,
        .pager-btn[aria-disabled="true"] {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>

<body data-active="<?= htmlspecialchars($active) ?>" data-crumbs="<?= htmlspecialchars($crumbs) ?>">
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
                        <!-- Toolbar mirip peminjaman -->
                        <form method="get" id="filter-form">
                            <div class="data-toolbar">
                                <div class="data-toolbar-left">
                                    <div class="input-icon" style="flex:1; max-width:320px;">
                                        <span class="ico"><svg viewBox="0 0 24 24">
                                                <circle cx="11" cy="11" r="7" />
                                                <path d="m21 21-4.3-4.3" />
                                            </svg></span>
                                        <input class="input" type="search" name="search"
                                            value="<?= htmlspecialchars($search) ?>"
                                            placeholder="Cari nama, email, atau ID...">
                                    </div>
                                </div>
                                <div class="data-toolbar-right">
                                    <select class="select" name="role"
                                        style="width:auto; padding:7px 28px 7px 10px; font-size:12px;">
                                        <option value="" <?= $role === '' ? 'selected' : '' ?>>Semua role</option>
                                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
                                    </select>
                                    <select class="select" name="status"
                                        style="width:auto; padding:7px 28px 7px 10px; font-size:12px; margin-left:10px;">
                                        <option value="" <?= $status === '' ? 'selected' : '' ?>>Semua status</option>
                                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary"
                                        style="font-size:12px; padding:7px 14px;">Cari</button>
                                </div>
                            </div>
                            <input type="hidden" name="per_page" id="filter-per-page" value="<?= $per_page ?>">
                            </form>

                        <!-- Table -->
                        <div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($total_rows > 0 && mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr class="data-row">
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                                </td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                                <td><?= ucfirst(htmlspecialchars($row['role'])) ?></td>
                                                <td>
                                                    <?php if ($row['is_active']): ?>
                                                        <span class="badge-active">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge-inactive">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="data-cell-actions">
                                                    <?php if ($row['is_active']): ?>
                                                        <form method="POST" style="display:inline"
                                                            onsubmit="return confirm('Nonaktifkan user ini?')">
                                                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                            <button type="submit" name="deactivate_user"
                                                                class="btn--icon btn-deactivate" aria-label="Nonaktifkan">
                                                                <i class="fa-regular fa-trash-can"></i> Nonaktifkan
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" style="display:inline"
                                                            onsubmit="return confirm('Aktifkan user ini kembali?')">
                                                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                            <button type="submit" name="activate_user"
                                                                class="btn--icon btn-activate" aria-label="Aktifkan">
                                                                <i class="fa-regular fa-check-circle"></i> Aktifkan
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada data user
                                                    <?= $status === 'active' ? 'aktif' : ($status === 'inactive' ? 'nonaktif' : '') ?> <?= ($search || $role || $status) ? ' dengan filter yang dipilih.' : '' ?>
                                                </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (mirip peminjaman) -->
                        <div class="data-foot">
                            <div class="data-foot-info">
                                <?php
                                $from_n = $total_rows > 0 ? ($page - 1) * $per_page + 1 : 0;
                                $to_n = min($page * $per_page, $total_rows);
                                ?>
                                <span>Showing <strong style="color:var(--t-base)"><?= $from_n ?>–<?= $to_n ?></strong>
                                    of <strong style="color:var(--t-base)"><?= $total_rows ?></strong></span>
                                <select class="select" name="per_page"
                                    style="width:auto; padding:7px 28px 7px 10px; font-size:12px; margin-right:10px;"
                                    onchange="document.getElementById('filter-per-page').value=this.value; document.getElementById('filter-form').submit();">
                                    <option value="15" <?= $per_page === 15 ? 'selected' : '' ?>>15 per page</option>
                                    <option value="25" <?= $per_page === 25 ? 'selected' : '' ?>>25 per page</option>
                                    <option value="50" <?= $per_page === 50 ? 'selected' : '' ?>>50 per page</option>
                                    <option value="100" <?= $per_page === 100 ? 'selected' : '' ?>>100 per page</option>
                                </select>
                            </div>
                            <?php if ($total_pages > 1): ?>
                                <div class="pager">
                                    <?php
                                    // helper to build query string for pagination links
                                    $qs = function ($p) use ($search, $role, $status, $per_page) {
                                        $params = array_filter([
                                            'search' => $search,
                                            'role' => $role,
                                            'status' => $status,
                                            'per_page' => $per_page,
                                            'page' => $p
                                        ], fn($v) => $v !== '');
                                        return '?' . http_build_query($params);
                                    };
                                    ?>
                                    <a href="<?= $qs($page - 1) ?>" class="pager-btn <?= $page <= 1 ? 'disabled' : '' ?>"
                                        <?= $page <= 1 ? 'aria-disabled="true"' : '' ?>>
                                        <svg viewBox="0 0 24 24">
                                            <path d="m15 18-6-6 6-6" />
                                        </svg>
                                    </a>
                                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                        <a href="<?= $qs($p) ?>"
                                            class="pager-btn <?= $p === $page ? 'is-active' : '' ?>"><?= $p ?></a>
                                    <?php endfor; ?>
                                    <a href="<?= $qs($page + 1) ?>"
                                        class="pager-btn <?= $page >= $total_pages ? 'disabled' : '' ?>"
                                        <?= $page >= $total_pages ? 'aria-disabled="true"' : '' ?>>
                                        <svg viewBox="0 0 24 24">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
            </main>
            <?php include __DIR__ . '/../../includes/footer.php'; ?>
        </div>
        <?php include __DIR__ . '/../../includes/script.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>