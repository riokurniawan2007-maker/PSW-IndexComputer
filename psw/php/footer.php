<?php // php/footer.php — shared footer ?>

<footer>
  <div class="footer-grid">
    <div>
      <div class="footer-brand-name">
        <div class="brand-dot" style="width:10px;height:10px;background:var(--accent);border-radius:50%;box-shadow:0 0 10px var(--accent);"></div>
        Index Computer
      </div>
      <p class="footer-desc">Toko komputer terpercaya di Batam. Menyediakan laptop, PC rakitan, aksesoris, hardware, printer, dan layanan servis berkualitas.</p>
      <div class="footer-contact-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <span><?= SITE_ADDRESS ?></span>
      </div>
      <div class="footer-contact-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8 9.91a16 16 0 0 0 6 6l.86-.86a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span><?= SITE_PHONE ?></span>
      </div>
      <div class="footer-contact-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span><?= SITE_HOURS ?></span>
      </div>
      <div class="footer-social-links">
        <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" class="social-icon-btn whatsapp" title="WhatsApp">
          <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.116 1.529 5.845L.057 23.486a.5.5 0 0 0 .611.61l5.579-1.463A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.887 0-3.663-.49-5.21-1.35l-.375-.215-3.875 1.016 1.035-3.78-.232-.388A9.961 9.961 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        </a>
        <a href="<?= SITE_TOKOPEDIA ?>" target="_blank" class="social-icon-btn tokopedia" title="Tokopedia">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        </a>
        <a href="<?= SITE_INSTAGRAM ?>" target="_blank" class="social-icon-btn instagram" title="Instagram">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
        </a>
        <a href="<?= SITE_FACEBOOK ?>" target="_blank" class="social-icon-btn facebook" title="Facebook">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
        </a>
      </div>
    </div>

    <div>
      <div class="footer-col-title">Navigasi</div>
      <div class="footer-links">
        <a href="index.php">Beranda</a>
        <a href="products.php">Semua Produk</a>
        <a href="solutions.php">Solusi PC</a>
        <a href="services.php">Layanan</a>
        <a href="support.php">Kontak & Support</a>
      </div>
    </div>

    <div>
      <div class="footer-col-title">Kategori</div>
      <div class="footer-links">
        <a href="products.php?cat=laptop">Laptop</a>
        <a href="products.php?cat=pc-komputer">PC & Komputer</a>
        <a href="products.php?cat=aksesoris">Aksesoris</a>
        <a href="products.php?cat=hardware">Hardware & Spare Part</a>
        <a href="products.php?cat=printer">Printer & Tinta</a>
        <a href="products.php?cat=gaming">Gaming</a>
      </div>
    </div>

    <div>
      <div class="footer-col-title">Official Payments</div>
      <div class="payment-list">
        <div class="payment-item">
          <div class="payment-bank">BCA</div>
          <div class="payment-num">8325 1978 65</div>
        </div>
        <div class="payment-item">
          <div class="payment-bank">BRI</div>
          <div class="payment-num">0331 0155 7788 207</div>
        </div>
        <div class="payment-item">
          <div class="payment-bank">Mandiri</div>
          <div class="payment-num">1090 0017 72567</div>
        </div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:8px;">A.N PT Sentral Index Komputindo</div>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <span>Copyright &copy; 2026 Index Computer. All Rights Reserved.</span>
    <span>
      <a href="products.php">Produk</a> &nbsp;·&nbsp;
      <a href="support.php">Kontak</a> &nbsp;·&nbsp;
      <a href="php/admin/index.php">Admin</a>
    </span>
  </div>
</footer>
<link rel="stylesheet" href="css/assistant.css?v=2.0.9">

<!-- AI Chatbot Floating Widget -->
<button class="chatbot-trigger" id="chatbotTrigger" title="Tanya AI Assistant">
  <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
  </svg>
</button>

<div class="chatbot-window" id="chatbotWindow">
  <div class="chatbot-header">
    <div class="chatbot-avatar">🤖</div>
    <div class="chatbot-info">
      <div class="chatbot-title">Index AI Assistant</div>
      <div class="chatbot-status">Online</div>
    </div>
    <button class="chatbot-close" id="chatbotClose" title="Tutup Chat">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>
  
  <div class="chatbot-body" id="chatbotBody">
    <!-- Bot Welcome Message -->
    <div class="chat-msg bot">
      <div class="chatbot-avatar">🤖</div>
      <div class="msg-bubble">
        Halo! Saya <strong>Index AI Assistant</strong>. Ada yang bisa saya bantu hari ini? 💻<br><br>
        Berikut beberapa hal yang bisa Anda tanyakan:
      </div>
    </div>
    
    <!-- Quick Options -->
    <div class="chatbot-quick">
      <button class="quick-btn" onclick="triggerQuickOption(this)">📍 Lokasi Toko & Jam Buka</button>
      <button class="quick-btn" onclick="triggerQuickOption(this)">💻 Daftar Harga Laptop</button>
      <button class="quick-btn" onclick="triggerQuickOption(this)">🛠️ Jasa Rakit PC Custom</button>
      <button class="quick-btn" onclick="triggerQuickOption(this)">📞 Nomor WhatsApp Toko</button>
    </div>
  </div>
  
  <div class="chatbot-footer">
    <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Tulis pesan..." autocomplete="off">
    <button class="chatbot-send" id="chatbotSend" title="Kirim Pesan">
      <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
      </svg>
    </button>
  </div>
</div>

<script src="js/assistant.js"></script>
<script src="js/main.js?v=2.0.2"></script>
</body>
</html>