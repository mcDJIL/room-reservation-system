<?php
$isDashboard = isset($active) && $active === 'dashboard';
$chartMonths = $monthsMaster ?? [];
$chartTrend = $trendDataFilled ?? [];
$chartRoomLabels = $roomLabel ?? [];
$chartRoomData = $roomData ?? [];
?>

<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script defer src="../../assets/js/admin/script.js"></script>
<?php if ($isDashboard): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php endif; ?>

<script>
    (function () {
        function hidePageLoader() {
            var loader = document.getElementById('page-loader');
            if (!loader) return;

            loader.classList.add('is-hidden');
            window.setTimeout(function () {
                if (loader && loader.parentNode) loader.parentNode.removeChild(loader);
            }, 260);
        }

        function runDashboardCharts() {
            var body = document.body;
            if (!body || body.getAttribute('data-active') !== 'dashboard') return;
            if (!window.Chart) return;

            var trendCanvas = document.getElementById('trendChart');
            var roomsCanvas = document.getElementById('topRoomsChart');

            if (trendCanvas) {
                new Chart(trendCanvas, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($chartMonths) ?>,
                        datasets: [{
                            label: 'Jumlah Peminjaman',
                            data: <?= json_encode($chartTrend) ?>,
                            borderWidth: 2,
                            tension: 0.4,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            if (roomsCanvas) {
                new Chart(roomsCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode($chartRoomLabels) ?>,
                        datasets: [{
                            data: <?= json_encode($chartRoomData) ?>,
                            borderWidth: 1,
                            backgroundColor: ['#3b82f6', '#f43f5e', '#f59e0b']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', hidePageLoader, { once: true });
        } else {
            hidePageLoader();
        }

        if (document.readyState === 'complete') {
            runDashboardCharts();
        } else {
            window.addEventListener('load', runDashboardCharts, { once: true });
        }
    })();
</script>