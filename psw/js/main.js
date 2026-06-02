// ============================================
// INDEX COMPUTER — Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- Theme label sync on load ----
  // toggleTheme() is defined globally in header.php inline script
  // Just sync the label text to match the active theme
  (function syncLabel() {
    var isLight = document.documentElement.getAttribute('data-theme') === 'light';
    var label = document.getElementById('themeLabel');
    if (label) label.textContent = isLight ? 'Dark Mode' : 'Light Mode';
  })();

  // ---- Navbar scroll effect ----
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 20);
    });
  }

  // ---- Mobile menu ----
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      mobileMenu.classList.toggle('open');
      const spans = hamburger.querySelectorAll('span');
      if (mobileMenu.classList.contains('open')) {
        spans[0].style.transform = 'translateY(7px) rotate(45deg)';
        spans[1].style.opacity = '0';
        spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
      } else {
        spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
      }
    });
  }

  // ---- Active nav link ----
  const page = window.location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.nav-links a, .mobile-menu a').forEach(link => {
    const href = link.getAttribute('href');
    if (href && href.includes(page)) link.classList.add('active');
    if (page === 'index.php' && href === 'index.php') link.classList.add('active');
  });

  // ---- Intersection Observer animations ----
  const animItems = document.querySelectorAll('.product-card, .cat-card, .solution-card, .service-card, .solution-page-card');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }, i * 60);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    animItems.forEach(item => {
      item.style.opacity = '0';
      item.style.transform = 'translateY(20px)';
      item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      observer.observe(item);
    });
  }

  // ---- FAQ accordion ----
  document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    });
  });

  // ---- Product filter (Products page) ----
  const filterItems = document.querySelectorAll('.filter-item[data-category]');
  filterItems.forEach(item => {
    item.addEventListener('click', () => {
      filterItems.forEach(f => f.classList.remove('active'));
      item.classList.add('active');
      const cat = item.dataset.category;
      filterProducts(cat);
    });
  });

  function filterProducts(category) {
    const cards = document.querySelectorAll('.product-card[data-category]');
    cards.forEach(card => {
      const show = category === 'all' || card.dataset.category === category;
      card.style.display = show ? '' : 'none';
    });
    updateCount();
  }

  function updateCount() {
    const visible = document.querySelectorAll('.product-card[data-category]:not([style*="none"])').length;
    const countEl = document.querySelector('.products-count span');
    if (countEl) countEl.textContent = visible;
  }

  // ---- Sort ----
  const sortSelect = document.querySelector('.sort-select');
  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      const grid = document.querySelector('.products-grid-main');
      if (!grid) return;
      const cards = [...grid.querySelectorAll('.product-card')];
      const val = sortSelect.value;
      cards.sort((a, b) => {
        const pa = parseFloat(a.dataset.price || 0);
        const pb = parseFloat(b.dataset.price || 0);
        const na = a.querySelector('.card-name')?.textContent || '';
        const nb = b.querySelector('.card-name')?.textContent || '';
        if (val === 'price-asc') return pa - pb;
        if (val === 'price-desc') return pb - pa;
        if (val === 'name-asc') return na.localeCompare(nb);
        return 0;
      });
      cards.forEach(c => grid.appendChild(c));
    });
  }

  // ---- Search ----
  const searchInput = document.querySelector('.nav-search input');
  if (searchInput) {
    searchInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter' && this.value.trim()) {
        window.location.href = `products.php?search=${encodeURIComponent(this.value.trim())}`;
      }
    });
  }

  // ---- Contact form ----
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      const btn = this.querySelector('.btn-submit');
      const alert = document.querySelector('.alert');
      btn.textContent = 'Mengirim...';
      btn.disabled = true;

      const formData = new FormData(this);
      try {
        const res = await fetch('php/contact.php', { method: 'POST', body: formData });
        const data = await res.json();
        alert.className = `alert ${data.success ? 'success' : 'error'}`;
        alert.textContent = data.message;
        alert.style.display = 'block';
        if (data.success) contactForm.reset();
      } catch (err) {
        alert.className = 'alert error';
        alert.textContent = 'Gagal mengirim pesan. Coba lagi.';
        alert.style.display = 'block';
      } finally {
        btn.textContent = 'Kirim Pesan →';
        btn.disabled = false;
        setTimeout(() => { alert.style.display = 'none'; }, 5000);
      }
    });
  }

  // ---- Admin modal ----
  window.openModal = function (id) {
    document.getElementById(id)?.classList.add('open');
  };
  window.closeModal = function (id) {
    document.getElementById(id)?.classList.remove('open');
  };
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function (e) {
      if (e.target === this) this.classList.remove('open');
    });
  });



});

// ---- Counter animation ----
function animateCount(el, target, duration = 1500) {
  let start = 0;
  const step = target / (duration / 16);
  const timer = setInterval(() => {
    start += step;
    if (start >= target) { el.textContent = target + (el.dataset.suffix || ''); clearInterval(timer); }
    else el.textContent = Math.floor(start) + (el.dataset.suffix || '');
  }, 16);
}
document.querySelectorAll('[data-count]').forEach(el => {
  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      animateCount(el, parseInt(el.dataset.count));
      observer.disconnect();
    }
  });
  observer.observe(el);
});