<?php
require_once 'php/config.php';
$pageTitle = 'Beranda';
$pageDesc  = 'Index Computer Batam – Toko komputer terpercaya di BCS Mall. Laptop, PC rakitan, aksesoris, dan layanan servis.';

$db = getDB();

// Featured products for Deals section
$featured = $db->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 AND p.is_active=1 LIMIT 4")->fetchAll();

// Categories with product count
$categories = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id=p.category_id AND p.is_active=1 GROUP BY c.id ORDER BY product_count DESC")->fetchAll();

// Solutions
$solutions = $db->query("SELECT * FROM solutions WHERE is_active=1 LIMIT 4")->fetchAll();

// Brands
$brands = $db->query("SELECT * FROM brands WHERE is_active=1")->fetchAll();

$catIcons = ['laptop'=>'💻','pc-komputer'=>'🖥️','aksesoris'=>'🖱️','hardware'=>'⚙️','printer'=>'🖨️','monitor'=>'📺','gaming'=>'🎮'];
$solutionIcons = [
    'gaming-setup' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="3" ry="3" /><line x1="6" y1="12" x2="10" y2="12" /><line x1="8" y1="10" x2="8" y2="14" /><circle cx="15.5" cy="12" r="1" fill="currentColor" /><circle cx="18.5" cy="12" r="1" fill="currentColor" /></svg>',
    'office-setup' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2" /><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" /></svg>',
    'design-editing' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M 11.5 3.5 C 16.5 3.5, 20.5 6.5, 20.5 11.5 C 20.5 15, 18 20, 15 20 C 13 20, 12 16.5, 10.5 16.5 C 9 16.5, 8 20, 5 20 C 2.5 20, 2.5 15, 2.5 11.5 C 2.5 6.5, 6.5 3.5, 11.5 3.5 Z" /><circle cx="14" cy="13" r="1.5" /><circle cx="7" cy="8" r="1.3" fill="currentColor" /><circle cx="12.5" cy="7.5" r="1.3" fill="currentColor" /><circle cx="6.5" cy="13.5" r="1.3" fill="currentColor" /><path d="M 16.5 7.5 L 15.5 4.5 C 15.5 3.8, 21.5 3.8, 21.5 4.5 L 20.5 7.5 Z" /><rect x="16.5" y="7.5" width="4" height="2" rx="0.5" /><path d="M 17.5 9.5 L 17.5 20.5 A 1 1 0 0 0 19.5 20.5 L 19.5 9.5 Z" /></svg>',
    'student-setup' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" /><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" /></svg>'
];

require_once 'php/header.php';
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-grid-bg"></div>
  <div class="hero-glow hero-glow-1"></div>
  <div class="hero-glow hero-glow-2"></div>

  <div class="hero-content">
    <div class="hero-text">
      <div class="hero-badge">
        <div class="dot"></div>
        Toko Komputer #1 di Batam
      </div>
      <h1 class="hero-title">
        Build Your<br>
        <span class="accent">Dream</span><br>
        PC Setup<span class="accent-2">.</span>
      </h1>
      <p class="hero-subtitle">
        Laptop, PC rakitan, aksesoris gaming, spare part lengkap — semua tersedia di Index Computer BCS Mall Batam.
      </p>
      <div class="hero-actions">
        <a href="products.php" class="btn-primary">
          Lihat Semua Produk
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="solutions.php" class="btn-secondary">
          Konsultasi Setup
        </a>
      </div>
      <div class="hero-stats">
        <div class="hero-stat">
          <div class="num" data-count="12000" data-suffix="+">0+</div>
          <div class="label">Produk Terjual</div>
        </div>
        <div class="hero-stat">
          <div class="num" data-count="4" data-suffix=".9★">4.9★</div>
          <div class="label">Rating Tokopedia</div>
        </div>
        <div class="hero-stat">
          <div class="num" data-count="10" data-suffix="+ Tahun">10+ Tahun</div>
          <div class="label">Pengalaman</div>
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="hero-card-main">
        <div class="placeholder-img">🖥️</div>
      </div>
      <div class="hero-floating-card card-1">
        <div class="floating-label">Garansi Resmi</div>
        <div class="floating-value green">✓ Distributor</div>
      </div>
      <div class="hero-floating-card card-2">
        <div class="floating-label">Lucky Draw</div>
        <div class="floating-value yellow">Min. Rp200rb</div>
      </div>
    </div>
  </div>
</section>

