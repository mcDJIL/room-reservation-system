<?php

session_start();

if (!empty($_SESSION['is_login']) && $_SESSION['is_login'] === true) {
  header("Location: ../../index.php");
}

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
        <form class="login-form" method="POST" action="../../actions/auth/auth_action.php">
          <input type="hidden" name="action" value="login">

          <!-- Alamat Email -->
          <div class="form-field">
            <label class="field-label" for="email">Alamat Email</label>
            <div class="input-wrapper">
              <input id="email" class="text-input" type="email" name="email"
                placeholder="nama@perusahaan.com" autocomplete="email" required>
            </div>
          </div>

          <!-- Kata Sandi -->
          <div class="form-field">
            <div class="field-header">
              <label class="field-label" for="password">Kata Sandi</label>
              <a href="#" class="forgot-link">Lupa kata sandi?</a>
            </div>
            <div class="input-wrapper">
              <input id="password" class="text-input password-input" type="password" name="password"
                placeholder="••••••••" autocomplete="current-password" required>
              <button type="button" class="password-toggle" id="togglePasswordBtn" aria-label="Tampilkan kata sandi">
                <svg class="eye-off-icon" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15.1 10.5L13.65 9.05C13.8 8.26667 13.575 7.53333 12.975 6.85C12.375 6.16667 11.6 5.9 10.65 6.05L9.2 4.6C9.48333 4.46667 9.77083 4.36667 10.0625 4.3C10.3542 4.23333 10.6667 4.2 11 4.2C12.25 4.2 13.3125 4.6375 14.1875 5.5125C15.0625 6.3875 15.5 7.45 15.5 8.7C15.5 9.03333 15.4667 9.34583 15.4 9.6375C15.3333 9.92917 15.2333 10.2167 15.1 10.5ZM18.3 13.65L16.85 12.25C17.4833 11.7667 18.0458 11.2375 18.5375 10.6625C19.0292 10.0875 19.45 9.43333 19.8 8.7C18.9667 7.01667 17.7708 5.67917 16.2125 4.6875C14.6542 3.69583 12.9167 3.2 11 3.2C10.5167 3.2 10.0417 3.23333 9.575 3.3C9.10833 3.36667 8.65 3.46667 8.2 3.6L6.65 2.05C7.33333 1.76667 8.03333 1.55417 8.75 1.4125C9.46667 1.27083 10.2167 1.2 11 1.2C13.5167 1.2 15.7583 1.89583 17.725 3.2875C19.6917 4.67917 21.1167 6.48333 22 8.7C21.6167 9.68333 21.1125 10.5958 20.4875 11.4375C19.8625 12.2792 19.1333 13.0167 18.3 13.65ZM18.8 19.8L14.6 15.65C14.0167 15.8333 13.4292 15.9708 12.8375 16.0625C12.2458 16.1542 11.6333 16.2 11 16.2C8.48333 16.2 6.24167 15.5042 4.275 14.1125C2.30833 12.7208 0.883333 10.9167 0 8.7C0.35 7.81667 0.791667 6.99583 1.325 6.2375C1.85833 5.47917 2.46667 4.8 3.15 4.2L0.4 1.4L1.8 0L20.2 18.4L18.8 19.8ZM4.55 5.6C4.06667 6.03333 3.625 6.50833 3.225 7.025C2.825 7.54167 2.48333 8.1 2.2 8.7C3.03333 10.3833 4.22917 11.7208 5.7875 12.7125C7.34583 13.7042 9.08333 14.2 11 14.2C11.3333 14.2 11.6583 14.1792 11.975 14.1375C12.2917 14.0958 12.6167 14.05 12.95 14L12.05 13.05C11.8667 13.1 11.6917 13.1375 11.525 13.1625C11.3583 13.1875 11.1833 13.2 11 13.2C9.75 13.2 8.6875 12.7625 7.8125 11.8875C6.9375 11.0125 6.5 9.95 6.5 8.7C6.5 8.51667 6.5125 8.34167 6.5375 8.175C6.5625 8.00833 6.6 7.83333 6.65 7.65L4.55 5.6Z" fill="#757684"/>
                </svg>
                <svg class="eye-on-icon" width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none">
                  <path d="M11 12.2C12.25 12.2 13.3125 11.7625 14.1875 10.8875C15.0625 10.0125 15.5 8.95 15.5 7.7C15.5 6.45 15.0625 5.3875 14.1875 4.5125C13.3125 3.6375 12.25 3.2 11 3.2C9.75 3.2 8.6875 3.6375 7.8125 4.5125C6.9375 5.3875 6.5 6.45 6.5 7.7C6.5 8.95 6.9375 10.0125 7.8125 10.8875C8.6875 11.7625 9.75 12.2 11 12.2ZM11 10.2C10.3167 10.2 9.73333 9.95833 9.25 9.475C8.76667 8.99167 8.525 8.40833 8.525 7.725C8.525 7.04167 8.76667 6.45833 9.25 5.975C9.73333 5.49167 10.3167 5.25 11 5.25C11.6833 5.25 12.2667 5.49167 12.75 5.975C13.2333 6.45833 11.6833 10.2 11 10.2ZM11 15.2C8.56667 15.2 6.35 14.5208 4.35 13.1625C2.35 11.8042 0.9 9.98333 0 7.7C0.9 5.41667 2.35 3.59583 4.35 2.2375C6.35 0.879167 8.56667 0.2 11 0.2C13.4333 0.2 15.65 0.879167 17.65 2.2375C19.65 3.59583 21.1 5.41667 22 7.7C21.1 9.98333 19.65 11.8042 17.65 13.1625C15.65 14.5208 13.4333 15.2 11 15.2Z" fill="#757684"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="submit-btn">
            Masuk
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9.13125 6.75H0V5.25H9.13125L4.93125 1.05L6 0L12 6L6 12L4.93125 10.95L9.13125 6.75Z" fill="white"/>
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
    }

    Swal.fire({
        position: "top", 
        icon: "success",
        title: successTitle,
        text: successText,
        width: "360px", // Biar ukurannya kecil minimalis sama kayak eror merah kemarin
        showConfirmButton: false,
        timer: 2500 
    });
</script>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
<script>
    let msgTitle = "Oops...";
    let msgText = "Terjadi kesalahan.";

    if ("<?php echo $_GET['error']; ?>" === "wrong_password") {
        msgTitle = "Password Salah!";
        msgText = "Kata sandi yang Anda masukkan tidak sesuai.";
    } else if ("<?php echo $_GET['error']; ?>" === "email_not_found") {
        msgTitle = "Email Tidak Terdaftar!";
        msgText = "Email kerja yang Anda masukkan belum terdaftar.";
    }

    Swal.fire({
        position: "top",
        icon: "error",
        title: msgTitle,
        text: msgText,
        width: "360px", // <-- Tambahkan baris ini untuk mengecilkan (bisa diganti misal 380px atau 400px sesuai selera)
        showConfirmButton: true,
        confirmButtonColor: "#d33"
    
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