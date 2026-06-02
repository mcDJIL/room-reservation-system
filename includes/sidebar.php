<?php

$session_role = $_SESSION['role'] ?? '';
$name = $_SESSION['name'] ?? '';

if (!isset($active)) $active = '';

$navSections = [
  [
    'label' => 'Workspace',
    'items' => [
      ['key' => 'dashboard', 'text' => 'Dashboard', 'href' => 'dashboard.php', 'icon' => 'fa-solid fa-gauge-high'],
    ],
  ],
  [
    'label' => 'Manajemen',
    'items' => [
      ['key' => 'ruangan', 'text' => 'Ruangan', 'href' => 'rooms.php', 'icon' => 'fa-solid fa-house'],
      ['key' => 'peminjaman', 'text' => 'Peminjaman', 'href' => 'approvals.php', 'icon' => 'fa-solid fa-calendar'],
      ['key' => 'pengguna', 'text' => 'Pengguna', 'href' => 'users.php', 'icon' => 'fa-solid fa-user'],
    ],
  ],
  [
    'label' => 'Laporan',
    'items' => [
      ['key' => 'laporan', 'text' => 'Cetak Laporan', 'href' => 'report.php', 'icon' => 'fa-solid fa-file-lines'],
    ],
  ],
];
?>

<div id="page-loader" class="page-loader" aria-hidden="true">
  <div class="page-loader-card">
    <div class="page-loader-ring" aria-hidden="true"></div>
    <div class="page-loader-text">
      <span>Memuat halaman</span>
      <small>Menyiapkan dashboard dan konten</small>
    </div>
  </div>
</div>

<aside class="d-sidebar">
  <div class="brand">
    <div class="brand-logo">
      <img style="width: 20px;" src="../../assets/images/logo-white.png" alt="SatSet">
    </div>
    <div class="brand-text">
      <div class="brand-name">SatSet</div>
    </div>
  </div>

  <?php foreach ($navSections as $section): ?>
    <nav class="nav-section">
      <div class="nav-label"><?php echo htmlspecialchars($section['label'], ENT_QUOTES); ?></div>
      <?php foreach ($section['items'] as $item): ?>
        <?php $isActive = isset($item['key']) && $item['key'] === $active; ?>
        <a class="nav-link<?php echo $isActive ? ' is-active' : ''; ?>" href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES); ?>">
          <i class="<?php echo htmlspecialchars($item['icon'], ENT_QUOTES); ?>" aria-hidden="true"></i>
          <span><?php echo htmlspecialchars($item['text'], ENT_QUOTES); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  <?php endforeach; ?>

  <div class="sidebar-footer">
    <div class="workspace">
      <div class="workspace-avatar">AD</div>
      <div class="workspace-text">
        <div class="workspace-name"><?php echo htmlspecialchars($name, ENT_QUOTES); ?></div>
          <div class="workspace-role"><?php echo htmlspecialchars($session_role, ENT_QUOTES); ?></div>
      </div>
      <svg class="workspace-chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="m7 9 5-5 5 5"/><path d="m7 15 5 5 5-5"/>
      </svg>
    </div>
  </div>
</aside>
