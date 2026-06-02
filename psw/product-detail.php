<?php
require_once 'php/config.php';

$db = getDB();

// Get product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: products.php');
    exit;
}

// Fetch product
$stmt = $db->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.id=? AND p.is_active=1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = htmlspecialchars($product['name']);
$pageDesc  = mb_substr(strip_tags($product['description'] ?? ''), 0, 155);

// Related products (same category, exclude current)
$relStmt = $db->prepare("SELECT p.*, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.category_id=? AND p.id!=? AND p.is_active=1 ORDER BY p.is_featured DESC, p.created_at DESC LIMIT 4");
$relStmt->execute([$product['category_id'], $id]);
$related = $relStmt->fetchAll();

$catIcons = ['laptop'=>'💻','pc-komputer'=>'🖥️','aksesoris'=>'🖱️','hardware'=>'⚙️','printer'=>'🖨️','monitor'=>'📺','gaming'=>'🎮'];
$icon = $catIcons[$product['cat_slug']] ?? '📦';

$waText = urlencode("Halo, saya tertarik dengan produk: *{$product['name']}* seharga " . formatRupiah($product['price']) . ". Apakah stok masih tersedia?");

require_once 'php/header.php';
?>

<style>
/* ===== BREADCRUMB ===== */
.breadcrumb {
  padding: 20px 0 0;
  max-width: 1200px;
  margin: 0 auto;
  padding-left: 24px;
  padding-right: 24px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--text-muted);
  flex-wrap: wrap;
}
.breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color .2s; }
.breadcrumb a:hover { color: var(--accent); }
.breadcrumb .sep { opacity: .4; }
.breadcrumb .current { color: var(--text); font-weight: 500; }

/* ===== DETAIL LAYOUT ===== */
.detail-wrap {
  max-width: 1200px;
  margin: 24px auto 64px;
  padding: 0 24px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  align-items: start;
}
@media(max-width:860px){
  .detail-wrap{ grid-template-columns:1fr; gap:28px; }
  .detail-image-panel { position: static; }
}
@media(max-width:480px){
  .breadcrumb { font-size: 12px; overflow-wrap: break-word; word-break: break-word; }
  .detail-price { font-size: clamp(24px, 7vw, 34px); }
  .detail-name { font-size: clamp(20px, 5vw, 28px); }
  .detail-actions { flex-direction: column; }
  .btn-detail-wa, .btn-detail-back { width: 100%; min-height: 48px; }
  .related-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
}

