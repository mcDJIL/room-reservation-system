<?php

date_default_timezone_set('Asia/Jakarta');

include __DIR__ . "/database.php";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    die("Koneksi Gagal");
}