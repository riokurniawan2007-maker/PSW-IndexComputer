<?php
require_once '../config.php';
require_once '../auth_check.php';

$db = getDB();

$success = '';
$error   = '';

// ---- TAMBAH KATEGORI ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    try {
        $name = sanitize($_POST['name'] ?? '');
        if (!$name) throw new Exception('Nama kategori wajib diisi.');

        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        
        $stmt = $db->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        $success = 'Kategori "' . htmlspecialchars($name) . '" berhasil ditambahkan!';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ---- EDIT KATEGORI ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    try {
        $id   = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        if (!$id || !$name) throw new Exception('Data tidak valid.');

        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));

        $stmt = $db->prepare("UPDATE categories SET name=?, slug=? WHERE id=?");
        $stmt->execute([$name, $slug, $id]);
        $success = 'Kategori berhasil diupdate!';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ---- HAPUS KATEGORI ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) throw new Exception('ID tidak valid.');

        // Cek apakah ada produk di kategori ini
        $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id=?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Kategori tidak bisa dihapus karena masih memiliki produk.');
        }

        $stmt = $db->prepare("DELETE FROM categories WHERE id=?");
        $stmt->execute([$id]);
        $success = 'Kategori berhasil dihapus.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ---- AMBIL DATA EDIT (AJAX) ----
if (isset($_GET['get_cat'])) {
    $id   = (int)$_GET['get_cat'];
    $stmt = $db->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$id]);
    header('Content-Type: application/json');
    echo json_encode($stmt->fetch());
    exit;
}

// ---- TAMPIL DATA ----
$search = sanitize($_GET['search'] ?? '');
$sql    = "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c";
$params = [];

if ($search) {
    $sql .= " WHERE name LIKE ?";
    $params[] = "%$search%";
}
$sql .= " ORDER BY name ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$categories = $stmt->fetchAll();

$totalMessages = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Kategori — Admin Index Computer</title>
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
    .page-alert {
      padding: 14px 18px;
      border-radius: var(--radius);
      font-size: 14px; font-weight: 500;
      margin-bottom: 24px;
      display: flex; align-items: center; gap: 10px;
    }
    .page-alert.success { background: rgba(6,182,212,0.1); border: 1px solid rgba(6,182,212,0.3); color: #06b6d4; }
    .page-alert.error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.3);  color: #ef4444; }
  </style>
</head>
<body>

<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-logo">Index<span>.</span>Admin</div>
    <nav class="admin-nav">
      <a href="index.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="products.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        Produk
      </a>
      <a href="categories.php" class="active">
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
    <div class="admin-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
      <div>
        <div class="admin-title">Kelola Kategori</div>
        <div style="color:var(--text-muted);font-size:14px;margin-top:4px;">Atur kategori produk untuk memudahkan pencarian pelanggan.</div>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <button id="adminThemeToggle" onclick="toggleAdminTheme()" title="Ganti tema" aria-label="Toggle light/dark mode"
          style="display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--border);color:var(--text);font-family:var(--font-body);font-size:13px;font-weight:600;padding:8px 16px;border-radius:24px;cursor:pointer;transition:var(--transition);flex-shrink:0;">
          <svg id="adminThemeIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
          <span id="adminThemeLabel">Light Mode</span>
        </button>
      <button class="btn-save" onclick="openModal('modalTambah')" style="display:flex;align-items:center;gap:8px;padding:12px 20px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Kategori
      </button>
      </div>
    </div>

    <?php if ($success): ?>
    <div class="page-alert success">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      <?= $success ?>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="page-alert error">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= $error ?>
    </div>
    <?php endif; ?>

    <div class="admin-table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:50px;">No</th>
            <th>Nama Kategori</th>
            <th>Slug</th>
            <th>Jumlah Produk</th>
            <th style="width:160px;text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $i => $cat): ?>
          <tr>
            <td style="color:var(--text-muted);font-size:13px;"><?= $i + 1 ?></td>
            <td style="font-weight:600;"><?= htmlspecialchars($cat['name']) ?></td>
            <td style="color:var(--text-muted);font-size:13px;"><?= htmlspecialchars($cat['slug']) ?></td>
            <td><span class="badge-active"><?= $cat['product_count'] ?> Produk</span></td>
            <td style="text-align:right;">
              <div class="table-actions" style="justify-content:flex-end;">
                <button class="btn-edit" onclick="openEdit(<?= $cat['id'] ?>)">Edit</button>
                <button class="btn-delete" onclick="confirmDelete(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>')">Hapus</button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($categories)): ?>
          <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">Belum ada kategori.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalTambah">
  <div class="modal" style="width:400px;">
    <div class="modal-title">Tambah Kategori</div>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="form-group">
        <label class="form-label">Nama Kategori</label>
        <input type="text" name="name" class="form-input" placeholder="Contoh: Laptop" required>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('modalTambah')">Batal</button>
        <button type="submit" class="btn-save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="modalEdit">
  <div class="modal" style="width:400px;">
    <div class="modal-title">Edit Kategori</div>
    <form method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="editId">
      <div class="form-group">
        <label class="form-label">Nama Kategori</label>
        <input type="text" name="name" id="editName" class="form-input" required>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeModal('modalEdit')">Batal</button>
        <button type="submit" class="btn-save">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal-overlay" id="modalHapus">
  <div class="modal" style="width:400px;text-align:center;">
    <div style="font-size:40px;margin-bottom:10px;">🗑️</div>
    <div class="modal-title" style="justify-content:center;">Hapus Kategori?</div>
    <p style="font-size:14px;color:var(--text-muted);margin-bottom:20px;">Kategori <strong id="hapusNama" style="color:var(--text)"></strong> akan dihapus permanen.</p>
    <form method="POST">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" id="hapusId">
      <div class="modal-actions" style="justify-content:center;">
        <button type="button" class="btn-cancel" onclick="closeModal('modalHapus')">Batal</button>
        <button type="submit" class="btn-delete" style="background:var(--accent-3);color:#fff;">Hapus</button>
      </div>
    </form>
  </div>
</div>

<script src="../../js/main.js"></script>
<script>
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

async function openEdit(id) {
  try {
    const res = await fetch(`categories.php?get_cat=${id}`);
    const data = await res.json();
    document.getElementById('editId').value = data.id;
    document.getElementById('editName').value = data.name;
    openModal('modalEdit');
  } catch (err) { alert('Gagal mengambil data.'); }
}

function confirmDelete(id, name) {
  document.getElementById('hapusId').value = id;
  document.getElementById('hapusNama').textContent = name;
  openModal('modalHapus');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', e => { if(e.target === o) o.classList.remove('open'); });
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
