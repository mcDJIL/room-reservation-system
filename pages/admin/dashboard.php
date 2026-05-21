<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
</head>
<?php
$active = 'dashboard';
$crumbs = 'Workspace | Dashboard';

session_start();
?>

<body data-active="<?php echo htmlspecialchars($active); ?>" data-crumbs="<?php echo htmlspecialchars($crumbs); ?>">
    <div class="shell">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        <div class="main">
            <?php include __DIR__ . '/../../includes/navbar.php'; ?>
            <main class="content">
                <?php include __DIR__ . '/../../includes/admin/metrics.php'; ?>

                <?php include __DIR__ . '/../../includes/admin/charts.php'; ?>

                <?php include __DIR__ . '/../../includes/admin/recent.php'; ?>

                <?php include __DIR__ . '/../../includes/admin/widgets.php'; ?>
            </main>
        <?php include __DIR__ . '/../../includes/footer.php'; ?>
    </div>

    <?php include __DIR__ . '/../../includes/script.php'; ?>
</body>

</html>