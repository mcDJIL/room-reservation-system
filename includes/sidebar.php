<?php
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

$BRAND_LOGO = '<svg viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">\n  <path fill="#ffffff" d="M14.747 9.125c.527-1.426 1.736-2.573 3.317-2.573c1.643 0 2.792 1.085 3.318 2.573l6.077 16.867c.186.496.248.931.248 1.147c0 1.209-.992 2.046-2.139 2.046c-1.303 0-1.954-.682-2.264-1.611l-.931-2.915h-8.62l-.93 2.884c-.31.961-.961 1.642-2.232 1.642c-1.24 0-2.294-.93-2.294-2.17c0-.496.155-.868.217-1.023l6.233-16.867zm.34 11.256h5.891l-2.883-8.992h-.062l-2.946 8.992z"/>\n</svg>';

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

function renderSidebar($NAV, $BRAND_LOGO, $active) {
  $sections = '';
  foreach ($NAV as $s) $sections .= renderSection($s, $active);
  $html = "<aside class=\"d-sidebar\">\n      <div class=\"brand\">\n        <div class=\"brand-logo\">".$BRAND_LOGO."</div>\n        <div class=\"brand-text\">\n          <div class=\"brand-name\">SatSet</div>\n                  </div>\n      </div>\n      ".$sections."\n      <div class=\"sidebar-footer\">\n        <div class=\"workspace\">\n          <div class=\"workspace-avatar\">JD</div>\n          <div class=\"workspace-text\">\n            <div class=\"workspace-name\">John Doe</div>\n            <div class=\"workspace-role\">admin</div>\n          </div>\n          <svg class=\"workspace-chev\" width=\"14\" height=\"14\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\">\n            <path d=\"m7 9 5-5 5 5\"/><path d=\"m7 15 5 5 5-5\"/>\n          </svg>\n        </div>\n      </div>\n    </aside>";
  echo $html;
}

renderSidebar($NAV, $BRAND_LOGO, $active);
