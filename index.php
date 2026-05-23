<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SatSet – Pinjam Ruangan Jadi Lebih Mudah & Praktis</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/user/landing.css">
  <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
</head>

<body>

  <?php
  $current_page = basename($_SERVER['PHP_SELF']);
  ?>
  <nav class="landing-navbar navbar navbar-expand-lg">
    <div class="container-fluid px-0">
      <a class="navbar-brand" href="./index.php" style="color: #164d6d;">
        <img src="./assets/images/logo.png" alt="SatSet" height="40" style="margin-right:6px;"
          onerror="this.style.display='none'">
        SatSet
      </a>

      <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
        data-bs-target="#landingNav" aria-controls="landingNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="landingNav">
        <ul class="navbar-nav mx-auto gap-lg-3">
          <li class="nav-item">
            <a class="nav-link active" href="./index.php">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#alur-peminjaman">Alur Peminjaman</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#daftar-ruangan">Ruangan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./pages/user/booking.php">Reservasi</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./pages/user/history.php">Riwayat</a>
          </li>
        </ul>
        
        <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a class="nav-link me-3" href="./pages/admin/dashboard.php">Dashboard</a>
          <?php endif; ?>
          <?php if (isset($_SESSION['user_id'])): ?>
            <a href="actions/auth/logout.php" class="btn btn-danger">Logout</a>
          <?php else: ?>
            <a href="pages/auth/register.php" class="nav-register">Register</a>
            <div class="nav-divider d-none d-lg-block"></div>
            <a href="pages/auth/login.php" class="btn-login">Login</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    </div>
  </nav>

  <?php include 'config/connection.php'; ?>
  <?php include 'includes/user/hero.php'; ?>
  <?php include 'includes/user/alur.php'; ?>
  <?php include 'includes/user/rooms.php'; ?>
  <?php include 'includes/user/keunggulan.php'; ?>
  <?php include 'includes/user/footer_landing.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>