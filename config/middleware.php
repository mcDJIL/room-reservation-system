<?php

$role = $_SESSION['role'];

if ($role !== 'admin') {
    header("Location: ../../index.php");
}