<!-- WHY US -->
<section style="background:var(--bg-2); padding:40px 40px;">
  <div class="container">
    <div class="services-row">
      <div class="service-card">
        <div class="service-icon">🏷️</div>
        <div class="service-title">Harga Kompetitif</div>
        <div class="service-text">Harga bersaing dengan produk original bergaransi resmi dari distributor.</div>
      </div>
      <div class="service-card">
        <div class="service-icon">🔧</div>
        <div class="service-title">Servis & Reparasi</div>
        <div class="service-text">Teknisi berpengalaman siap menangani kerusakan ringan hingga berat.</div>
      </div>
      <div class="service-card">
        <div class="service-icon">🚀</div>
        <div class="service-title">Same-Day Delivery</div>
        <div class="service-text">Pengiriman di hari yang sama untuk area Batam.</div>
      </div>
      <div class="service-card">
        <div class="service-icon">📦</div>
        <div class="service-title">Pre-Order</div>
        <div class="service-text">Barang tidak tersedia? Kami siap buka sistem pre-order untuk kamu.</div>
      </div>
    </div>
  </div>
</section>

<!-- DEALS OF THE WEEK -->
<section class="deals-section">
  <div class="container">
    <div class="section-header section-header-row">
      <div>
        <div class="section-tag">🔥 Penawaran Terbatas</div>
        <h2 class="section-title">Deals of the <span>Week</span></h2>
        <p class="section-subtitle">Produk pilihan dengan harga spesial, stok terbatas!</p>
      </div>
      <a href="products.php" class="view-all">
        Semua Produk
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>

    <div class="deals-grid">
      <?php foreach ($featured as $p): ?>
      <div class="product-card" onclick="window.location='products.php?id=<?= $p['id'] ?>'">
        <div class="card-image">
          <?php if ($p['image_url']): ?>
            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          <?php else: ?>
            <span><?= $catIcons[$p['cat_name'] ?? ''] ?? '📦' ?></span>
          <?php endif; ?>
          <span class="card-badge">HOT</span>
        </div>
        <div class="card-body">
          <div class="card-brand"><?= htmlspecialchars($p['brand'] ?? $p['cat_name']) ?></div>
          <div class="card-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="card-price"><?= formatRupiah($p['price']) ?></div>
          <div class="card-actions">
            <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Halo,%20saya%20tertarik%20dengan%20<?= urlencode($p['name']) ?>" target="_blank" class="btn-cart">
              <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.116 1.529 5.845L.057 23.486a.5.5 0 0 0 .611.61l5.579-1.463A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.887 0-3.663-.49-5.21-1.35l-.375-.215-3.875 1.016 1.035-3.78-.232-.388A9.961 9.961 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
              Tanya via WA
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align:center; margin-top:40px;">
      <a href="products.php" class="btn-secondary" style="display:inline-flex;">
        View More Products
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-left:8px"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="categories-section">
  <div class="container">
    <div class="section-header">
      <div class="section-tag">Kategori Produk</div>
      <h2 class="section-title">Apa yang Kamu <span>Cari?</span></h2>
    </div>
    <div class="categories-grid">
      <?php foreach ($categories as $cat): ?>
      <a href="products.php?cat=<?= urlencode($cat['slug']) ?>" class="cat-card">
        <div class="cat-icon"><?= $catIcons[$cat['slug']] ?? '📦' ?></div>
        <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
        <div class="cat-count"><?= $cat['product_count'] ?> produk</div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- SOLUTIONS -->
<section class="solutions-section">
  <div class="container">
    <div class="section-header">
      <div class="section-tag">Paket Setup PC</div>
      <h2 class="section-title">Solusi untuk <span>Semua Kebutuhan</span></h2>
      <p class="section-subtitle">Pilih setup yang sesuai dengan kebutuhanmu — dari gaming sampai kerja dan belajar.</p>
    </div>
    <div class="solutions-grid">
      <?php foreach ($solutions as $sol): ?>
      <a href="solutions.php#<?= htmlspecialchars($sol['slug']) ?>" class="solution-card">
        <div class="solution-icon-wrap"><?= $solutionIcons[$sol['slug']] ?? '💡' ?></div>
        <div class="solution-body">
          <div class="solution-name"><?= htmlspecialchars($sol['name']) ?></div>
          <div class="solution-desc"><?= htmlspecialchars($sol['description']) ?></div>
          <div class="btn-explore">
            Explore
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- BRANDS -->
<section class="brands-section">
  <div class="container">
    <div class="section-header" style="text-align:center;">
      <div class="section-tag">Partner Resmi</div>
      <h2 class="section-title">Brands We Work <span>With</span></h2>
    </div>
    <div class="brands-track-wrap">
      <div class="brands-track">
        <?php foreach (array_merge($brands, $brands) as $brand): ?>
        <div class="brand-item"><?= htmlspecialchars($brand['name']) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php require_once 'php/footer.php'; ?>