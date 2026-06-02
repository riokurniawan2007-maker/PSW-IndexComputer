<?php
// ============================================
// INDEX COMPUTER - Database Configuration
// ============================================

define('DB_HOST', 'sql205.infinityfree.com');
define('DB_NAME', 'if0_41981904_index_computer');
define('DB_USER', 'if0_41981904');
define('DB_PASS', 'PSRDldfW2uQOAje');
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', '3306');

define('SITE_NAME', 'Index Computer');
define('SITE_TAGLINE', 'Toko Komputer Terpercaya di Batam');
define('SITE_PHONE', '0815-3662-8362');
define('SITE_EMAIL', 'yenni.gudangtoko@gmail.com');
define('SITE_ADDRESS', 'BCS Mall Lantai Basement Blok A1 No.5,6,7, Jl. Bunga Raya, Batu Selicin, Kec. Lubuk Baja, Kota Batam, Kepulauan Riau 29444');
define('SITE_HOURS', '10.00 – 22.00 WIB');
define('SITE_WHATSAPP', '6281536628362');
define('SITE_TOKOPEDIA', 'https://www.tokopedia.com/indexcomputer');
define('SITE_INSTAGRAM', 'https://instagram.com/indexcomputer');
define('SITE_FACEBOOK', 'https://facebook.com/indexcomputer');
define('GEMINI_API_KEY', 'AIzaSyDKU6labhBJS_1n29qY7E_T5p-Z6w_0780');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 10,
            ]);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

function formatRupiah($amount) {
    return 'Rp' . number_format($amount, 0, ',', '.');
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}