<?php
require_once 'php/config.php';
$pageTitle = 'Solusi PC Setup';

$db = getDB();
$solutions = $db->query("SELECT * FROM solutions WHERE is_active=1")->fetchAll();

$solutionIcons = [
    'gaming-setup'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="4"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="15" cy="11" r="1" fill="currentColor" stroke="none"/><circle cx="18" cy="13" r="1" fill="currentColor" stroke="none"/></svg>',
    'office-setup'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
    'design-editing' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>',
    'student-setup'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>'
];
require_once 'php/header.php';
?>

<div class="page-hero">
  <div class="page-hero-content">
    <div class="section-tag">Paket PC Setup</div>
    <h1 class="page-title"><span>Solutions</span> untuk Semua</h1>
    <p class="page-subtitle">Kami bantu kamu memilih setup PC yang tepat sesuai kebutuhan dan budget.</p>
  </div>
</div>

<section style="padding:60px 40px;">
  <div class="container">
    <div class="solutions-page-grid">
      <?php foreach ($solutions as $sol): 
        $icon  = $solutionIcons[$sol['slug']] ?? '💡';
      ?>
      <div class="solution-page-card" id="<?= htmlspecialchars($sol['slug']) ?>">
        <div class="solution-img">
          <?= $icon ?>
        </div>
        <div class="solution-info">
          <div class="solution-tag-label">Untuk <?= htmlspecialchars($sol['target']) ?></div>
          <div class="solution-page-name"><?= htmlspecialchars($sol['name']) ?></div>
          <div class="solution-page-desc"><?= htmlspecialchars($sol['description']) ?></div>
        </div>
        <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Halo,%20saya%20tertarik%20dengan%20<?= urlencode($sol['name']) ?>.%20Bisa%20bantu%20konsultasi?" target="_blank" class="btn-wa" style="white-space:nowrap;flex-shrink:0;">
          Konsultasi
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Info Section -->
    <div class="solutions-info-grid">
      <div>
        <div class="section-tag">Cara Kerja</div>
        <h2 class="section-title solutions-info-title">Konsultasi <span>Gratis</span></h2>
        <p style="color:var(--text-muted);margin-top:12px;line-height:1.7;">Tim kami siap membantu kamu memilih komponen yang tepat sesuai budget dan kebutuhan. Tidak perlu khawatir — kami jelaskan detail setiap produk.</p>
        <div style="margin-top:28px;display:flex;flex-direction:column;gap:16px;">
          <div style="display:flex;gap:14px;align-items:flex-start;">
            <div style="width:36px;height:36px;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-weight:800;color:var(--accent);flex-shrink:0;">1</div>
            <div><div style="font-family:var(--font-display);font-weight:700;margin-bottom:4px;">Pilih Kategori Setup</div><div style="font-size:14px;color:var(--text-muted);">Tentukan kebutuhan: gaming, office, desain, atau pelajar.</div></div>
          </div>
          <div style="display:flex;gap:14px;align-items:flex-start;">
            <div style="width:36px;height:36px;background:rgba(6,182,212,0.1);border:1px solid rgba(6,182,212,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-weight:800;color:var(--accent-2);flex-shrink:0;">2</div>
            <div><div style="font-family:var(--font-display);font-weight:700;margin-bottom:4px;">Konsultasi via WhatsApp</div><div style="font-size:14px;color:var(--text-muted);">Tim kami rekomendasikan komponen terbaik sesuai budget.</div></div>
          </div>
          <div style="display:flex;gap:14px;align-items:flex-start;">
            <div style="width:36px;height:36px;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-weight:800;color:var(--accent);flex-shrink:0;">3</div>
            <div><div style="font-family:var(--font-display);font-weight:700;margin-bottom:4px;">Ambil atau Dikirim</div><div style="font-size:14px;color:var(--text-muted);">Bisa langsung ke toko atau kami kirim same-day delivery.</div></div>
          </div>
        </div>
      </div>
      <div>
        <div class="section-tag">PC Rakitan Kustom</div>
        <h2 class="section-title solutions-info-title">Rakit PC <span>Sendiri</span></h2>
        <p style="color:var(--text-muted);margin-top:12px;line-height:1.7;">Ingin PC sesuai spec impian? Kami melayani jasa PC rakitan kustom — kamu tentukan budget dan kebutuhan, kami pilihkan komponen terbaik dan rakit untuk kamu.</p>
        <div style="margin-top:24px;display:flex;flex-wrap:wrap;gap:8px;">
          <?php foreach (['Intel & AMD', 'RTX Series', 'DDR5 Ready', 'Custom RGB', 'Water Cooling', 'Budget Friendly'] as $tag): ?>
          <span class="chip"><?= $tag ?></span>
          <?php endforeach; ?>
        </div>
        <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Halo,%20saya%20ingin%20konsultasi%20PC%20rakitan%20kustom" target="_blank" class="btn-primary" style="display:inline-flex;margin-top:24px;">
          Mulai Konsultasi
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once 'php/footer.php'; ?>
