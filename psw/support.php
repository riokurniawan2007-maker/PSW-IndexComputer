<?php
require_once 'php/config.php';
$pageTitle = 'Kontak & Support';
require_once 'php/header.php';
?>

<div class="page-hero">
  <div class="page-hero-content">
    <div class="section-tag">Hubungi Kami</div>
    <h1 class="page-title">Ada <span>Pertanyaan?</span></h1>
    <p class="page-subtitle">Tim kami siap membantu kamu — dari informasi produk hingga layanan servis.</p>
  </div>
</div>

<section style="padding:60px 40px;">
  <div class="container">
    <div class="contact-layout">

      <!-- Left: Info & FAQ -->
      <div class="contact-info">
        <div class="contact-info-card">
          <h3>📍 Lokasi Toko</h3>
          <div class="info-row">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span><?= SITE_ADDRESS ?></span>
          </div>
          <div class="info-row">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span><?= SITE_HOURS ?></span>
          </div>
          <a href="https://maps.google.com/?q=BCS+Mall+Batam" target="_blank" class="btn-secondary" style="display:inline-flex;margin-top:12px;font-size:13px;padding:10px 16px;">
            Buka Google Maps
          </a>
        </div>

        <div class="contact-info-card">
          <h3>📞 Kontak Langsung</h3>
          <div class="info-row">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8 9.91a16 16 0 0 0 6 6l.86-.86a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <a href="tel:<?= SITE_PHONE ?>" style="color:var(--accent-2);"><?= SITE_PHONE ?></a>
          </div>
          <div class="info-row">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <a href="mailto:<?= SITE_EMAIL ?>" style="color:var(--accent-2);"><?= SITE_EMAIL ?></a>
          </div>
          <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" class="btn-wa" style="display:inline-flex;margin-top:12px;">
            WhatsApp Sekarang
          </a>
        </div>

        <div class="contact-info-card">
          <h3>🙋 FAQ</h3>
          <?php
          $faqs = [
            ['q'=>'Apakah ada garansi untuk produk yang dibeli?','a'=>'Ya! Semua produk yang kami jual memiliki garansi resmi dari distributor sesuai ketentuan masing-masing brand.'],
            ['q'=>'Apakah bisa pesan produk yang tidak tersedia di toko?','a'=>'Bisa! Kami menyediakan sistem pre-order. Hubungi kami via WhatsApp untuk info lebih lanjut.'],
            ['q'=>'Berapa lama proses servis laptop/PC?','a'=>'Tergantung tingkat kerusakan. Untuk kerusakan ringan biasanya 1-3 hari kerja. Tim kami akan menginformasikan estimasi waktu setelah diagnosa.'],
            ['q'=>'Apakah ada same-day delivery?','a'=>'Ada! Untuk area Batam, pesan sebelum pukul 14.00 WIB dan produk akan dikirim di hari yang sama.'],
            ['q'=>'Bagaimana cara bayar?','a'=>'Bisa transfer bank (BCA, BRI, Mandiri) atau bayar langsung di toko. COD juga tersedia untuk pengiriman.'],
          ];
          foreach ($faqs as $faq):
          ?>
          <div class="faq-item">
            <div class="faq-question">
              <?= $faq['q'] ?>
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="faq-answer"><?= $faq['a'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Right: Contact Form -->
      <div class="contact-form-wrap">
        <div class="form-title">Kirim Pesan 💬</div>
        <div class="alert" id="formAlert"></div>
        <form id="contactForm" novalidate>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Nama Lengkap *</label>
              <input type="text" name="name" class="form-input" placeholder="Nama kamu" required>
            </div>
            <div class="form-group">
              <label class="form-label">No. WhatsApp</label>
              <input type="tel" name="phone" class="form-input" placeholder="08xxxxxxxxxx">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" placeholder="email@kamu.com">
          </div>
          <div class="form-group">
            <label class="form-label">Topik</label>
            <select name="subject" class="form-select">
              <option value="">Pilih topik...</option>
              <option value="Info Produk">Info Produk</option>
              <option value="Harga & Penawaran">Harga & Penawaran</option>
              <option value="Servis & Reparasi">Servis & Reparasi</option>
              <option value="Pre-Order">Pre-Order</option>
              <option value="Pengiriman">Pengiriman</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Pesan *</label>
            <textarea name="message" class="form-textarea" placeholder="Tulis pesanmu di sini..." required></textarea>
          </div>
          <button type="submit" class="btn-submit">Kirim Pesan →</button>
        </form>

        <div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--border);text-align:center;">
          <div style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">Atau langsung chat via:</div>
          <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" class="btn-wa" style="display:inline-flex;justify-content:center;width:100%;">
            WhatsApp — Balas lebih cepat!
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'php/footer.php'; ?>