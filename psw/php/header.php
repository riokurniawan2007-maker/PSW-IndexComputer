<?php
// php/header.php — shared navbar + head
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="theme-color" content="#080c14" id="metaThemeColor">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <script>
    // Apply saved theme BEFORE paint to avoid flash + define global toggleTheme
    (function(){
      var saved = localStorage.getItem('theme');
      var prefer = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
      var theme = saved || prefer;
      if(theme === 'light') document.documentElement.setAttribute('data-theme','light');
    })();

    // Global function — available immediately, before any JS file loads
    function toggleTheme() {
      var html = document.documentElement;
      var isLight = html.getAttribute('data-theme') === 'light';
      if (isLight) {
        html.removeAttribute('data-theme');
        localStorage.setItem('theme', 'dark');
      } else {
        html.setAttribute('data-theme', 'light');
        localStorage.setItem('theme', 'light');
      }
      // Update mobile label
      var label = document.getElementById('themeLabel');
      if (label) label.textContent = isLight ? 'Light Mode' : 'Dark Mode';
      // Update meta theme-color
      var meta = document.getElementById('metaThemeColor');
      if (meta) meta.setAttribute('content', isLight ? '#080c14' : '#f0f4ff');
    }
  </script>
  <title><?= isset($pageTitle) ? $pageTitle . ' — ' . SITE_NAME : SITE_NAME . ' | Toko Komputer Batam' ?></title>
  <meta name="description" content="<?= isset($pageDesc) ? $pageDesc : 'Index Computer Batam – Toko komputer terpercaya di BCS Mall. Laptop, PC rakitan, aksesoris, spare part, dan servis komputer.' ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="css/style.css?v=2.1.1">
</head>
<body>

<nav class="navbar" id="navbar">
  <a href="index.php" class="nav-brand">
    <div class="brand-dot"></div>
    Index Computer
  </a>

  <div class="nav-links">
    <a href="index.php" <?= $currentPage==='index.php' ? 'class="active"' : '' ?>>Beranda</a>
    <a href="products.php" <?= $currentPage==='products.php' ? 'class="active"' : '' ?>>Produk</a>
    <a href="solutions.php" <?= $currentPage==='solutions.php' ? 'class="active"' : '' ?>>Solusi</a>
    <a href="services.php" <?= $currentPage==='services.php' ? 'class="active"' : '' ?>>Layanan</a>
    <a href="support.php" <?= $currentPage==='support.php' ? 'class="active"' : '' ?>>Kontak</a>
  </div>

  <div class="nav-right">
    <div class="nav-search">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <input type="text" placeholder="Cari produk...">
    </div>
    <!-- Theme Toggle -->
    <button class="theme-toggle" id="themeToggle" aria-label="Toggle light/dark mode" title="Ganti tema" onclick="toggleTheme()">
      <!-- Sun icon -->
      <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1" x2="12" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1" y1="12" x2="3" y2="12"/>
        <line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
      </svg>
      <!-- Moon icon -->
      <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
      </svg>
    </button>
    <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" class="btn-wa">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.116 1.529 5.845L.057 23.486a.5.5 0 0 0 .611.61l5.579-1.463A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.887 0-3.663-.49-5.21-1.35l-.375-.215-3.875 1.016 1.035-3.78-.232-.388A9.961 9.961 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
      WhatsApp
    </a>
    <div class="hamburger" id="hamburger">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <a href="index.php">Beranda</a>
  <a href="products.php">Produk</a>
  <a href="solutions.php">Solusi</a>
  <a href="services.php">Layanan</a>
  <a href="support.php">Kontak</a>
  <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" style="color:#25D366; font-weight:700;">
    💬 WhatsApp Kami
  </a>
  <div class="mobile-theme-row">
    <span>Tema</span>
    <button class="theme-pill" id="themeToggleMobile" aria-label="Toggle tema" onclick="toggleTheme()">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
      <span id="themeLabel">Light Mode</span>
    </button>
  </div>
</div>