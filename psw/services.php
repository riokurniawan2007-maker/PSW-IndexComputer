<?php
require_once 'php/config.php';
$pageTitle = 'Layanan';
require_once 'php/header.php';
?>

<div class="page-hero">
  <div class="page-hero-content">
    <div class="section-tag">Apa yang Kami Tawarkan</div>
    <h1 class="page-title">Layanan <span>Kami</span></h1>
    <p class="page-subtitle">Tidak hanya jual produk — kami juga siap membantu dari servis, konsultasi, hingga pengiriman.</p>
  </div>
</div>

<section style="padding:60px 40px;">
  <div class="container">

    <!-- Main Services -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px;margin-bottom:60px;">
      <?php
      $services = [
        ['icon'=>'🔧','title'=>'Servis & Reparasi Komputer','desc'=>'Teknisi berpengalaman siap menangani kerusakan laptop dan PC, ringan hingga berat. Diagnosa awal gratis!','detail'=>['Ganti thermal paste','Upgrade RAM & SSD','Perbaikan motherboard','Reinstall OS & driver','Pembersihan debu']],
        ['icon'=>'🖥️','title'=>'Jasa Rakitan PC Custom','desc'=>'Kamu tentukan budget dan kebutuhan, kami pilihkan komponen terbaik dan rakit dengan rapi dan terorganisir.','detail'=>['Konsultasi gratis','Komponen original bergaransi','Rakitan rapi & terorganisir','Pengujian sebelum diserahkan','Garansi perakitan']],
        ['icon'=>'🚀','title'=>'Same-Day Delivery','desc'=>'Pesan sebelum jam 14.00 WIB, produk sampai di hari yang sama untuk area Batam.','detail'=>['Area pengiriman: seluruh Batam','Ongkir terjangkau','Produk dikemas aman','Update status pengiriman','COD tersedia']],
        ['icon'=>'📦','title'=>'Pre-Order','desc'=>'Produk yang kamu cari tidak tersedia di toko? Kami buka sistem pre-order untuk mendatangkan barang impian kamu.','detail'=>['DP minimal 50%','Estimasi waktu 3-14 hari kerja','Produk original','Konfirmasi via WhatsApp','Garansi resmi distributor']],
        ['icon'=>'🎟️','title'=>'Lucky Draw Berhadiah','desc'=>'Setiap pembelian minimal Rp200.000 berhak mendapatkan kupon lucky draw berhadiah menarik.','detail'=>['Berlaku setiap hari','Hadiah berupa voucher & aksesori','Tidak ada batas maksimal kupon','Undian dilakukan setiap bulan','Info lebih lanjut di toko']],
        ['icon'=>'💬','title'=>'Konsultasi Gratis','desc'=>'Bingung memilih produk? Tim kami siap memberikan rekomendasi jujur sesuai kebutuhan dan budget kamu.','detail'=>['Via WhatsApp / langsung ke toko','Rekomendasi jujur & transparan','Tidak dipaksa beli','Perbandingan spesifikasi','Saran upgrade terbaik']],
      ];
      foreach ($services as $svc):
      ?>
      <div class="service-card" style="text-align:left;padding:28px 24px;">
        <div style="font-size:40px;margin-bottom:16px;"><?= $svc['icon'] ?></div>
        <div style="font-family:var(--font-display);font-size:18px;font-weight:800;margin-bottom:8px;"><?= $svc['title'] ?></div>
        <div style="font-size:14px;color:var(--text-muted);line-height:1.6;margin-bottom:16px;"><?= $svc['desc'] ?></div>
        <ul style="display:flex;flex-direction:column;gap:6px;">
          <?php foreach ($svc['detail'] as $d): ?>
          <li style="font-size:13px;color:var(--text-muted);display:flex;align-items:center;gap:8px;">
            <span style="color:var(--accent);font-size:10px;">▶</span><?= $d ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- CTA Banner -->
    <div style="background:linear-gradient(135deg,rgba(59,130,246,0.08),rgba(6,182,212,0.08));border:1px solid rgba(59,130,246,0.15);border-radius:var(--radius-lg);padding:48px;text-align:center;">
      <div class="section-tag" style="justify-content:center;display:flex;">Mulai Sekarang</div>
      <h2 class="section-title" style="margin-top:8px;">Ada yang bisa <span>kami bantu?</span></h2>
      <p style="color:var(--text-muted);margin:12px auto 32px;max-width:500px;">Kunjungi toko kami di BCS Mall atau langsung hubungi via WhatsApp — kami siap melayani!</p>
      <div style="display:flex;justify-content:center;gap:16px;flex-wrap:wrap;">
        <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" class="btn-wa" style="font-size:16px;padding:14px 28px;">
          Chat WhatsApp
        </a>
        <a href="support.php" class="btn-secondary">
          Kirim Pesan
        </a>
      </div>
    </div>

  </div>
</section>

<?php require_once 'php/footer.php'; ?>