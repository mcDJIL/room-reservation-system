<?php
  include '../../config/middleware.php';

  // --- SEO defaults (can be overridden per-page by defining these vars before including header) ---
  $site_name = 'SatSet';
  $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
  $meta_title = isset($meta_title) ? $meta_title : ('Dashboard · ' . $site_name);
  $meta_description = isset($meta_description) ? $meta_description : 'SatSet – Pinjam Ruangan Jadi Lebih Mudah & Praktis.';
  $meta_keywords = isset($meta_keywords) ? $meta_keywords : 'ruangan,pinjam ruangan,reservasi,ruang meeting,satset';
  $meta_image = isset($meta_image) ? $meta_image : $site_url . '/assets/images/logo.png';
  $meta_image_alt = isset($meta_image_alt) ? $meta_image_alt : $site_name . ' logo';
  $meta_url = isset($meta_url) ? $meta_url : $site_url . $_SERVER['REQUEST_URI'];
  $meta_canonical = isset($meta_canonical) ? $meta_canonical : $meta_url;
  $meta_type = isset($meta_type) ? $meta_type : 'website';
  $meta_robots = isset($meta_robots) ? $meta_robots : 'index, follow';
?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($meta_title, ENT_QUOTES); ?></title>

<!-- Primary SEO -->
<meta name="description" content="<?php echo htmlspecialchars($meta_description, ENT_QUOTES); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords, ENT_QUOTES); ?>">
<meta name="robots" content="<?php echo htmlspecialchars($meta_robots, ENT_QUOTES); ?>">
<link rel="canonical" href="<?php echo htmlspecialchars($meta_canonical, ENT_QUOTES); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:locale" content="id_ID">
<meta property="og:type" content="<?php echo htmlspecialchars($meta_type, ENT_QUOTES); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($meta_title, ENT_QUOTES); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($meta_description, ENT_QUOTES); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($meta_url, ENT_QUOTES); ?>">
<meta property="og:site_name" content="<?php echo htmlspecialchars($site_name, ENT_QUOTES); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($meta_image, ENT_QUOTES); ?>">
<meta property="og:image:alt" content="<?php echo htmlspecialchars($meta_image_alt, ENT_QUOTES); ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($meta_title, ENT_QUOTES); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($meta_description, ENT_QUOTES); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($meta_image, ENT_QUOTES); ?>">

<!-- JSON-LD structured data -->
<script type="application/ld+json">
<?php echo json_encode([
  "@context" => "https://schema.org",
  "@graph" => [
    [
      "@type" => "Organization",
      "name" => $site_name,
      "url" => $site_url,
      "logo" => $meta_image
    ],
    [
      "@type" => "WebSite",
      "url" => $site_url,
      "name" => $site_name,
      "description" => $meta_description,
      "potentialAction" => [
        "@type" => "SearchAction",
        "target" => $site_url . '/?s={search_term_string}',
        "query-input" => 'required name=search_term_string'
      ]
    ]
  ]
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
?>
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link rel="stylesheet" href="../../assets/css/admin/dashboard.css">
<link rel="stylesheet" href="../../assets/css/admin/rooms.css">
<link rel="stylesheet" href="../../assets/css/dark-mode-dashboard.css">
<link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
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
    window.addEventListener('storage', function (e) {
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
