<?php

if (!isset($crumbs)) $crumbs = '';
// render crumbs
$crumbsHtml = '';
if ($crumbs) {
  $parts = array_filter(array_map('trim', explode('|', $crumbs)));
  $sep = '<svg class="sep" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>';
  foreach ($parts as $i => $p) {
    $cls = ($i === count($parts) - 1) ? ' class="current"' : '';
    $crumbsHtml .= ($i > 0 ? $sep : '') . '<span' . $cls . '>' . htmlspecialchars($p, ENT_QUOTES) . '</span>';
  }
}
?>

<header class="d-topbar">
  <div class="crumbs">
    <button class="hamburger" data-drawer-open aria-label="Open navigation">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <?php echo $crumbsHtml; ?>
  </div>
  <div class="topbar-actions">
    <button class="cmd" data-palette-open>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      <span>Search...</span>
      <kbd class="kbd">⌘K</kbd>
    </button>

    <div class="dd-wrap">
      <button class="icon-btn" data-dropdown aria-label="Notifications">
        <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <span class="count danger">3</span>
      </button>
      <div class="dd-menu" role="menu">
        <div class="dd-head">
          <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          Notifications
        </div>
        <div class="dd-list">
          <a class="dd-item" href="#">
            <div class="dd-avatar a1">JD</div>
            <div class="dd-body">
              <div class="dd-text"><strong>John Doe</strong> liked your <em>post</em></div>
              <div class="dd-time">5 MIN AGO</div>
            </div>
          </a>
          <a class="dd-item" href="#">
            <div class="dd-avatar a2">MD</div>
            <div class="dd-body">
              <div class="dd-text"><strong>Moo Doe</strong> liked your <em>cover image</em></div>
              <div class="dd-time">7 MIN AGO</div>
            </div>
          </a>
          <a class="dd-item" href="#">
            <div class="dd-avatar a3">LD</div>
            <div class="dd-body">
              <div class="dd-text"><strong>Lee Doe</strong> commented on your <em>video</em></div>
              <div class="dd-time">10 MIN AGO</div>
            </div>
          </a>
        </div>
        <a class="dd-footer" href="#">View all notifications →</a>
      </div>
    </div>

    <button class="icon-btn" id="themeToggle" aria-label="Toggle theme"></button>

    <div class="dd-wrap">
      <div class="avatar" data-dropdown tabindex="0" role="button" aria-label="Account menu">AD</div>
      <div class="dd-menu dd-profile" role="menu">
        <div class="dd-profile-head">
          <div class="dd-profile-name"><?php echo htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES); ?></div>
          <div class="dd-profile-email"><?php echo htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES); ?></div>
        </div>
        <a class="dd-menu-item" href="../../index.php">
          <i class="fa-regular fa-home"></i>
          Beranda
        </a>
        <a class="dd-menu-item danger" href="../../actions/auth/logout.php">
          <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
          Logout
        </a>
      </div>
    </div>
  </div>
</header>
