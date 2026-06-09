<?php
session_start();

require_once '../../config/connection.php';

// Sekarang variabel $conn sudah siap digunakan untuk query database!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- LOGIKA REGISTER ---
    if ($action === 'register') {
        // Ambil input mentah
        $nama_raw = trim($_POST['nama'] ?? '');
        $email_raw = trim($_POST['email'] ?? '');
        $password_raw = $_POST['password'] ?? '';
        $terms = isset($_POST['terms']);

        $errors = [];

        // Validasi server-side
        if ($nama_raw === '') {
            $errors['nama'] = 'Nama lengkap wajib diisi.';
        }

        if ($email_raw === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email_raw, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Masukkan alamat email yang valid.';
        } else {
            // Cek apakah email sudah terdaftar
            $check = mysqli_real_escape_string($conn, $email_raw);
            $q = "SELECT id FROM users WHERE email = '$check' LIMIT 1";
            $r = mysqli_query($conn, $q);
            if ($r && mysqli_num_rows($r) > 0) {
                $errors['email'] = 'Email sudah terdaftar.';
            }
        }

        if ($password_raw === '') {
            $errors['password'] = 'Kata sandi wajib diisi.';
        } elseif (strlen($password_raw) < 6) {
            $errors['password'] = 'Kata sandi minimal 6 karakter.';
        }

        if (!$terms) {
            $errors['terms'] = 'Anda harus menyetujui ketentuan.';
        }

        if (!empty($errors)) {
            // Simpan errors dan old input ke session lalu redirect kembali
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'nama' => $nama_raw,
                'email' => $email_raw,
                'terms' => $terms
            ];
            header("Location: ../../pages/auth/register.php");
            exit();
        }

        // Siap simpan ke database
        $nama = mysqli_real_escape_string($conn, $nama_raw);
        $email = mysqli_real_escape_string($conn, $email_raw);
        $password = password_hash($password_raw, PASSWORD_BCRYPT);
        $role = 'user';

        $query = "INSERT INTO users (name, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            header("Location: ../../pages/auth/login.php?status=success");
            exit();
        } else {
            // Simpan error generik ke session agar halaman register bisa menampilkannya
            $_SESSION['errors'] = ['general' => 'Gagal membuat akun: ' . mysqli_error($conn)];
            $_SESSION['old'] = ['nama' => $nama_raw, 'email' => $email_raw, 'terms' => $terms];
            header("Location: ../../pages/auth/register.php");
            exit();
        }
    }

    // --- LOGIKA LOGIN ---
    if ($action === 'login') {
        $email_raw = trim($_POST['email'] ?? '');
        $password_raw = $_POST['password'] ?? '';

        $errors = [];

        if ($email_raw === '') {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($email_raw, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Masukkan alamat email yang valid.';
        }

        if ($password_raw === '') {
            $errors['password'] = 'Kata sandi wajib diisi.';
        }

        if (!empty($errors)) {
            $_SESSION['errors_login'] = $errors;
            $_SESSION['old_login'] = [
                'email' => $email_raw
            ];
            header("Location: ../../pages/auth/login.php");
            exit();
        }

        $email = mysqli_real_escape_string($conn, $email_raw);
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password_raw, $user['password'])) {
                unset($_SESSION['errors_login'], $_SESSION['old_login']);
                $_SESSION['is_login'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                if ($user['role'] === 'admin') {
                    header("Location: ../../pages/admin/dashboard.php");
                } else {
                    header("Location: ../../index.php");
                }

                exit();
            } else {
                $_SESSION['errors_login'] = ['password' => 'Kata sandi yang Anda masukkan tidak sesuai.'];
                $_SESSION['old_login'] = ['email' => $email_raw];
                header("Location: ../../pages/auth/login.php");
                exit();
            }
        } else {
            $_SESSION['errors_login'] = ['email' => 'Email kerja yang Anda masukkan belum terdaftar.'];
            $_SESSION['old_login'] = ['email' => $email_raw];
            header("Location: ../../pages/auth/login.php");
            exit();
        }
    }

    if ($_POST['action'] === 'forgot_password') {

        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($password !== $confirmPassword) {

            $_SESSION['errors_login']['general'] =
                'Konfirmasi password tidak sama';

            header('Location: ../../pages/auth/login.php');
            exit;
        }

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user) {

            $_SESSION['errors_login']['general'] =
                'Email tidak ditemukan';

            header('Location: ../../pages/auth/login.php');
            exit;
        }

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
         SET password = ?
         WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $hashedPassword,
            $user['id']
        );

        mysqli_stmt_execute($stmt);

        header(
            'Location: ../../pages/auth/login.php?status=password_reset'
        );
        exit;
    }
}

?>