<?php

$role = $_SESSION['role'];

if (empty($role) || $role !== 'admin') {
    header("Location: ../../index.php");
}