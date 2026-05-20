<?php
session_start();

// 1. Masuk ke folder config, lalu ambil database.php
require_once '../../config/database.php';

// 2. Masuk ke folder config, lalu ambil connection.php
require_once '../../config/connection.php';

// Sekarang variabel $conn sudah siap digunakan untuk query database!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- LOGIKA REGISTER ---
    if ($action === 'register') {
        $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role     = 'user';

        $query = "INSERT INTO users (name, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        
        if (mysqli_query($conn, $query)) {
            header("Location: ../../pages/auth/login.php?status=success");
         exit();
        } else {
            echo "Error Register: " . mysqli_error($conn);
        }
    }

    // --- LOGIKA LOGIN ---
      if ($action === 'login') {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query  = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['is_login'] = true;
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['name']     = $user['name'];

            // Arahkan kembali ke home setelah login sukses
            header("Location: ../../index.php");
            exit(); 
       } else {
            header("Location: ../../pages/auth/login.php?error=wrong_password");
            exit();
        }
    } else {
        header("Location: ../../pages/auth/login.php?error=email_not_found");
        exit();
    }
}
    }

?>