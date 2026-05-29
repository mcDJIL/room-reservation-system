<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;

if (empty($role) || $role !== 'admin') {
    if (!headers_sent()) {
        header("Location: /index.php");
        exit;
    }

    // Fallback when output is already sent before middleware is included.
    echo '<script>window.location.replace("/index.php");</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=/index.php"></noscript>';
    exit;
}