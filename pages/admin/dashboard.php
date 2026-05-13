<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard · 2026 Redesign Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/dark-mode-dashboard.css">
    <script>
        (function () {
            function syncTheme() {
                var root = document.documentElement;
                var theme = root.getAttribute('data-theme');
                root.setAttribute('data-bs-theme', theme === 'dark' ? 'dark' : 'light');
            }
            try {
                var saved = localStorage.getItem('dash26-theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = saved || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
                syncTheme();
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
                syncTheme();
            }
            
            // Watch for theme changes
            window.addEventListener('storage', function(e) {
                if (e.key === 'dash26-theme') {
                    document.documentElement.setAttribute('data-theme', e.newValue || 'light');
                    syncTheme();
                }
            });
            
            // Expose sync function for theme toggle
            window.syncTheme = syncTheme;
        })();
    </script>
    <script defer="defer" src="../../assets/js/runtime.js"></script>
    <script defer="defer" src="../../assets/js/vendor-fullcalendar.js"></script>
    <script defer="defer" src="../../assets/js/vendor-chartjs.js"></script>
    <script defer="defer" src="../../assets/js/vendors.js"></script>
    <script defer="defer" src="../../assets/js/2026.js"></script>
    <script defer src="../../assets/js/charts.js"></script>
</head>
<?php
$active = 'dashboard';
$crumbs = 'Workspace | Dashboard';
?>

<body data-active="<?php echo htmlspecialchars($active); ?>" data-crumbs="<?php echo htmlspecialchars($crumbs); ?>">
    <div class="shell">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
        <div class="main">
            <?php include __DIR__ . '/../../includes/header.php'; ?>
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