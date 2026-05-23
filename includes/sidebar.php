<?php

$role = $_SESSION['role'];
$name = $_SESSION['name'];

if (!isset($active)) $active = '';
$NAV = [
  [
    'label' => 'Workspace',
    'items' => [
      ['key' => 'dashboard', 'text' => 'Dashboard', 'href' => 'dashboard.php', 'icon' => 'fa-solid fa-gauge-high' ],
    ],
  ],
  [
    'label' => 'Manajemen',
    'items' => [
      ['key' => 'ruangan', 'text' => 'Ruangan', 'href' => 'rooms.php', 'icon' => 'fa-solid fa-house' ],
      ['key' => 'peminjaman', 'text' => 'Peminjaman', 'href' => 'approvals.php', 'icon' => 'fa-solid fa-house-circle-check' ],
      ['key' => 'pengguna', 'text' => 'Pengguna', 'href' => 'users.php', 'icon' => 'fa-solid fa-user' ],
    ],
  ],
  [
    'label' => 'Laporan',
    'items' => [
      ['key' => 'laporan', 'text' => 'Cetak Laporan', 'href' => 'report.php', 'icon'=>'fa-solid fa-file-lines' ],
    ],
  ],
];

$BRAND_LOGO = '<img style="width: 20px;" src="../../assets/images/logo-white.png"></img>';

function esc($s) { return htmlspecialchars($s, ENT_QUOTES); }

function renderNavLink($item, $active) {
  $activeClass = (isset($item['key']) && $item['key'] === $active) ? ' is-active' : '';
  $badge = '';
  if (isset($item['badge'])) {
    $badge = '<span class="nav-badge '.esc($item['badge']['kind']).'">'.esc($item['badge']['text']).'</span>';
  }
  $icon = isset($item['icon']) ? $item['icon'] : '';
  $href = isset($item['href']) ? $item['href'] : '#';
  $text = isset($item['text']) ? $item['text'] : '';
  return "<a class=\"nav-link".$activeClass."\" href=\"".esc($href)."\">\n      <i class=\"".esc($icon)."\" aria-hidden=\"true\"></i>\n      <span>".esc($text)."</span>\n      ".$badge."\n    </a>";
}

function renderNavGroup($item, $active) {
  $isOpen = false;
  if (isset($item['children'])) {
    foreach ($item['children'] as $c) {
      if (isset($c['key']) && $c['key'] === $active) { $isOpen = true; break; }
    }
  }
  $openClass = $isOpen ? ' is-open' : '';
  $submenu = '';
  if (isset($item['children'])) {
    foreach ($item['children'] as $c) {
      $submenu .= '<a href="'.esc($c['href']).'">'.esc($c['text']).'</a>';
    }
  }
  $icon = isset($item['icon']) ? $item['icon'] : '';
  $text = isset($item['text']) ? $item['text'] : '';
  return "<div class=\"nav-item-group".$openClass."\" data-nav-group>\n      <a class=\"nav-link\" href=\"javascript:void(0)\" data-nav-toggle>\n        <i class=\"".esc($icon)."\" aria-hidden=\"true\"></i>\n        <span>".esc($text)."</span>\n        <i class=\"fa-solid fa-chevron-right chev\" aria-hidden=\"true\"></i>\n      </a>\n      <div class=\"nav-submenu\">".$submenu."</div>\n    </div>";
}

function renderSection($section, $active) {
  $itemsHtml = '';
  foreach ($section['items'] as $item) {
    if (isset($item['children'])) $itemsHtml .= renderNavGroup($item, $active);
    else $itemsHtml .= renderNavLink($item, $active);
  }
  return "<nav class=\"nav-section\">\n      <div class=\"nav-label\">".esc($section['label'])."</div>\n      ".$itemsHtml."\n    </nav>";
}

function renderSidebar($NAV, $BRAND_LOGO, $active, $name, $role) {
  $sections = '';
  foreach ($NAV as $s) $sections .= renderSection($s, $active);
  $html = "<div id=\"page-loader\" class=\"page-loader\" aria-hidden=\"true\">\n      <div class=\"page-loader-card\">\n        <div class=\"page-loader-ring\" aria-hidden=\"true\"></div>\n        <div class=\"page-loader-text\">\n          <span>Memuat halaman</span>\n          <small>Menyiapkan dashboard dan konten</small>\n        </div>\n      </div>\n    </div>\n    <aside class=\"d-sidebar\">\n      <div class=\"brand\">\n        <div class=\"brand-logo\">".$BRAND_LOGO."</div>\n        <div class=\"brand-text\">\n          <div class=\"brand-name\">SatSet</div>\n                  </div>\n      </div>\n      ".$sections."\n      <div class=\"sidebar-footer\">\n        <div class=\"workspace\">\n          <div class=\"workspace-avatar\">AD</div>\n          <div class=\"workspace-text\">\n            <div class=\"workspace-name\">$name</div>\n            <div class=\"workspace-role\">$role</div>\n          </div>\n          <svg class=\"workspace-chev\" width=\"14\" height=\"14\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\">\n            <path d=\"m7 9 5-5 5 5\"/><path d=\"m7 15 5 5 5-5\"/>\n          </svg>\n        </div>\n      </div>\n    </aside>";
  echo $html;
}

renderSidebar($NAV, $BRAND_LOGO, $active, $name, $role);
