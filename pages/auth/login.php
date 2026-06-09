<?php

session_start();

if (!empty($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
  header("Location: ../../index.php");
}

$errors = $_SESSION['errors_login'] ?? [];
$old = $_SESSION['old_login'] ?? [];
unset($_SESSION['errors_login'], $_SESSION['old_login']);

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk · SatSet</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/auth/login.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="login-page">
    <div class="login-card">

      <!-- Brand Logo -->
      <div class="brand-logo">
        <img src="../../assets/images/logo.png" alt="SatSet" class="brand-icon">
        <span class="brand-name">SatSet</span>
      </div>

      <!-- Page Heading -->
      <div class="login-heading">
        <h1 class="login-title">Masuk</h1>
        <p class="login-subtitle">Akses portal pemesanan perusahaan Anda</p>
      </div>

      <!-- Login Form -->
      <?php if (!empty($errors['general'])): ?>
        <div class="error-message" style="margin-bottom:12px"><?php echo htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>
      <form class="login-form" method="POST" action="../../actions/auth/auth_action.php">
        <input type="hidden" name="action" value="login">

        <!-- Alamat Email -->
        <div class="form-field">
          <label class="field-label" for="email">Alamat Email</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M2 16C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V2C0 1.45 0.195833 0.979167 0.5875 0.5875C0.979167 0.195833 1.45 0 2 0H18C18.55 0 19.0208 0.195833 19.4125 0.5875C19.8042 0.979167 20 1.45 20 2V14C20 14.55 19.8042 15.0208 19.4125 15.4125C19.0208 15.8042 18.55 16 18 16H2ZM10 9L2 4V14H18V4L10 9ZM10 7L18 2H2L10 7ZM2 4V2V14V4Z"
                  fill="#757684" />
              </svg>
            </span>
            <input id="email" class="text-input" type="email" name="email" placeholder="nama@perusahaan.com"
              autocomplete="email" required value="<?php echo htmlspecialchars($old['email'] ?? '') ?>">
          </div>
          <?php if (!empty($errors['email'])): ?>
            <small class="error-message" id="error-email"><?php echo htmlspecialchars($errors['email']) ?></small>
          <?php endif; ?>
        </div>

        <!-- Kata Sandi -->
        <div class="form-field">
          <div class="field-header">
            <label class="field-label" for="password">Kata Sandi</label>
            <a href="#" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
              Lupa kata sandi?
            </a>
          </div>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M2 21C1.45 21 0.979167 20.8042 0.5875 20.4125C0.195833 20.0208 0 19.55 0 19V9C0 8.45 0.195833 7.97917 0.5875 7.5875C0.979167 7.19583 1.45 7 2 7H3V5C3 3.61667 3.4875 2.4375 4.4625 1.4625C5.4375 0.4875 6.61667 0 8 0C9.38333 0 10.5625 0.4875 11.5375 1.4625C12.5125 2.4375 13 3.61667 13 5V7H14C14.55 7 15.0208 7.19583 15.4125 7.5875C15.8042 7.97917 16 8.45 16 9V19C16 19.55 15.8042 20.0208 15.4125 20.4125C15.0208 20.8042 14.55 21 14 21H2ZM8 16C8.55 16 9.02083 15.8042 9.4125 15.4125C9.80417 15.0208 10 14.55 10 14C10 13.45 9.80417 12.9792 9.4125 12.5875C9.02083 12.1958 8.55 12 8 12C7.45 12 6.97917 12.1958 6.5875 12.5875C6.19583 12.9792 6 13.45 6 14C6 14.55 6.19583 15.0208 6.5875 15.4125C6.97917 15.8042 7.45 16 8 16ZM5 7H11V5C11 4.16667 10.7083 3.45833 10.125 2.875C9.54167 2.29167 8.83333 2 8 2C7.16667 2 6.45833 2.29167 5.875 2.875C5.29167 3.45833 5 4.16667 5 5V7Z"
                  fill="#757684" />
              </svg>
            </span>
            <input id="password" class="text-input password-input" type="password" name="password"
              placeholder="••••••••" autocomplete="current-password" required>
            <button type="button" class="password-toggle" id="togglePasswordBtn" aria-label="Tampilkan kata sandi">
              <svg class="eye-off-icon" width="22" height="20" viewBox="0 0 22 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M15.1 10.5L13.65 9.05C13.8 8.26667 13.575 7.53333 12.975 6.85C12.375 6.16667 11.6 5.9 10.65 6.05L9.2 4.6C9.48333 4.46667 9.77083 4.36667 10.0625 4.3C10.3542 4.23333 10.6667 4.2 11 4.2C12.25 4.2 13.3125 4.6375 14.1875 5.5125C15.0625 6.3875 15.5 7.45 15.5 8.7C15.5 9.03333 15.4667 9.34583 15.4 9.6375C15.3333 9.92917 15.2333 10.2167 15.1 10.5ZM18.3 13.65L16.85 12.25C17.4833 11.7667 18.0458 11.2375 18.5375 10.6625C19.0292 10.0875 19.45 9.43333 19.8 8.7C18.9667 7.01667 17.7708 5.67917 16.2125 4.6875C14.6542 3.69583 12.9167 3.2 11 3.2C10.5167 3.2 10.0417 3.23333 9.575 3.3C9.10833 3.36667 8.65 3.46667 8.2 3.6L6.65 2.05C7.33333 1.76667 8.03333 1.55417 8.75 1.4125C9.46667 1.27083 10.2167 1.2 11 1.2C13.5167 1.2 15.7583 1.89583 17.725 3.2875C19.6917 4.67917 21.1167 6.48333 22 8.7C21.6167 9.68333 21.1125 10.5958 20.4875 11.4375C19.8625 12.2792 19.1333 13.0167 18.3 13.65ZM18.8 19.8L14.6 15.65C14.0167 15.8333 13.4292 15.9708 12.8375 16.0625C12.2458 16.1542 11.6333 16.2 11 16.2C8.48333 16.2 6.24167 15.5042 4.275 14.1125C2.30833 12.7208 0.883333 10.9167 0 8.7C0.35 7.81667 0.791667 6.99583 1.325 6.2375C1.85833 5.47917 2.46667 4.8 3.15 4.2L0.4 1.4L1.8 0L20.2 18.4L18.8 19.8ZM4.55 5.6C4.06667 6.03333 3.625 6.50833 3.225 7.025C2.825 7.54167 2.48333 8.1 2.2 8.7C3.03333 10.3833 4.22917 11.7208 5.7875 12.7125C7.34583 13.7042 9.08333 14.2 11 14.2C11.3333 14.2 11.6583 14.1792 11.975 14.1375C12.2917 14.0958 12.6167 14.05 12.95 14L12.05 13.05C11.8667 13.1 11.6917 13.1375 11.525 13.1625C11.3583 13.1875 11.1833 13.2 11 13.2C9.75 13.2 8.6875 12.7625 7.8125 11.8875C6.9375 11.0125 6.5 9.95 6.5 8.7C6.5 8.51667 6.5125 8.34167 6.5375 8.175C6.5625 8.00833 6.6 7.83333 6.65 7.65L4.55 5.6Z"
                  fill="#757684" />
              </svg>
              <svg class="eye-on-icon" width="22" height="16" viewBox="0 0 22 16" fill="none"
                xmlns="http://www.w3.org/2000/svg" style="display:none">
                <path
                  d="M11 12.2C12.25 12.2 13.3125 11.7625 14.1875 10.8875C15.0625 10.0125 15.5 8.95 15.5 7.7C15.5 6.45 15.0625 5.3875 14.1875 4.5125C13.3125 3.6375 12.25 3.2 11 3.2C9.75 3.2 8.6875 3.6375 7.8125 4.5125C6.9375 5.3875 6.5 6.45 6.5 7.7C6.5 8.95 6.9375 10.0125 7.8125 10.8875C8.6875 11.7625 9.75 12.2 11 12.2ZM11 10.2C10.3167 10.2 9.73333 9.95833 9.25 9.475C8.76667 8.99167 8.525 8.40833 8.525 7.725C8.525 7.04167 8.76667 6.45833 9.25 5.975C9.73333 5.49167 10.3167 5.25 11 5.25C11.6833 5.25 12.2667 5.49167 12.75 5.975C13.2333 6.45833 11.6833 10.2 11 10.2ZM11 15.2C8.56667 15.2 6.35 14.5208 4.35 13.1625C2.35 11.8042 0.9 9.98333 0 7.7C0.9 5.41667 2.35 3.59583 4.35 2.2375C6.35 0.879167 8.56667 0.2 11 0.2C13.4333 0.2 15.65 0.879167 17.65 2.2375C19.65 3.59583 21.1 5.41667 22 7.7C21.1 9.98333 19.65 11.8042 17.65 13.1625C15.65 14.5208 13.4333 15.2 11 15.2Z"
                  fill="#757684" />
              </svg>
            </button>
          </div>
          <?php if (!empty($errors['password'])): ?>
            <small class="error-message" id="error-password"><?php echo htmlspecialchars($errors['password']) ?></small>
          <?php endif; ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn">
          Masuk
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.13125 6.75H0V5.25H9.13125L4.93125 1.05L6 0L12 6L6 12L4.93125 10.95L9.13125 6.75Z"
              fill="white" />
          </svg>
        </button>

      </form>

      <!-- Footer Link -->
      <div class="login-footer">
        <span class="footer-text">Belum punya akun?</span>
        <a href="register.php" class="register-link">Daftar Sekarang</a>
      </div>

    </div>
  </div>

  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Reset Password</h5>

          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="../../actions/auth/auth_action.php" method="POST">

          <input type="hidden" name="action" value="forgot_password">

          <div class="modal-body">

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Password Baru</label>
              <input type="password" class="form-control" name="password" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Konfirmasi Password</label>
              <input type="password" class="form-control" name="confirm_password" required>
            </div>

          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Batal
            </button>

            <button type="submit" class="btn btn-primary">
              Simpan Password
            </button>
          </div>

        </form>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php if (isset($_GET['status'])): ?>
    <script>
      let successTitle = "Berhasil!";
      let successText = "Proses berhasil dilakukan.";

      // Cek statusnya dari URL, apakah sukses register atau sukses logout
      if ("<?php echo $_GET['status']; ?>" === "success") {
        successTitle = "Registrasi Berhasil!";
        successText = "Akunmu sudah aktif, silakan login di sini.";
      } else if ("<?php echo $_GET['status']; ?>" === "logout") {
        successTitle = "Sampai Jumpa!";
        successText = "Anda telah berhasil keluar dari akun.";
      } else if ("<?php echo $_GET['status']; ?>" === "password_reset") {
          successTitle = "Password Berhasil Diubah!";
          successText = "Silakan login menggunakan password baru.";
      }

      Swal.fire({
        position: "top",
        icon: "success",
        title: successTitle,
        text: successText,
        width: "360px",
        showConfirmButton: false,
        timer: 2500
      });
    </script>
  <?php endif; ?>
  <?php if (isset($_GET['status']) && $_GET['status'] === 'email_not_found'): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Email Tidak Ditemukan',
        text: 'Pastikan email yang dimasukkan sudah terdaftar.'
      });
    </script>
  <?php endif; ?>
  <script>
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const eyeOff = toggleBtn.querySelector('.eye-off-icon');
    const eyeOn = toggleBtn.querySelector('.eye-on-icon');

    toggleBtn.addEventListener('click', function () {
      const isHidden = passwordInput.type === 'password';
      passwordInput.type = isHidden ? 'text' : 'password';
      eyeOff.style.display = isHidden ? 'none' : '';
      eyeOn.style.display = isHidden ? '' : 'none';
    });
  </script>
</body>

</html>