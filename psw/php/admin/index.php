<?php
require_once '../config.php';
require_once '../auth_check.php';

$db = getDB();

// Stats
$totalProducts = $db->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalCategories = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalMessages = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn();
$totalBrands = $db->query("SELECT COUNT(*) FROM brands WHERE is_active=1")->fetchColumn();

// Products list
$products = ries c ON p.category_id=c.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

$catIcons = ['laptop'=>'💻','pc-komputer'=>'🖥️','aksesoris'=>'🖱️','hardware'=>'⚙️','printer'=>'🖨️','monitor'=>'📺','gaming'=>'🎮'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel — Index Computer</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="../../css/style.css?v=2.0.8">
  <script>
    (function(){
      var saved = localStorage.getItem('theme');
      var prefer = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
      var theme = saved || prefer;
      if(theme === 'light') document.documentElement.setAttribute('data-theme','light');
    })();
  </script>
</head>
<body>

<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-logo">
      Index<span>.</span>Admin
    </div>
    <nav class="admin-nav">
      <a href="index.php" class="active">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="products.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        Produk
      </a>
      <a href="categories.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        Kategori
      </a>
      <a href="messages.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 0 2 2z"/></svg>
        Pesan <?php if ($totalMessages > 0): ?><span style="background:var(--accent-3);color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;margin-left:4px;"><?= $totalMessages ?></span><?php endif; ?>
      </a>
      <a href="../../index.php" target="_blank">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Lihat Website
      </a>
      <a href="logout.php" style="color:var(--accent-3);margin-top:auto;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
      </a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="admin-main">
    <div class="admin-header" style="display:flex;align-items:flex-start;justify-content:space-between;">
      <div>
        <div class="admin-title">Dashboard</div>
        <div style="color:var(--text-muted);font-size:14px;margin-top:4px;">Selamat datang, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>!</div>
      </div>
      <!-- Theme Toggle Button -->
      <button id="adminThemeToggle" onclick="toggleAdminTheme()" title="Ganti tema" aria-label="Toggle light/dark mode"
        style="display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--border);color:var(--text);font-family:var(--font-body);font-size:13px;font-weight:600;padding:8px 16px;border-radius:24px;cursor:pointer;transition:var(--transition);flex-shrink:0;">
        <svg id="adminThemeIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <!-- rendered by JS -->
        </svg>
        <span id="adminThemeLabel">Light Mode</span>
      </button>
    </div>

    <!-- Stats -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-card-label">Total Produk Aktif</div>
        <div class="stat-card-value yellow"><?= $totalProducts ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Kategori</div>
        <div class="stat-card-value cyan"><?= $totalCategories ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Pesan Belum Dibaca</div>
        <div class="stat-card-value" style="color:var(--accent-3);"><?= $totalMessages ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Brand Aktif</div>
        <div class="stat-card-value"><?= $totalBrands ?></div>
      </div>
    </div>

    <!-- Dashboard Tables Side by Side -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

      <!-- Recent Products Table -->
      <div class="admin-table-wrap">
        <div class="admin-table-header">
          <div class="admin-table-title">Produk Terbaru</div>
          <a href="products.php" class="btn-edit">Kelola Semua →</a>
        </div>
        <table>
          <thead>
            <tr>
              <th>Produk</th>
              <th>Kategori</th>
              <th>Harga</th>
              <th>Stok</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
              <td>
                <div style="font-family:var(--font-display);font-weight:700;font-size:13px;"><?= htmlspecialchars($p['name']) ?></div>
                <?php if ($p['brand']): ?><div style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($p['brand']) ?></div><?php endif; ?>
              </td>
              <td style="color:var(--text-muted);font-size:13px;"><?= htmlspecialchars($p['cat_name']) ?></td>
              <td style="font-family:var(--font-display);font-weight:700;color:var(--accent);"><?= formatRupiah($p['price']) ?></td>
              <td style="font-size:13px;"><?= $p['stock'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:40px;">Belum ada produk.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Recent Messages -->
      <div class="admin-table-wrap">
        <div class="admin-table-header">
          <div class="admin-table-title">Pesan Masuk</div>
          <a href="messages.php" class="btn-edit">Lihat Semua →</a>
        </div>
        <table>
          <thead>
            <tr><th>Nama</th><th>Topik</th><th>Pesan</th><th>Status</th><th>Waktu</th></tr>
          </thead>
          <tbody>
            <?php foreach ($messages as $msg): ?>
            <tr>
              <td>
                <div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($msg['name']) ?></div>
                <?php if ($msg['phone']): ?><div style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($msg['phone']) ?></div><?php endif; ?>
              </td>
              <td style="font-size:13px;color:var(--text-muted);"><?= htmlspecialchars($msg['subject'] ?: '-') ?></td>
              <td style="font-size:13px;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($msg['message']) ?></td>
              <td>
                <span class="<?= $msg['status']==='unread' ? 'badge-inactive' : 'badge-active' ?>">
                  <?= $msg['status'] === 'unread' ? 'Belum Dibaca' : 'Dibaca' ?>
                </span>
              </td>
              <td style="font-size:12px;color:var(--text-muted);"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px;">Belum ada pesan masuk.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>

<script src="../../js/main.js"></script>
<script>
function editProduct(id) {
  // Redirect ke halaman edit produk
  window.location.href = `products.php?edit=${id}`;
}

// Admin theme toggle
function renderAdminThemeIcon(isLight) {
  var icon = document.getElementById('adminThemeIcon');
  if (!icon) return;
  if (isLight) {
    // Moon icon (switch to dark)
    icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
  } else {
    // Sun icon (switch to light)
    icon.innerHTML = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
  }
}
function toggleAdminTheme() {
  var html = document.documentElement;
  var isLight = html.getAttribute('data-theme') === 'light';
  var label = document.getElementById('adminThemeLabel');
  if (isLight) {
    html.removeAttribute('data-theme');
    localStorage.setItem('theme', 'dark');
    if (label) label.textContent = 'Light Mode';
    renderAdminThemeIcon(false);
  } else {
    html.setAttribute('data-theme', 'light');
    localStorage.setItem('theme', 'light');
    if (label) label.textContent = 'Dark Mode';
    renderAdminThemeIcon(true);
  }
}
// Init icon & label on load
(function(){
  var isLight = document.documentElement.getAttribute('data-theme') === 'light';
  var label = document.getElementById('adminThemeLabel');
  if (label) label.textContent = isLight ? 'Dark Mode' : 'Light Mode';
  renderAdminThemeIcon(isLight);
})();
</script>
</body>
</html>