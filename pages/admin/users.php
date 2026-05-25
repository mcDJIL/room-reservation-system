<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$active = 'pengguna';
$crumbs = 'Manajemen | Pengguna';

include __DIR__ . '/../../config/connection.php';

// --- Search & Filter Logic ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role   = isset($_GET['role']) ? trim($_GET['role']) : '';

$where = [];
$params = [];

if ($search !== '') {
    $escaped_search = mysqli_real_escape_string($conn, $search);
    $where[] = "(name LIKE '%$escaped_search%' OR email LIKE '%$escaped_search%' OR id LIKE '%$escaped_search%')";
}

if ($role !== '' && in_array($role, ['Admin', 'Editor', 'Viewer'])) {
    $escaped_role = mysqli_real_escape_string($conn, $role);
    $where[] = "role = '$escaped_role'";
}

$where_clause = '';
if (count($where) > 0) {
    $where_clause = 'WHERE ' . implode(' AND ', $where);
}

$sql = "SELECT id, name, email, role FROM users $where_clause ORDER BY name ASC";
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
                                    <input class="input" type="search" name="search" placeholder="Search users by name, email, or ID..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                            <div class="data-toolbar-right">
                                <select class="select" name="role" style="width: auto; padding: 7px 28px 7px 10px; font-size: 12px;" onchange="this.form.submit()">
                                    <option value="">All roles</option>
                                    <option value="Admin" <?php echo $role === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="Editor" <?php echo $role === 'Editor' ? 'selected' : ''; ?>>Editor</option>
                                    <option value="Viewer" <?php echo $role === 'Viewer' ? 'selected' : ''; ?>>Viewer</option>
                                </select>
                            </div>
                            <!-- Keep the search input from auto-submitting immediately; user must press Enter -->
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
                                                <td><?php echo htmlspecialchars($row['role']); ?></td>
                                                <td>
                                                    <div class="data-cell-actions">
                                                        <button class="btn--icon btn-delete" aria-label="Delete" data-id="<?php echo $row['id']; ?>">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center;">Tidak ada data user.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="data-foot">
                            <div class="data-foot-info">
                                <span>Showing <strong style="color: var(--t-base);">1–<?php echo $total_rows; ?></strong> of <strong style="color: var(--t-base);"><?php echo $total_rows; ?></strong></span>
                                <!-- Per-page dropdown is not fully functional; you can add pagination later -->
                                <select class="select" disabled>
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

        <?php include __DIR__ . '/../../includes/script.php'; ?>
    </body>

</html>

<?php
mysqli_close($conn);
?>