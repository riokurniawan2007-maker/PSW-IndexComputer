<?php
require_once 'php/config.php';
$pageTitle = 'Produk';

$db = getDB();

// Filters
$search      = isset($_GET['search'])    ? trim($_GET['search'])    : '';
$catSlug     = isset($_GET['cat'])       ? trim($_GET['cat'])       : '';
$sort        = isset($_GET['sort'])      ? trim($_GET['sort'])      : 'default';
$minPrice    = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice    = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;

// Pagination
$perPage     = 12;
$pageNum     = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($pageNum - 1) * $perPage;

// Build WHERE
$where  = ['p.is_active=1'];
$params = [];
if ($search) {
    $where[]  = '(p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)';
    $params   = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}
if ($catSlug) {
    $where[]  = 'c.slug = ?';
    $params[] = $catSlug;
}
if ($minPrice > 0) {
    $where[]  = 'p.price >= ?';
    $params[] = $minPrice;
}
if ($maxPrice > 0) {
    $where[]  = 'p.price <= ?';
    $params[] = $maxPrice;
}
$whereSQL = implode(' AND ', $where);
$orderBy  = match($sort) {
    'price-asc'  => 'p.price ASC',
    'price-desc' => 'p.price DESC',
    'name-asc'   => 'p.name ASC',
    default      => 'p.is_featured DESC, p.created_at DESC'
};

// Total count 窶・pakai salinan $params tersendiri
$countParams = $params;
$countStmt   = $db->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id=c.id WHERE $whereSQL");
$countStmt->execute($countParams);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages    = max(1, (int)ceil($totalProducts / $perPage));

// Produk halaman ini
$mainSQL = "SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE $whereSQL ORDER BY $orderBy LIMIT ? OFFSET ?";
$stmt = $db->prepare($mainSQL);

