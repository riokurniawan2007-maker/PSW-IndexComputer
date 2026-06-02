<?php
// php/auth_check.php — include di setiap halaman admin
// Pastikan config.php sudah di-require sebelum file ini

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    // login.php ada di folder yang sama dengan file admin (php/admin/)
    header('Location: login.php');
    exit;
}