/* ===== IMAGE PANEL ===== */
.detail-image-panel {
  position: sticky;
  top: 90px;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 380px;
  padding: 32px;
}
.detail-image-panel img {
  width: 100%;
  max-height: 420px;
  object-fit: contain;
  border-radius: 12px;
  transition: transform .4s ease;
}
.detail-image-panel img:hover { transform: scale(1.04); }
.detail-image-placeholder {
  font-size: 96px;
  filter: drop-shadow(0 8px 24px rgba(0,0,0,.3));
}
.badge-hot {
  position: absolute;
  top: 18px; left: 18px;
  background: linear-gradient(135deg, #ff4757, #ff6b81);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 1.2px;
  padding: 5px 12px;
  border-radius: 20px;
  box-shadow: 0 4px 12px rgba(255,71,87,.4);
}
.badge-stock-low {
  position: absolute;
  bottom: 18px; left: 18px;
  background: linear-gradient(135deg,#f39c12,#e67e22);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 20px;
}

/* ===== INFO PANEL ===== */
.detail-info { display: flex; flex-direction: column; gap: 20px; }

.detail-cat-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(99,102,241,.12);
  color: var(--accent);
  border: 1px solid rgba(99,102,241,.25);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: .5px;
  width: fit-content;
}

.detail-name {
  font-family: var(--font-display);
  font-size: clamp(22px, 3vw, 32px);
  font-weight: 800;
  line-height: 1.25;
  color: var(--text);
}

.detail-brand {
  font-size: 14px;
  color: var(--text-muted);
  display: flex;
  align-items: center;
  gap: 6px;
}
.detail-brand strong { color: var(--text); font-weight: 600; }

.detail-price-block {
  background: linear-gradient(135deg, rgba(99,102,241,.1), rgba(139,92,246,.08));
  border: 1px solid rgba(99,102,241,.2);
  border-radius: 16px;
  padding: 20px 24px;
}
.detail-price {
  font-family: var(--font-display);
  font-size: 34px;
  font-weight: 800;
  color: var(--accent);
  line-height: 1;
}
.detail-price-note {
  font-size: 12px;
  color: var(--text-muted);
  margin-top: 6px;
}

.detail-stock {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: var(--text-muted);
}
.stock-dot {
  width: 10px; height: 10px;
  border-radius: 50%;
}
.stock-dot.in  { background: #2ecc71; box-shadow: 0 0 8px rgba(46,204,113,.5); }
.stock-dot.low { background: #f39c12; box-shadow: 0 0 8px rgba(243,156,18,.5); }
.stock-dot.out { background: #e74c3c; box-shadow: 0 0 8px rgba(231,76,60,.5); }

.detail-desc {
  font-size: 14px;
  line-height: 1.8;
  color: var(--text-muted);
  border-top: 1px solid var(--border);
  padding-top: 20px;
}
.detail-desc p { margin: 0; }

/* ===== SPECS TABLE ===== */
.specs-block { border-top: 1px solid var(--border); padding-top: 20px; }
.specs-title {
  font-family: var(--font-display);
  font-size: 15px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.specs-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.specs-table tr:nth-child(odd) td { background: rgba(255,255,255,.02); }
.specs-table td {
  padding: 9px 12px;
  border-bottom: 1px solid var(--border);
  vertical-align: top;
}
.specs-table td:first-child {
  color: var(--text-muted);
  width: 38%;
  font-weight: 500;
}
.specs-table td:last-child { color: var(--text); }

/* ===== CTA BUTTONS ===== */
.detail-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.btn-detail-wa {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  background: linear-gradient(135deg, #25D366, #128C7E);
  color: #fff;
  font-weight: 700;
  font-size: 15px;
  padding: 16px 24px;
  border-radius: 14px;
  text-decoration: none;
  transition: all .3s;
  box-shadow: 0 8px 24px rgba(37,211,102,.3);
  min-width: 180px;
}
.btn-detail-wa:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(37,211,102,.45);
}
.btn-detail-back {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: var(--card);
  border: 1px solid var(--border);
  color: var(--text);
  font-weight: 600;
  font-size: 14px;
  padding: 16px 22px;
  border-radius: 14px;
  text-decoration: none;
  transition: all .3s;
}
.btn-detail-back:hover {
  background: var(--border);
  transform: translateY(-2px);
}

/* ===== RELATED ===== */
.related-section {
  max-width: 1200px;
  margin: 0 auto 64px;
  padding: 0 24px;
}
.related-title {
  font-family: var(--font-display);
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.related-title::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}
.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 20px;
}
.rel-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  transition: all .3s;
  display: block;
}
.rel-card:hover {
  border-color: var(--accent);
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(99,102,241,.15);
}
.rel-card-img {
  height: 160px;
  background: rgba(255,255,255,.03);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.rel-card-img img { width: 100%; height: 100%; object-fit: contain; padding: 12px; }
.rel-card-img span { font-size: 48px; }
.rel-card-body { padding: 14px 16px; }
.rel-card-name { font-size: 13px; font-weight: 600; line-height: 1.4; margin-bottom: 6px; color: var(--text); }
.rel-card-price { font-size: 14px; font-weight: 700; color: var(--accent); }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb">
  <a href="index.php">Beranda</a>
  <span class="sep">›</span>
  <a href="products.php">Produk</a>
  <span class="sep">›</span>
  <a href="products.php?cat=<?= urlencode($product['cat_slug']) ?>"><?= htmlspecialchars($product['cat_name']) ?></a>
  <span class="sep">›</span>
  <span class="current"><?= htmlspecialchars($product['name']) ?></span>
</div>

<!-- Detail -->
<div class="detail-wrap">

  <!-- Image -->
  <div class="detail-image-panel" style="position:relative;">
    <?php if ($product['image_url']): ?>
      <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    <?php else: ?>
      <div class="detail-image-placeholder"><?= $icon ?></div>
    <?php endif; ?>

    <?php if ($product['is_featured']): ?>
      <div class="badge-hot">⚡ FEATURED</div>
    <?php endif; ?>

    <?php if ($product['stock'] > 0 && $product['stock'] <= 3): ?>
      <div class="badge-stock-low">Sisa <?= $product['stock'] ?> unit!</div>
    <?php endif; ?>
  </div>

  <!-- Info -->
  <div class="detail-info">
    <!-- Category tag -->
    <div class="detail-cat-tag">
      <?= $icon ?> <?= htmlspecialchars($product['cat_name']) ?>
    </div>

    <!-- Name -->
    <h1 class="detail-name"><?= htmlspecialchars($product['name']) ?></h1>

    <!-- Brand -->
    <?php if ($product['brand']): ?>
    <div class="detail-brand">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
      Brand: <strong><?= htmlspecialchars($product['brand']) ?></strong>
    </div>
    <?php endif; ?>

    <!-- Price -->
    <div class="detail-price-block">
      <div class="detail-price"><?= formatRupiah($product['price']) ?></div>
      <div class="detail-price-note">Harga belum termasuk ongkir · Bisa nego</div>
    </div>

    <!-- Stock -->
    <div class="detail-stock">
      <?php if ($product['stock'] > 3): ?>
        <span class="stock-dot in"></span>
        <span>Stok tersedia <strong style="color:var(--text);">(<?= $product['stock'] ?> unit)</strong></span>
      <?php elseif ($product['stock'] > 0): ?>
        <span class="stock-dot low"></span>
        <span>Stok hampir habis — sisa <strong style="color:#f39c12;"><?= $product['stock'] ?> unit</strong></span>
      <?php else: ?>
        <span class="stock-dot out"></span>
        <span style="color:#e74c3c;">Stok habis — hubungi kami untuk pre-order</span>
      <?php endif; ?>
    </div>

    <!-- Description -->
    <?php if ($product['description']): ?>
    <div class="detail-desc">
      <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
    </div>
    <?php endif; ?>

    <!-- Specs (from specs field if exists, otherwise basic info) -->
    <div class="specs-block">
      <div class="specs-title">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
        Informasi Produk
      </div>
      <table class="specs-table">
        <tr>
          <td>Nama Produk</td>
          <td><?= htmlspecialchars($product['name']) ?></td>
        </tr>
        <?php if ($product['brand']): ?>
        <tr>
          <td>Brand / Merek</td>
          <td><?= htmlspecialchars($product['brand']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
          <td>Kategori</td>
          <td><?= htmlspecialchars($product['cat_name']) ?></td>
        </tr>
        <tr>
          <td>Harga</td>
          <td><strong style="color:var(--accent);"><?= formatRupiah($product['price']) ?></strong></td>
        </tr>
        <tr>
          <td>Ketersediaan</td>
          <td>
            <?php if ($product['stock'] > 0): ?>
              <span style="color:#2ecc71;">✓ Tersedia</span> (<?= $product['stock'] ?> unit)
            <?php else: ?>
              <span style="color:#e74c3c;">✗ Habis</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php if (isset($product['specifications']) && $product['specifications']): ?>
        <?php
          // Try JSON specs first, fallback to plain text
          $specs = json_decode($product['specifications'], true);
          if (is_array($specs)) {
            foreach ($specs as $key => $val) {
              echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars($val) . "</td></tr>";
            }
          } else {
            // Plain text specs — display as single row
            echo "<tr><td>Spesifikasi</td><td>" . nl2br(htmlspecialchars($product['specifications'])) . "</td></tr>";
          }
        ?>
        <?php endif; ?>
      </table>
    </div>

    <!-- CTA Actions -->
    <div class="detail-actions">
      <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=<?= $waText ?>"
         target="_blank" class="btn-detail-wa" id="btn-order-wa">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.116 1.529 5.845L.057 23.486a.5.5 0 0 0 .611.61l5.579-1.463A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.887 0-3.663-.49-5.21-1.35l-.375-.215-3.875 1.016 1.035-3.78-.232-.388A9.961 9.961 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        Pesan via WhatsApp
      </a>
      <a href="products.php?cat=<?= urlencode($product['cat_slug']) ?>" class="btn-detail-back" id="btn-back-products">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
        Kembali
      </a>
    </div>

  </div><!-- /detail-info -->
</div><!-- /detail-wrap -->

<!-- Related Products -->
<?php if (!empty($related)): ?>
<section class="related-section">
  <div class="related-title">Produk Serupa</div>
  <div class="related-grid">
    <?php foreach ($related as $r): ?>
    <a href="product-detail.php?id=<?= $r['id'] ?>" class="rel-card">
      <div class="rel-card-img">
        <?php if ($r['image_url']): ?>
          <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="<?= htmlspecialchars($r['name']) ?>" loading="lazy">
        <?php else: ?>
          <span><?= $catIcons[$r['cat_slug']] ?? '📦' ?></span>
        <?php endif; ?>
      </div>
      <div class="rel-card-body">
        <div class="rel-card-name"><?= htmlspecialchars($r['name']) ?></div>
        <div class="rel-card-price"><?= formatRupiah($r['price']) ?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php require_once 'php/footer.php'; ?>
