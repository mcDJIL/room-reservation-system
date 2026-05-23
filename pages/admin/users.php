<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
</head>
<?php
$active = 'pengguna';
$crumbs = 'Manajemen | Pengguna';

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
?>

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
                                    <option>All roles</option>
                                    <option>Admin</option>
                                    <option>Editor</option>
                                    <option>Viewer</option>
                                </select>
                            </div>
                        </div>

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
                                    <tr class="data-row is-selected">
                                        <td>
                                            <div class="data-cell-user">
                                                <div class="data-cell-user-name">Sara Kim</div>
                                            </div>
                                        </td>
                                        <td>sara@gmail.com</td>
                                        <td><span class="badge primary">Admin</span></td>
                                        <td>
                                            <div class="data-cell-actions">
                                                <button class="btn--icon btn-delete" aria-label="Delete" data-id="1">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
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

        <?php include __DIR__ . '/../../includes/script.php'; ?>
</body>

</html>