// Bind params satu per satu agar bisa atur tipe data
$idx = 1;
foreach ($params as $p) {
    $stmt->bindValue($idx++, $p);
}
$stmt->bindValue($idx++, (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue($idx++, (int)$offset,  PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Categories for sidebar
$categories = $db->query("SELECT c.*, COUNT(p.id) as cnt FROM categories c LEFT JOIN products p ON c.id=p.category_id AND p.is_active=1 GROUP BY c.id ORDER BY cnt DESC")->fetchAll();

// Price range — sesuaikan dengan filter aktif (kategori/search), BUKAN harga filter itu sendiri
$rangeWhere  = ['p.is_active=1'];
$rangeParams = [];
if ($search) {
    $rangeWhere[]  = '(p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)';
    $rangeParams   = array_merge($rangeParams, ["%$search%", "%$search%", "%$search%"]);
}
if ($catSlug) {
    $rangeWhere[]  = 'c.slug = ?';
    $rangeParams[] = $catSlug;
}
$rangeSQL  = "SELECT MIN(p.price) as min_p, MAX(p.price) as max_p FROM products p JOIN categories c ON p.category_id=c.id WHERE " . implode(' AND ', $rangeWhere);
$rangeStmt = $db->prepare($rangeSQL);
$rangeStmt->execute($rangeParams);
$priceRange = $rangeStmt->fetch();
$globalMin  = (int)($priceRange['min_p'] ?? 0);
$globalMax  = (int)($priceRange['max_p'] ?? 100000000);
// Jika slider min/max melebihi batas range yang baru, reset agar tidak error
if ($minPrice < $globalMin) $minPrice = 0;
if ($maxPrice > $globalMax)  $maxPrice = 0;

// Warna dot per kategori untuk sidebar
$catColors = [
    'laptop'      => '#3b82f6',
    'pc-komputer' => '#8b5cf6',
    'aksesoris'   => '#06b6d4',
    'hardware'    => '#f59e0b',
    'printer'     => '#10b981',
    'monitor'     => '#ec4899',
    'gaming'      => '#ef4444',
];
// Ikon fallback produk tanpa gambar (PHP unicode escape — aman lintas encoding)
$catIcons = [
    'laptop'      => "\u{1F4BB}",
    'pc-komputer' => "\u{1F4BB}",
    'aksesoris'   => "\u{1F5B1}",
    'hardware'    => "\u{1F527}",
    'printer'     => "\u{1F4C4}",
    'monitor'     => "\u{1F4FA}",
    'gaming'      => "\u{1F3AE}",
];

// Helper URL pagination
function pageUrl($page) {
    $p = $_GET;
    if ($page <= 1) {
        unset($p['page']);
    } else {
        $p['page'] = $page;
    }
    $qs = http_build_query($p);
    return 'products.php' . ($qs ? '?' . $qs : '');
}

require_once 'php/header.php';
?>

<div class="page-hero">
  <div class="page-hero-content">
    <div class="section-tag">Katalog Produk</div>
    <h1 class="page-title">Upgrade Your Setup <span>Today</span></h1>
    <p class="page-subtitle">
      <?= $search ? "Hasil pencarian untuk: <strong>" . htmlspecialchars($search) . "</strong>" : 'Temukan laptop, PC, aksesoris, dan hardware terlengkap.' ?>
    </p>
  </div>
</div>

<div class="products-layout">
  <!-- SIDEBAR KIRI: Kategori -->
  <aside class="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-title">Kategori</div>
      <div class="filter-item <?= !$catSlug ? 'active' : '' ?>" onclick="window.location='products.php<?= $search ? '?search='.urlencode($search) : '' ?>'">
        <span class="cat-label">
          <span class="cat-dot" style="background:linear-gradient(135deg,var(--accent),var(--accent-2));"></span>
          Semua Produk
        </span>
        <span class="filter-count"><?= array_sum(array_column($categories, 'cnt')) ?></span>
      </div>
      <?php foreach ($categories as $cat):
        $dotColor = $catColors[$cat['slug']] ?? '#64748b';
      ?>
      <div class="filter-item <?= $catSlug === $cat['slug'] ? 'active' : '' ?>"
           onclick="window.location='products.php?cat=<?= urlencode($cat['slug']) ?><?= $search ? '&search='.urlencode($search) : '' ?>'">
        <span class="cat-label">
          <span class="cat-dot" style="background:<?= $dotColor ?>;"></span>
          <?= htmlspecialchars($cat['name']) ?>
        </span>
        <span class="filter-count"><?= $cat['cnt'] ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-title">Butuh Bantuan?</div>
      <p style="font-size:13px;color:var(--text-muted);margin-bottom:14px;line-height:1.6;">Tidak yakin produk mana yang cocok? Tim kami siap membantu.</p>
      <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" class="btn-wa" style="width:100%;justify-content:center;">
        Chat via WhatsApp
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="products-main">
    <!-- Toolbar -->
    <div class="products-toolbar">
      <div class="products-count">
        <?php if ($totalProducts > 0): ?>
          Menampilkan <span><?= $offset + 1 ?>-<?= min($offset + $perPage, $totalProducts) ?></span> dari <span><?= $totalProducts ?></span> produk
          <?= $catSlug ? ' &middot; <strong>' . htmlspecialchars($catSlug) . '</strong>' : '' ?>
        <?php else: ?>
          <span>0</span> produk ditemukan
        <?php endif; ?>
      </div>
      <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <?php if ($search): ?>
        <a href="products.php<?= $catSlug ? '?cat='.urlencode($catSlug) : '' ?>" class="chip">&#x2715; "<?= htmlspecialchars($search) ?>"</a>
        <?php endif; ?>
        <?php if ($minPrice > 0 || $maxPrice > 0): ?>
        <a href="products.php<?= $catSlug ? '?cat='.urlencode($catSlug) : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>" class="chip">&#x2715; Filter Harga</a>
        <?php endif; ?>
        <form method="GET" style="display:flex;gap:8px;" id="searchSortForm">
          <?php if ($catSlug): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($catSlug) ?>"><?php endif; ?>

          <?php if ($minPrice > 0): ?><input type="hidden" name="min_price" value="<?= $minPrice ?>"><?php endif; ?>
          <?php if ($maxPrice > 0): ?><input type="hidden" name="max_price" value="<?= $maxPrice ?>"><?php endif; ?>
          <div class="nav-search" style="margin:0;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>" style="width:160px;">
          </div>
          <select name="sort" class="sort-select" onchange="this.form.submit()">
            <option value="default"    <?= $sort==='default'    ?'selected':'' ?>>Terbaru</option>
            <option value="price-asc"  <?= $sort==='price-asc'  ?'selected':'' ?>>Harga Terendah</option>
            <option value="price-desc" <?= $sort==='price-desc' ?'selected':'' ?>>Harga Tertinggi</option>
            <option value="name-asc"   <?= $sort==='name-asc'   ?'selected':'' ?>>Nama A-Z</option>
          </select>
        </form>
      </div>
    </div>

    <!-- Grid -->
    <?php if (empty($products)): ?>
    <div style="text-align:center;padding:80px 20px;color:var(--text-muted);">
      <div style="font-size:48px;margin-bottom:16px;">&#x1F50D;</div>
      <div style="font-family:var(--font-display);font-size:20px;font-weight:700;margin-bottom:8px;">Produk tidak ditemukan</div>
      <div style="font-size:14px;">Coba kata kunci lain atau <a href="products.php" style="color:var(--accent);">lihat semua produk</a></div>
    </div>
    <?php else: ?>
    <div class="products-grid-main">
      <?php foreach ($products as $p): ?>
      <div class="product-card" data-category="<?= htmlspecialchars($p['cat_slug']) ?>" data-price="<?= $p['price'] ?>">
        <a href="product-detail.php?id=<?= $p['id'] ?>" class="card-image" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">
          <?php if ($p['image_url']): ?>
            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
          <?php else: ?>
            <span style="font-size:44px;"><?= $catIcons[$p['cat_slug']] ?? "\u{1F4E6}" ?></span>
          <?php endif; ?>
          <?php if ($p['is_featured']): ?><span class="card-badge">HOT</span><?php endif; ?>
          <?php if ($p['stock'] <= 3 && $p['stock'] > 0): ?>
          <span class="card-badge" style="top:auto;bottom:10px;left:10px;background:var(--accent-3);">Stok <?= $p['stock'] ?></span>
          <?php endif; ?>
        </a>
        <div class="card-body">
          <div class="card-brand"><?= htmlspecialchars($p['brand'] ?? $p['cat_name']) ?></div>
          <div class="card-name"><?= htmlspecialchars($p['name']) ?></div>
          <?php if ($p['description']): ?>
          <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;line-height:1.4;"><?= htmlspecialchars(mb_substr($p['description'], 0, 60)) ?>...</div>
          <?php endif; ?>
          <div class="card-price"><?= formatRupiah($p['price']) ?></div>
          <div class="card-actions">
            <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Halo,%20saya%20tertarik%20dengan%20produk:%20<?= urlencode($p['name']) ?>%20seharga%20<?= urlencode(formatRupiah($p['price'])) ?>" target="_blank" class="btn-cart">
              <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.116 1.529 5.845L.057 23.486a.5.5 0 0 0 .611.61l5.579-1.463A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.887 0-3.663-.49-5.21-1.35l-.375-.215-3.875 1.016 1.035-3.78-.232-.388A9.961 9.961 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
              Tanya via WA
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-wrap">
      <?php if ($pageNum > 1): ?>
      <a href="<?= pageUrl($pageNum - 1) ?>" class="page-btn page-arrow" title="Halaman Sebelumnya">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
        <span class="btn-text" style="margin-left:6px;font-size:12px;">Prev</span>
      </a>
      <?php endif; ?>

      <?php
      $range = 2;
      $start = max(1, $pageNum - $range);
      $end   = min($totalPages, $pageNum + $range);
      if ($start > 1): ?>
        <a href="<?= pageUrl(1) ?>" class="page-btn">1</a>
        <?php if ($start > 2): ?><span class="page-dots">...</span><?php endif; ?>
      <?php endif; ?>

      <?php for ($i = $start; $i <= $end; $i++): ?>
      <a href="<?= pageUrl($i) ?>" class="page-btn <?= $i === $pageNum ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?><span class="page-dots">...</span><?php endif; ?>
        <a href="<?= pageUrl($totalPages) ?>" class="page-btn"><?= $totalPages ?></a>
      <?php endif; ?>

      <?php if ($pageNum < $totalPages): ?>
      <a href="<?= pageUrl($pageNum + 1) ?>" class="page-btn page-arrow" title="Halaman Berikutnya">
        <span class="btn-text" style="margin-right:6px;font-size:12px;">Next</span>
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <?php endif; ?>
    </div>
    <div style="text-align:center;padding-bottom:8px;">
      <span style="font-size:13px;color:var(--text-muted);">Halaman <strong style="color:var(--text);"><?= $pageNum ?></strong> dari <strong style="color:var(--text);"><?= $totalPages ?></strong></span>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>

  <!-- SIDEBAR KANAN: Filter Harga + Promo + CTA -->
  <aside class="sidebar-right">

    <!-- Filter Harga -->
    <div class="sidebar-section">
      <div class="sidebar-title">Filter Harga</div>
      <form method="GET" id="priceFilterForm">
        <?php if ($catSlug): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($catSlug) ?>"><?php endif; ?>
        <?php if ($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
        <?php if ($sort !== 'default'): ?><input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>"><?php endif; ?>

        <div class="price-range-wrap">
          <div class="price-range-track">
            <div class="price-range-fill" id="rangeFill"></div>
            <input type="range" id="rangeMin" name="min_price" min="<?= $globalMin ?>" max="<?= $globalMax ?>"
                   value="<?= $minPrice ?: $globalMin ?>" step="100000" oninput="updateRange()">
            <input type="range" id="rangeMax" name="max_price" min="<?= $globalMin ?>" max="<?= $globalMax ?>"
                   value="<?= $maxPrice ?: $globalMax ?>" step="100000" oninput="updateRange()">
          </div>
          <div class="price-labels">
            <span id="labelMin"><?= $minPrice > 0 ? formatRupiah($minPrice) : formatRupiah($globalMin) ?></span>
            <span id="labelMax"><?= $maxPrice > 0 ? formatRupiah($maxPrice) : formatRupiah($globalMax) ?></span>
          </div>
        </div>
        <button type="submit" class="btn-filter-apply" id="applyPriceBtn">Terapkan Filter</button>
        <?php if ($minPrice > 0 || $maxPrice > 0): ?>
        <a href="products.php<?= $catSlug ? '?cat='.urlencode($catSlug) : '' ?><?= $search ? ($catSlug ? '&' : '?').'search='.urlencode($search) : '' ?>" class="btn-filter-reset">Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Promo CTA -->
    <div class="sidebar-promo">
      <div class="sidebar-promo-icon">&#x1F39F;</div>
      <div class="sidebar-promo-title">Lucky Draw!</div>
      <div class="sidebar-promo-desc">Beli min. Rp200rb, dapat kupon lucky draw berhadiah menarik setiap bulan.</div>
      <a href="support.php" class="sidebar-promo-link">Pelajari lebih lanjut &rarr;</a>
    </div>

    <!-- Servis CTA -->
    <div class="sidebar-section" style="text-align:center;">
      <div style="font-size:28px;margin-bottom:8px;">&#x1F527;</div>
      <div style="font-family:var(--font-display);font-size:14px;font-weight:700;margin-bottom:6px;">Servis & Reparasi</div>
      <div style="font-size:12px;color:var(--text-muted);line-height:1.6;margin-bottom:12px;">Diagnosa awal gratis. Teknisi berpengalaman siap membantu.</div>
      <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Halo,%20saya%20ingin%20konsultasi%20servis%20laptop/PC" target="_blank" class="btn-wa" style="width:100%;justify-content:center;font-size:13px;padding:10px;">
        Konsultasi Servis
      </a>
    </div>

  </aside>
</div>

<script>
// ---- Price range slider ----
const gMin = <?= $globalMin ?>;
const gMax = <?= $globalMax ?>;

function formatRp(val) {
  if (val >= 1000000) return 'Rp' + (val / 1000000).toFixed(val % 1000000 === 0 ? 0 : 1) + ' Jt';
  if (val >= 1000) return 'Rp' + Math.round(val / 1000) + ' Rb';
  return 'Rp' + val;
}

function updateRange() {
  const minR = document.getElementById('rangeMin');
  const maxR = document.getElementById('rangeMax');
  let minV = parseInt(minR.value);
  let maxV = parseInt(maxR.value);

  // Prevent crossing
  if (minV > maxV - 500000) {
    if (document.activeElement === minR) minV = maxV - 500000;
    else maxV = minV + 500000;
    minR.value = minV;
    maxR.value = maxV;
  }

  const pct = (v) => ((v - gMin) / (gMax - gMin)) * 100;
  document.getElementById('rangeFill').style.left  = pct(minV) + '%';
  document.getElementById('rangeFill').style.right = (100 - pct(maxV)) + '%';
  document.getElementById('labelMin').textContent = formatRp(minV);
  document.getElementById('labelMax').textContent = formatRp(maxV);
}

// Init on load
document.addEventListener('DOMContentLoaded', updateRange);
</script>

<?php require_once 'php/footer.php'; ?>
