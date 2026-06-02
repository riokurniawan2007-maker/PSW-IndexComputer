<?php
require_once '../config.php';
require_once '../auth_check.php';

$db = getDB();

$success = '';
$error   = '';

// ---- MARK AS READ/UNREAD ----
if (isset($_GET['toggle_read'])) {
    try {
        $id = (int)$_GET['toggle_read'];
        $status = sanitize($_GET['status'] ?? 'read');
        $stmt = $db->prepare("UPDATE contact_messages SET status=? WHERE id=?");
        $stmt->execute([$status, $id]);
        header('Location: messages.php?success=Status updated');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ---- HAPUS PESAN ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) throw new Exception('ID tidak valid.');

        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id=?");
        $stmt->execute([$id]);
        $success = 'Pesan berhasil dihapus.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ---- AMBIL DATA PESAN (AJAX) ----
if (isset($_GET['get_msg'])) {
    $id   = (int)$_GET['get_msg'];
    
    // Auto mark as read when viewed
    $db->prepare("UPDATE contact_messages SET status='read' WHERE id=? AND status='unread'")->execute([$id]);
    
    $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id=?");
    $stmt->execute([$id]);
    header('Content-Type: application/json');
    echo json_encode($stmt->fetch());
    exit;
}

// ---- TAMPIL DATA ----
$statusFilter = sanitize($_GET['filter'] ?? '');
$sql = "SELECT * FROM contact_messages";
$params = [];

if ($statusFilter) {
    $sql .= " WHERE status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

$totalUnread = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn();
if (isset($_GET['success'])) $success = sanitize($_GET['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesan Masuk — Admin Index Computer</title>
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
    
    .unread-row { background: rgba(59,130,246,0.03); }
    .unread-dot { width: 8px; height: 8px; background: var(--accent); border-radius: 50%; display: inline-block; margin-right: 8px; }
    
    .msg-detail-label { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .msg-detail-value { font-size: 15px; margin-bottom: 20px; color: var(--text); }
    .msg-content-box { background: var(--bg-2); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; line-height: 1.6; white-space: pre-wrap; font-size: 14px; }
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
      <a href="categories.php">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        Kategori
      </a>
      <a href="messages.php" class="active">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 0 2 2z"/></svg>
        Pesan <?php if ($totalUnread > 0): ?><span style="background:var(--accent-3);color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;margin-left:4px;"><?= $totalUnread ?></span><?php endif; ?>
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
    <div class="admin-header">
      <div>
        <div class="admin-title">Pesan Masuk</div>
        <div style="color:var(--text-muted);font-size:14px;margin-top:4px;">
          Kamu memiliki <strong style="color:var(--accent)"><?= $totalUnread ?></strong> pesan yang belum dibaca.
        </div>
      </div>
      <button id="adminThemeToggle" onclick="toggleAdminTheme()" title="Ganti tema" aria-label="Toggle light/dark mode"
        style="display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--border);color:var(--text);font-family:var(--font-body);font-size:13px;font-weight:600;padding:8px 16px;border-radius:24px;cursor:pointer;transition:var(--transition);flex-shrink:0;">
        <svg id="adminThemeIcon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
        <span id="adminThemeLabel">Light Mode</span>
      </button>
    </div>

    <?php if ($success): ?>
    <div class="page-alert success">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <div class="filter-bar" style="margin-bottom:20px;display:flex;gap:10px;">
      <a href="messages.php" class="btn-edit <?= !$statusFilter ? 'active' : '' ?>" style="padding:8px 16px;">Semua</a>
      <a href="messages.php?filter=unread" class="btn-edit <?= $statusFilter==='unread' ? 'active' : '' ?>" style="padding:8px 16px;">Belum Dibaca</a>
      <a href="messages.php?filter=read" class="btn-edit <?= $statusFilter==='read' ? 'active' : '' ?>" style="padding:8px 16px;">Dibaca</a>
    </div>

    <div class="admin-table-wrap">
      <table>
        <thead>
          <tr>
            <th>Pengirim</th>
            <th>Topik</th>
            <th>Pesan</th>
            <th>Waktu</th>
            <th style="width:160px;text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($messages as $msg): ?>
          <tr class="<?= $msg['status']==='unread' ? 'unread-row' : '' ?>">
            <td>
              <div style="font-weight:600;display:flex;align-items:center;">
                <?php if ($msg['status']==='unread'): ?><span class="unread-dot"></span><?php endif; ?>
                <?= htmlspecialchars($msg['name']) ?>
              </div>
              <div style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($msg['email'] ?: $msg['phone']) ?></div>
            </td>
            <td style="font-size:13px;"><?= htmlspecialchars($msg['subject'] ?: '-') ?></td>
            <td style="font-size:13px;color:var(--text-muted);max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              <?= htmlspecialchars($msg['message']) ?>
            </td>
            <td style="font-size:12px;color:var(--text-muted);"><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?></td>
            <td style="text-align:right;">
              <div class="table-actions" style="justify-content:flex-end;">
                <button class="btn-edit" onclick="viewMessage(<?= $msg['id'] ?>)">Baca</button>
                <button class="btn-delete" onclick="confirmDelete(<?= $msg['id'] ?>, '<?= htmlspecialchars(addslashes($msg['name'])) ?>')">Hapus</button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($messages)): ?>
          <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">Tidak ada pesan ditemukan.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<!-- Modal Detail Pesan -->
<div class="modal-overlay" id="modalView">
  <div class="modal" style="width:560px;">
    <div class="modal-title">Detail Pesan</div>
    <div style="padding:10px 0;">
      <div class="msg-detail-label">Dari</div>
      <div class="msg-detail-value"><strong id="msgName"></strong> (<span id="msgContact"></span>)</div>
      
      <div class="msg-detail-label">Subjek</div>
      <div class="msg-detail-value" id="msgSubject"></div>
      
      <div class="msg-detail-label">Isi Pesan</div>
      <div class="msg-content-box" id="msgContent"></div>
      
      <div style="font-size:12px;color:var(--text-muted);margin-top:16px;" id="msgTime"></div>
    </div>
    <div class="modal-actions">
      <button type="button" class="btn-cancel" onclick="closeModal('modalView'); location.reload();">Tutup</button>
      <a href="" id="btnToggleStatus" class="btn-edit" style="text-decoration:none;">Tandai Belum Dibaca</a>
    </div>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal-overlay" id="modalHapus">
  <div class="modal" style="width:400px;text-align:center;">
    <div style="font-size:40px;margin-bottom:10px;">🗑️</div>
    <div class="modal-title" style="justify-content:center;">Hapus Pesan?</div>
    <p style="font-size:14px;color:var(--text-muted);margin-bottom:20px;">Pesan dari <strong id="hapusNama" style="color:var(--text)"></strong> akan dihapus permanen.</p>
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

async function viewMessage(id) {
  try {
    const res = await fetch(`messages.php?get_msg=${id}`);
    const data = await res.json();
    
    document.getElementById('msgName').textContent = data.name;
    document.getElementById('msgContact').textContent = data.email || data.phone;
    document.getElementById('msgSubject').textContent = data.subject || 'Tanpa Subjek';
    document.getElementById('msgContent').textContent = data.message;
    document.getElementById('msgTime').textContent = 'Dikirim pada: ' + data.created_at;
    
    const toggleBtn = document.getElementById('btnToggleStatus');
    if (data.status === 'read') {
      toggleBtn.textContent = 'Tandai Belum Dibaca';
      toggleBtn.href = `messages.php?toggle_read=${data.id}&status=unread`;
      toggleBtn.style.display = 'inline-block';
    } else {
      toggleBtn.style.display = 'none';
    }
    
    openModal('modalView');
  } catch (err) { alert('Gagal mengambil data.'); }
}

function confirmDelete(id, name) {
  document.getElementById('hapusId').value = id;
  document.getElementById('hapusNama').textContent = name;
  openModal('modalHapus');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', e => { if(e.target === o) { closeModal(o.id); if(o.id === 'modalView') location.reload(); } });
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
