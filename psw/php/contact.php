<?php
// php/contact.php — handle contact form submission
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$name    = sanitize($_POST['name']    ?? '');
$email   = sanitize($_POST['email']   ?? '');
$phone   = sanitize($_POST['phone']   ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');

if (!$name || !$message) {
    echo json_encode(['success' => false, 'message' => 'Nama dan pesan wajib diisi.']);
    exit;
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid.']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $subject, $message]);
    echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim! Kami akan segera menghubungi kamu.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesan. Silakan coba lagi.']);
}