<?php
// php/assistant_handler.php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

try {
    $db = getDB();
    
    // Fetch products list for AI context
    $products = $db->query("SELECT p.name, p.price, c.name as category, p.brand FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 LIMIT 60")->fetchAll(PDO::FETCH_ASSOC);
    
    $productText = "";
    if ($products) {
        foreach ($products as $p) {
            $productText .= "- {$p['name']} (Brand: {$p['brand']}, Kategori: {$p['category']}, Harga: " . formatRupiah($p['price']) . ")\n";
        }
    } else {
        $productText = "Katalog kosong saat ini.\n";
    }
} catch (Exception $e) {
    // Fallback if DB connection fails
    $productText = "Katalog saat ini tidak dapat dimuat langsung dari database, hubungi CS kami.\n";
}

$systemInstruction = "Kamu adalah **Index Computer AI Assistant**, asisten virtual cerdas, ramah, dan profesional untuk toko komputer **Index Computer** yang berlokasi di BCS Mall Lantai Basement Blok A1 No.5,6,7, Batam.\n\n"
    . "Tugas utama kamu adalah membantu pelanggan menjawab pertanyaan tentang:\n"
    . "1. Informasi Toko:\n"
    . "   - Alamat: " . SITE_ADDRESS . "\n"
    . "   - Telepon / WA: " . SITE_PHONE . " (Hubungi WA Sales kami: wa.me/" . SITE_WHATSAPP . ")\n"
    . "   - Jam Buka: " . SITE_HOURS . "\n"
    . "   - Rekening Pembayaran Resmi Toko:\n"
    . "     - BCA: 8325 1978 65\n"
    . "     - BRI: 0331 0155 7788 207\n"
    . "     - Mandiri: 1090 0017 72567\n"
    . "     (Semua rekening atas nama PT Sentral Index Komputindo)\n\n"
    . "2. Katalog Produk & Harga Real-Time Toko Kami saat ini:\n"
    . $productText . "\n"
    . "3. Layanan Pendukung:\n"
    . "   - Jasa Rakit PC Custom (Konsultasi gratis, komponen original bergaransi distributor resmi, rakitan rapi, diuji sebelum diserahkan, garansi perakitan).\n"
    . "   - Servis & Reparasi Laptop/PC oleh teknisi berpengalaman.\n"
    . "   - Pengiriman cepat di hari yang sama (Same-Day Delivery) untuk wilayah Batam.\n"
    . "   - Sistem Pre-Order untuk barang yang tidak tersedia di stok.\n\n"
    . "Aturan Penting saat Berbicara dengan Pelanggan:\n"
    . "- Jawablah menggunakan Bahasa Indonesia yang ramah, sopan, antusias, dan mudah dipahami.\n"
    . "- Gunakan ikon/emoji yang relevan (seperti 💻, 🖥️, ⚙️, 🖱️, 🛠️, 📞) agar pesan lebih menarik dan interaktif.\n"
    . "- Jika ditanya produk yang ada di daftar, berikan harganya secara akurat sesuai data di atas dan rekomendasikan keunggulan produk tersebut.\n"
    . "- Jika produk tidak ada dalam daftar katalog kami di atas, katakan dengan sopan bahwa produk tersebut mungkin kosong atau bisa dipesan melalui sistem Pre-Order via WhatsApp Sales kami di " . SITE_PHONE . ".\n"
    . "- Jika pelanggan ingin berkonsultasi rakit PC, tanyakan budget mereka (misal: 5 juta, 10 juta, 20 juta) dan kebutuhan penggunaan mereka (gaming, editing, kantor/sekolah), lalu berikan rekomendasi komponen terbaik yang ada dalam katalog kami.\n"
    . "- Di akhir jawaban yang relevan, ingatkan mereka bahwa mereka bisa langsung menghubungi WhatsApp Sales kami atau berkunjung ke toko kami di BCS Mall Batam.\n"
    . "- Selalu jaga jawaban agar padat, informatif, dan tidak terlalu panjang agar nyaman dibaca di widget chat melayang.";

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
$history = $input['history'] ?? [];

if (empty($message)) {
    echo json_encode(['reply' => 'Ada yang bisa saya bantu mengenai produk komputer, laptop, servis, atau rakit PC? 💻']);
    exit;
}

// Build contents array for Gemini generateContent API
$contents = [];
foreach ($history as $h) {
    $role = ($h['role'] === 'model' || $h['role'] === 'assistant') ? 'model' : 'user';
    $contents[] = [
        'role' => $role,
        'parts' => [['text' => $h['text']]]
    ];
}
$contents[] = [
    'role' => 'user',
    'parts' => [['text' => $message]]
];

$apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
if (empty($apiKey)) {
    echo json_encode(['reply' => 'Maaf, sistem chatbot sedang tidak terkonfigurasi dengan benar (API Key kosong). Silakan hubungi kami di WhatsApp ' . SITE_PHONE . ' 📞']);
    exit;
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

$payload = [
    'contents' => $contents,
    'systemInstruction' => [
        'parts' => [['text' => $systemInstruction]]
    ]
];

$maxRetries = 2;
$retryDelay = 1500000; // 1.5 seconds in microseconds
$response = false;
$httpCode = 0;
$curlError = '';

for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local XAMPP compatibility if SSL CA certs are outdated
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode === 200) {
        break;
    }
    
    // If rate limited (429) and we have retries left, wait and try again
    if ($httpCode === 429 && $attempt < $maxRetries) {
        usleep($retryDelay);
        continue;
    }
    
    break;
}

if ($response === false) {
    echo json_encode(['reply' => 'Maaf, koneksi ke asisten AI terganggu. Silakan hubungi WhatsApp kami langsung di ' . SITE_PHONE . ' 📞 (Error: ' . $curlError . ')']);
    exit;
}

$resData = json_decode($response, true);
if ($httpCode !== 200) {
    $errMsg = $resData['error']['message'] ?? 'Unknown API Error';
    if ($httpCode === 429) {
        echo json_encode(['reply' => 'Maaf, asisten AI kami sedang melayani banyak pelanggan saat ini (High Traffic). Silakan kirim kembali pesan Anda dalam beberapa detik ⚡']);
    } else {
        echo json_encode(['reply' => 'Maaf, terjadi kesalahan saat menghubungi asisten AI kami. (Error: ' . $errMsg . ')']);
    }
    exit;
}

$reply = $resData['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat merumuskan jawaban saat ini. Ada hal lain yang bisa saya bantu?';
echo json_encode(['reply' => $reply]);
