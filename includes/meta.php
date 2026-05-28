<?php
// Shared SEO meta include. Can be used on any page by setting $meta_* vars before including.
$site_name = isset($site_name) ? $site_name : 'SatSet';
$site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$meta_title = isset($meta_title) ? $meta_title : $site_name;
$meta_description = isset($meta_description) ? $meta_description : 'SatSet – Pinjam Ruangan Jadi Lebih Mudah & Praktis.';
$meta_keywords = isset($meta_keywords) ? $meta_keywords : 'ruangan,pinjam ruangan,reservasi,ruang meeting,satset';
$meta_image = isset($meta_image) ? $meta_image : ($site_url . '/assets/images/logo.png');
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
