<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    (function () {
        function run() {
            if (window.initCharts) return window.initCharts();
            let tries = 0;
            const id = setInterval(() => {
                if (window.initCharts) {
                    clearInterval(id);
                    return window.initCharts();
                }
                if (++tries > 80) clearInterval(id);
            }, 25);
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run); else run();

        if (window.SEEDS) { window.SEEDS['devices-doughnut'] = (t) => ({ type: 'doughnut', data: { labels: ['Auditorium', 'Mini Teater D3', 'Mini Teater Pascasarjana'], datasets: [{ data: [62, 30, 8], backgroundColor: [t.primary, t.purple, t.info], borderColor: t.bg, borderWidth: 3 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'right' } } } }); if (window.initCharts) window.initCharts(); }

        (function () {
                        function cssToken(name){ return getComputedStyle(document.documentElement).getPropertyValue(name).trim(); }

                        function buildDoughnut() {
                            const canvas = document.querySelector('canvas[data-chart-key="devices-doughnut"], #topRoomsChart');
                            if (!canvas) return;

                            const t = {
                                primary: cssToken('--primary') || '#6366F1',
                                purple: cssToken('--purple') || '#7C3AED',
                                info: cssToken('--info') || '#06B6D4',
                                bg: cssToken('--bg-card') || '#fff'
                            };

                            const cfg = {
                                type: 'doughnut',
                                data: {
                                    labels: ['Auditorium','Mini Teater D3','Mini Teater Pascasarjana'],
                                    datasets: [{ data: [62,30,8], backgroundColor: [t.primary, t.purple, t.info], borderColor: t.bg, borderWidth: 3 }]
                                },
                                options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'right' } } }
                            };

                            function create() {
                                try { if (canvas.__chartInstance) canvas.__chartInstance.destroy(); } catch (e) {}
                                canvas.__chartInstance = new (window.Chart)(canvas, cfg);
                            }

                            if (window.Chart) return create();

                            // wait for Chart to become available
                            let tries = 0; const id = setInterval(() => {
                                if (window.Chart) { clearInterval(id); create(); }
                                if (++tries > 80) { clearInterval(id); console.warn('Could not init doughnut chart: Chart not available'); }
                            }, 25);
                        }

                        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', buildDoughnut); else buildDoughnut();
                    })();
    })();
</script>