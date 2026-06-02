<?php
require_once '../config.php';
require_once '../auth_check.php';

$db = getDB();
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// ============================================
// HANDLE FORM ACTIONS
// ============================================

$success = '';
$error   = '';

// ---- UPLOAD HELPER ----
function handleImageUpload($fileInput, $oldImage = '') {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldImage; // tidak ada file baru, kembalikan yang lama
    }

    $file     = $_FILES[$fileInput];
    $maxSize  = 2 * 1024 * 1024; // 2MB
    $allowed  = ['image/jpeg', 'image/png', 'image/webp'];
    $uploadDir = '../../images/products/';

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload gagal. Kode error: ' . $file['error']);
    }
    if ($file['size'] > $maxSize) {
        throw new Exception('Ukuran file maksimal 2MB.');
    }
    if (!in_array($file['type'], $allowed)) {
        throw new Exception('Format file harus JPG, PNG, atau WebP.');
    }

    // Buat folder jika belum ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate nama file unik
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    $destPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new Exception('Gagal memindahkan file upload.');
    }

    // Hapus foto lama jika ada
    if ($oldImage && file_exists('../../' . $oldImage)) {
        unlink('../../' . $oldImage);
    }

    return 'images/products/' . $filename;
}

// ---- TAMBAH PRODUK ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    try {
        $name        = sanitize($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $brand       = sanitize($_POST['brand'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $specs       = sanitize($_POST['specifications'] ?? '');
        $price       = (float)str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0');
        $stock       = (int)($_POST['stock'] ?? 0);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = isset($_POST['is_active']) ? 1 : 0;

        if (!$name || !$category_id || $price <= 0) {
            throw new Exception('Nama, kategori, dan harga wajib diisi.');
        }

        // Buat slug dari nama
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name)) . '-' . time();

        $imageUrl = handleImageUpload('image');

        $stmt = $db->prepare("INSERT INTO products (category_id, name, slug, brand, description, specifications, price, stock, image_url, is_featured, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$category_id, $name, $slug, $brand, $description, $specs, $price, $stock, $imageUrl, $is_featured, $is_active]);

        $success = 'Produk "' . htmlspecialchars($name) . '" berhasil ditambahkan!';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ---- EDIT PRODUK ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    try {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = sanitize($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $brand       = sanitize($_POST['brand'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $specs       = sanitize($_POST['specifications'] ?? '');
        $price       = (float)str_replace(['.', ','], ['', '.'], $_POST['price'] ?? '0');
        $stock       = (int)($_POST['stock'] ?? 0);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = isset($_POST['is_active']) ? 1 : 0;

        if (!$id || !$name || !$category_id || $price <= 0) {
            throw new Exception('Data tidak valid.');
        }

        // Ambil data lama untuk cek foto
        $old = $db->prepare("SELECT image_url FROM products WHERE id=?");
        $old->execute([$id]);
        $oldProduct = $old->fetch();

        $imageUrl = handleImageUpload('image', $oldProduct['image_url'] ?? '');

        $stmt = $db->prepare("UPDATE products SET category_id=?, name=?, brand=?, description=?, specifications=?, price=?, stock=?, image_url=?, is_featured=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$category_id, $name, $brand, $description, $specs, $price, $stock, $imageUrl, $is_featured, $is_active, $id]);

        $success = 'Produk "' . htmlspecialchars($name) . '" berhasil diupdate!';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ---- HAPUS PRODUK ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) throw new Exception('ID tidak valid.');

        // Hapus foto jika ada
        $stmt = $db->prepare("SELECT image_url FROM products WHERE id=?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch();
        if ($prod && $prod['image_url'] && file_exists('../../' . $prod['image_url'])) {
            unlink('../../' . $prod['image_url']);
        }

        $stmt = $db->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$id]);

        $success = 'Produk berhasil dihapus.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ---- AMBIL DATA EDIT (AJAX) ----
if (isset($_GET['get_product'])) {
    $id   = (int)$_GET['get_product'];
    $stmt = $db->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    header('Content-Type: application/json');
    echo json_encode($stmt->fetch());
    exit;
}

// ============================================
// TAMPIL DATA
// ============================================

// Filter & search
$search    = sanitize($_GET['search'] ?? '');
$catFilter = (int)($_GET['cat'] ?? 0);

// Pagination
$perPage     = 15;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($currentPage - 1) * $perPage;

$where  = [];
$params = [];
if ($search) {
    $where[]  = '(p.name LIKE ? OR p.brand LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($catFilter) {
    $where[]  = 'p.category_id = ?';
    $params[] = $catFilter;
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Total count
$countParams = $params;
$countStmt   = $db->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id=c.id $whereSQL");
$countStmt->execute($countParams);
$totalFiltered = (int)$countStmt->fetchColumn();
$totalPages    = max(1, (int)ceil($totalFiltered / $perPage));

$mainSQL = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id $whereSQL ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($mainSQL);

$idx = 1;
foreach ($params as $p) {
    $stmt->bindValue($idx++, $p);
}
$stmt->bindValue($idx++, (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue($idx++, (int)$offset,  PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$totalAll      = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalActive   = $db->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalFeatured = $db->query("SELECT COUNT(*) FROM products WHERE is_featured=1")->fetchColumn();

// Helper URL pagination admin
function adminPageUrl($page) {
    $p = $_GET;
    $p['page'] = $page;
    return 'products.php?' . http_build_query($p);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Produk — Admin Index Computer</title>
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
  <style>
    /* Upload preview */
    .upload-area {
      border: 2px dashed var(--border);
      border-radius: var(--radius);
      padding: 28px;
      text-align: center;
      cursor: pointer;
      transition: var(--transition);
      background: var(--bg-3);
      position: relative;
    }
    .upload-area:hover, .upload-area.drag-over {
      border-color: var(--accent);
      background: rgba(59,130,246,0.05);
    }
    .upload-area input[type="file"] {
      position: absolute; inset: 0;
      opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .upload-icon { font-size: 32px; margin-bottom: 8px; }
    .upload-text { font-size: 14px; color: var(--text-muted); }
    .upload-text span { color: var(--accent); font-weight: 600; }
    .upload-preview {
      width: 100%; max-height: 180px;
      object-fit: contain;
      border-radius: var(--radius);
      margin-top: 12px;
      display: none;
      border: 1px solid var(--border);
    }
    .upload-preview.show { display: block; }

    /* Current image */
    .current-img {
      width: 60px; height: 60px;
      object-fit: cover;
      border-radius: var(--radius);
      border: 1px solid var(--border);
    }
    .no-img {
      width: 60px; height: 60px;
      background: var(--bg-3);
      border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px;
      border: 1px solid var(--border);
    }

    /* Toggle switch */
    .toggle-wrap {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 12px;
    }
    .toggle {
      position: relative; width: 40px; height: 22px;
    }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
      position: absolute; inset: 0;
      background: var(--bg-3);
      border: 1px solid var(--border);
      border-radius: 11px;
      cursor: pointer;
      transition: var(--transition);
    }
    .toggle-slider::before {
      content: '';
      position: absolute;
      width: 16px; height: 16px;
      left: 2px; top: 2px;
      background: var(--text-muted);
      border-radius: 50%;
      transition: var(--transition);
    }
    .toggle input:checked + .toggle-slider { background: var(--accent); border-color: var(--accent); }
    .toggle input:checked + .toggle-slider::before { transform: translateX(18px); background: #fff; }
    .toggle-label { font-size: 14px; color: var(--text-muted); }

    /* Alert */
    .page-alert {
      padding: 14px 18px;
      border-radius: var(--radius);
      font-size: 14px; font-weight: 500;
      margin-bottom: 24px;
      display: flex; align-items: center; gap: 10px;
    }
    .page-alert.success { background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.3); color: #06b6d4; }
    .page-alert.error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.3);  color: #ef4444; }

    /* Form grid */
    .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    /* Filter bar */
    .filter-bar {
      display: flex; gap: 12px; align-items: center; flex-wrap: wrap;
      margin-bottom: 20px;
    }
    .filter-bar input, .filter-bar select {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      color: var(--text); font-family: var(--font-body);
      font-size: 14px; padding: 9px 14px;
      outline: none; transition: var(--transition);
    }
    .filter-bar input:focus, .filter-bar select:focus { border-color: var(--accent); }
    .filter-bar input { width: 220px; }
    .records-info { font-size: 13px; color: var(--text-muted); margin-left: auto; }

    /* Table image column */
    td.img-cell { width: 72px; }

    @media (max-width: 768px) {
      .form-grid-3 { grid-template-columns: 1fr; }
      .form-grid-2 { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<div class="admin-layout">
  <!-- ===== SIDEBAR ===== -->
  <aside class="admin-sidebar">
    <div class="admin-logo">Index<span>.</span>Admin</div>
    <nav class="admin-nav">
      <a href="index.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="products.php" class="active">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        Produk
      </a>
      <a href="categories.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        Kategori
      </a>
      <a href="messages.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 0 2 2z"/></svg>
        Pesan
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

  <!-- ===== MAIN ===== -->
  <main class="admin-main">
    <div class="admin-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
      <div>
        <div class="admin-title">Kelola Produk</div>
        <div style="color:var(--text-muted);font-size:14px;margin-top:4px;">
          Total: <strong style="color:var(--text)"><?= $totalAll ?></strong> produk &nbsp;·&nbsp;
          Aktif: <strong style="color:var(--accent-2)"><?= $totalActive ?></strong> &nbsp;·&nbsp;
          Featured: <strong style="color:var(--accent)"><?= $totalFeatured ?></strong>
        </div>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <button id="adminThemeToggle" onclick="toggleAdminTheme()" title="Ganti tema" aria-label="Toggle light/dark mode"
          style="display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--border);color:var(--text);font-family:var(--font-body);font-size:13px;font-weight:600;padding:8px 16px;border-radius:24px;cursor:pointer;transition:var(--transition);flex-shrink:0;">
          <svg id="adminThemeIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
          <span id="adminThemeLabel">Light Mode</span>
        </button>
        <button class="btn-save" onclick="openModal('modalTambah')" style="display:flex;align-items:center;gap:8px;padding:12px 20px;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Tambah Produk
        </button>
      </div>
    </div>

    <!-- Alert -->
    <?php if ($success): ?>
    <div class="page-alert success">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="page-alert error">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <form method="GET" class="filter-bar" style="align-items:center;">
      <div style="display:flex;gap:0;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;background:var(--surface);">
        <input type="text" name="search" placeholder="Cari nama / brand..."
               value="<?= htmlspecialchars($search) ?>"
               style="border:none;border-radius:0;background:transparent;padding:10px 14px;font-size:14px;color:var(--text);outline:none;width:220px;">
        <button type="submit"
                style="background:var(--accent);color:var(--bg);border:none;padding:0 18px;font-family:var(--font-display);font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;transition:var(--transition);">
          Cari
        </button>
      </div>
      <select name="cat" onchange="this.form.submit()"
              style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);color:var(--text);font-family:var(--font-body);font-size:14px;padding:10px 14px;outline:none;cursor:pointer;height:42px;">
        <option value="">Semua Kategori</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>" <?= $catFilter == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($search || $catFilter): ?>
      <a href="products.php"
         style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--text-muted);padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius);transition:var(--transition);">
        ✕ Reset
      </a>
      <?php endif; ?>
      <div class="records-info">
        <?php if ($totalFiltered > 0): ?>
          Menampilkan <?= $offset + 1 ?>–<?= min($offset + $perPage, $totalFiltered) ?> dari <?= $totalFiltered ?> produk
        <?php else: ?>
          0 produk ditemukan
        <?php endif; ?>
      </div>
    </form>

    <!-- Tabel Produk -->
    <div class="admin-table-wrap" style="overflow-x:auto;">
      <table style="min-width:900px;">
        <thead>
          <tr>
            <th style="width:50px;">No</th>
            <th style="width:72px;">Foto</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status</th>
            <th style="width:160px;white-space:nowrap;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
          <tr>
            <td colspan="8" style="text-align:center;padding:48px;color:var(--text-muted);">
              <div style="font-size:36px;margin-bottom:12px;">📦</div>
              Tidak ada produk ditemukan.
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($products as $i => $p): ?>
          <tr>
            <td style="color:var(--text-muted);font-size:13px;"><?= $offset + $i + 1 ?></td>
            <td class="img-cell">
              <?php if ($p['image_url']): ?>
                <img src="../../<?= htmlspecialchars($p['image_url']) ?>" alt="" class="current-img">
              <?php else: ?>
                <div class="no-img">📦</div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-family:var(--font-display);font-weight:700;font-size:13px;line-height:1.3;"><?= htmlspecialchars($p['name']) ?></div>
              <?php if ($p['brand']): ?>
              <div style="font-size:12px;color:var(--text-muted);margin-top:2px;"><?= htmlspecialchars($p['brand']) ?></div>
              <?php endif; ?>
              <?php if ($p['is_featured']): ?>
              <span style="font-size:10px;background:rgba(59,130,246,0.15);color:var(--accent);padding:2px 6px;border-radius:4px;margin-top:4px;display:inline-block;">⭐ Featured</span>
              <?php endif; ?>
            </td>
            <td style="font-size:13px;color:var(--text-muted);"><?= htmlspecialchars($p['cat_name']) ?></td>
            <td style="font-family:var(--font-display);font-weight:700;color:var(--accent);font-size:14px;"><?= formatRupiah($p['price']) ?></td>
            <td style="font-size:13px;">
              <span style="color:<?= $p['stock'] <= 3 ? 'var(--accent-3)' : ($p['stock'] <= 10 ? '#f59e0b' : 'var(--accent-2)') ?>">
                <?= $p['stock'] ?>
              </span>
            </td>
            <td>
              <?php if ($p['is_active']): ?>
                <span class="badge-active">Aktif</span>
              <?php else: ?>
                <span class="badge-inactive">Nonaktif</span>
              <?php endif; ?>
            </td>
            <td style="white-space:nowrap;">
              <div class="table-actions" style="flex-wrap:nowrap;">
                <button class="btn-edit" onclick="openEdit(<?= $p['id'] ?>)" style="display:inline-flex;align-items:center;gap:5px;white-space:nowrap;">
                  
                  Edit
                </button>
                <button class="btn-delete" onclick="hapusProduk(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')" style="display:inline-flex;align-items:center;gap:5px;white-space:nowrap;">
                  
                  Hapus
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION ADMIN -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-wrap">
      <span class="pagination-info">
        Halaman <span><?= $currentPage ?></span> dari <span><?= $totalPages ?></span>
      </span>

      <?php if ($currentPage > 1): ?>
      <a href="<?= adminPageUrl($currentPage - 1) ?>" class="page-btn page-arrow" title="Sebelumnya">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
      </a>
      <?php endif; ?>

      <?php
      $range = 2;
      $start = max(1, $currentPage - $range);
      $end   = min($totalPages, $currentPage + $range);
      if ($start > 1): ?>
        <a href="<?= adminPageUrl(1) ?>" class="page-btn">1</a>
        <?php if ($start > 2): ?><span class="page-dots">···</span><?php endif; ?>
      <?php endif; ?>

      <?php for ($i = $start; $i <= $end; $i++): ?>
      <a href="<?= adminPageUrl($i) ?>" class="page-btn <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?><span class="page-dots">···</span><?php endif; ?>
        <a href="<?= adminPageUrl($totalPages) ?>" class="page-btn"><?= $totalPages ?></a>
      <?php endif; ?>

      <?php if ($currentPage < $totalPages): ?>
      <a href="<?= adminPageUrl($currentPage + 1) ?>" class="page-btn page-arrow" title="Berikutnya">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </main>
</div>

<!-- ============================================
     MODAL TAMBAH PRODUK
     ============================================ -->
<div class="modal-overlay" id="modalTambah">
  <div class="modal" style="width:640px;">
    <div class="modal-title">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:8px;color:var(--accent)"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Produk Baru
    </div>

    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add">

      <!-- Foto -->
      <div class="form-group">
        <label class="form-label">Foto Produk</label>
        <div class="upload-area" id="uploadAreaAdd">
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this, 'previewAdd')">
          <div class="upload-icon">📷</div>
          <div class="upload-text">Drag & drop atau <span>klik untuk pilih</span></div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">JPG, PNG, WebP · Maks. 2MB</div>
          <img id="previewAdd" class="upload-preview" alt="Preview">
        </div>
      </div>

      <!-- Nama & Kategori -->
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">Nama Produk *</label>
          <input type="text" name="name" class="form-input" placeholder="Contoh: Logitech G102" required>
        </div>
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="category_id" class="form-select" required>
            <option value="">Pilih kategori...</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Brand & Stok -->
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">Brand</label>
          <input type="text" name="brand" class="form-input" placeholder="Contoh: Logitech">
        </div>
        <div class="form-group">
          <label class="form-label">Stok</label>
          <input type="number" name="stock" class="form-input" value="0" min="0">
        </div>
      </div>

      <!-- Harga -->
      <div class="form-group">
        <label class="form-label">Harga (Rp) *</label>
        <input type="text" name="price" class="form-input" placeholder="Contoh: 350000" required
               oninput="formatHarga(this)">
      </div>

      <!-- Deskripsi -->
      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-textarea" style="min-height:80px;" placeholder="Deskripsi singkat produk..."></textarea>
      </div>

      <!-- Spesifikasi -->
      <div class="form-group">
        <label class="form-label">Spesifikasi</label>
        <textarea name="specifications" class="form-textarea" style="min-height:80px;" placeholder="Contoh: Intel Core i5, 8GB RAM, 512GB SSD..."></textarea>
      </div>

      <!-- Toggle -->
      <div style="display:flex;gap:24px;margin-bottom:8px;">
        <label class="toggle-wrap">
          <label class="toggle">
            <input type="checkbox" name="is_active" checked>
            <span class="toggle-slider"></span>
          </label>
          <span class="toggle-label">Produk Aktif</span>
        </label>
        <label class="toggle-wrap">
          <label class="toggle">
            <input type="checkbox" name="is_featured">
            <span class="toggle-slider"></span>
          </label>
          <span class="toggle-label">Featured (tampil di Beranda)</span>
        </label>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('modalTambah')">Batal</button>
        <button type="submit" class="btn-save">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:6px"><polyline points="20 6 9 17 4 12"/></svg>
          Simpan Produk
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================
     MODAL EDIT PRODUK
     ============================================ -->
<div class="modal-overlay" id="modalEdit">
  <div class="modal" style="width:640px;">
    <div class="modal-title">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:8px;color:var(--accent-2)"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Edit Produk
    </div>

    <form method="POST" enctype="multipart/form-data" id="formEdit">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="editId">

      <!-- Foto -->
      <div class="form-group">
        <label class="form-label">Foto Produk</label>
        <div style="display:flex;gap:16px;align-items:flex-start;">
          <div>
            <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px;">Foto saat ini:</div>
            <img id="editCurrentImg" src="" alt="" class="current-img" style="width:80px;height:80px;">
            <div id="editNoImg" class="no-img" style="width:80px;height:80px;display:none;">📦</div>
          </div>
          <div style="flex:1;">
            <div class="upload-area" id="uploadAreaEdit">
              <input type="file" name="image" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this, 'previewEdit')">
              <div class="upload-icon" style="font-size:24px;">🔄</div>
              <div class="upload-text" style="font-size:13px;">Klik untuk <span>ganti foto</span></div>
              <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">Kosongkan jika tidak ingin ganti</div>
              <img id="previewEdit" class="upload-preview" alt="Preview">
            </div>
          </div>
        </div>
      </div>

      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">Nama Produk *</label>
          <input type="text" name="name" id="editName" class="form-input" required>
        </div>
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="category_id" id="editCategory" class="form-select" required>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">Brand</label>
          <input type="text" name="brand" id="editBrand" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Stok</label>
          <input type="number" name="stock" id="editStock" class="form-input" min="0">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Harga (Rp) *</label>
        <input type="text" name="price" id="editPrice" class="form-input" required oninput="formatHarga(this)">
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" id="editDescription" class="form-textarea" style="min-height:80px;"></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Spesifikasi</label>
        <textarea name="specifications" id="editSpecs" class="form-textarea" style="min-height:80px;"></textarea>
      </div>

      <div style="display:flex;gap:24px;margin-bottom:8px;">
        <label class="toggle-wrap">
          <label class="toggle">
            <input type="checkbox" name="is_active" id="editActive">
            <span class="toggle-slider"></span>
          </label>
          <span class="toggle-label">Produk Aktif</span>
        </label>
        <label class="toggle-wrap">
          <label class="toggle">
            <input type="checkbox" name="is_featured" id="editFeatured">
            <span class="toggle-slider"></span>
          </label>
          <span class="toggle-label">Featured (tampil di Beranda)</span>
        </label>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('modalEdit')">Batal</button>
        <button type="submit" class="btn-save">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:6px"><polyline points="20 6 9 17 4 12"/></svg>
          Update Produk
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal-overlay" id="modalHapus">
  <div class="modal" style="width:420px;text-align:center;">
    <div style="font-size:48px;margin-bottom:16px;">🗑️</div>
    <div class="modal-title" style="justify-content:center;">Hapus Produk?</div>
    <p style="color:var(--text-muted);font-size:14px;margin-bottom:8px;">Produk <strong id="hapusNama" style="color:var(--text)"></strong> akan dihapus permanen beserta fotonya.</p>
    <p style="color:var(--accent-3);font-size:13px;margin-bottom:24px;">Tindakan ini tidak bisa dibatalkan.</p>
    <form method="POST" id="formHapus">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" id="hapusId">
      <div class="modal-actions" style="justify-content:center;">
        <button type="button" class="btn-cancel" onclick="closeModal('modalHapus')">Batal</button>
        <button type="submit" style="padding:10px 24px;border-radius:var(--radius);background:var(--accent-3);color:#fff;font-family:var(--font-display);font-size:14px;font-weight:700;transition:var(--transition);">
          Ya, Hapus
        </button>
      </div>
    </form>
  </div>
</div>

<script src="../../js/main.js"></script>
<script>
// ---- Modal helpers ----
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});

// ---- Preview gambar ----
function previewImage(input, previewId) {
  const preview = document.getElementById(previewId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.add('show');
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// ---- Format harga (strip non-digit, handle decimals from DB) ----
function formatHarga(input) {
  // Parse as float first to handle DB values like '5799000.00',
  // then convert to integer string to strip decimals safely
  let num = parseFloat(input.value);
  if (!isNaN(num)) {
    input.value = Math.round(num).toString();
  } else {
    input.value = input.value.replace(/\D/g, '');
  }
}

// ---- Buka modal edit + isi data ----
async function openEdit(id) {
  try {
    const res  = await fetch(`products.php?get_product=${id}`);
    const data = await res.json();
    if (!data) return alert('Data tidak ditemukan.');

    document.getElementById('editId').value          = data.id;
    document.getElementById('editName').value        = data.name;
    document.getElementById('editBrand').value       = data.brand || '';
    document.getElementById('editCategory').value    = data.category_id;
    document.getElementById('editStock').value       = data.stock;
    document.getElementById('editPrice').value       = Math.round(parseFloat(data.price)) || 0;
    document.getElementById('editDescription').value = data.description || '';
    document.getElementById('editSpecs').value       = data.specifications || '';
    document.getElementById('editActive').checked    = data.is_active == 1;
    document.getElementById('editFeatured').checked  = data.is_featured == 1;

    // Foto saat ini
    const imgEl   = document.getElementById('editCurrentImg');
    const noImgEl = document.getElementById('editNoImg');
    if (data.image_url) {
      imgEl.src = '../../' + data.image_url;
      imgEl.style.display = 'block';
      noImgEl.style.display = 'none';
    } else {
      imgEl.style.display = 'none';
      noImgEl.style.display = 'flex';
    }

    // Reset preview gambar baru
    const prev = document.getElementById('previewEdit');
    prev.src = '';
    prev.classList.remove('show');

    openModal('modalEdit');
  } catch (err) {
    alert('Gagal memuat data produk.');
  }
}

// ---- Konfirmasi hapus ----
function hapusProduk(id, nama) {
  document.getElementById('hapusId').value  = id;
  document.getElementById('hapusNama').textContent = nama;
  openModal('modalHapus');
}

// ---- Drag & drop visual ----
document.querySelectorAll('.upload-area').forEach(area => {
  area.addEventListener('dragover',  e => { e.preventDefault(); area.classList.add('drag-over'); });
  area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
  area.addEventListener('drop',      e => { e.preventDefault(); area.classList.remove('drag-over'); });
});

// Admin theme toggle
function renderAdminThemeIcon(isLight) {
  var icon = document.getElementById('adminThemeIcon');
  if (!icon) return;
  if (isLight) {
    icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
  } else {
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
(function(){
  var isLight = document.documentElement.getAttribute('data-theme') === 'light';
  var label = document.getElementById('adminThemeLabel');
  if (label) label.textContent = isLight ? 'Dark Mode' : 'Light Mode';
  renderAdminThemeIcon(isLight);
})();
</script>
</body>
</html>