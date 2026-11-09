/**
 * Spordha Diagnostics Ltd. - Core Client-Side Interactions
 * Monochromatic UI Scripts & Security Demonstrations
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Menu Toggle
  const mobileToggle = document.querySelector('.mobile-toggle');
  const navLinks = document.querySelector('.nav-links');

  if (mobileToggle && navLinks) {
    mobileToggle.addEventListener('click', function () {
      const isVisible = navLinks.style.display === 'flex';
      navLinks.style.display = isVisible ? 'none' : 'flex';
      if (!isVisible) {
        navLinks.style.flexDirection = 'column';
        navLinks.style.position = 'absolute';
        navLinks.style.top = '100%';
        navLinks.style.left = '0';
        navLinks.style.width = '100%';
        navLinks.style.backgroundColor = '#ffffff';
        navLinks.style.padding = '1.5rem';
        navLinks.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.1)';
        navLinks.style.borderBottom = '1px solid #e4e4e7';
      }
    });
  }

  // 2. Interactive Tabs System (PKI SOC, Documentation, Portals)
  const tabButtons = document.querySelectorAll('.tab-btn');
  tabButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.getAttribute('data-tab');
      const tabGroup = this.closest('.tab-container') || document;

      tabGroup.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      tabGroup.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

      this.classList.add('active');
      const targetPane = tabGroup.querySelector('#' + targetId);
      if (targetPane) {
        targetPane.classList.add('active');
      }
    });
  });

  // 3. Modals
  const modalOpeners = document.querySelectorAll('[data-modal-target]');
  modalOpeners.forEach(btn => {
    btn.addEventListener('click', function () {
      const modalId = this.getAttribute('data-modal-target');
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('active');
      }
    });
  });

  const modalClosers = document.querySelectorAll('.modal-close-btn, .modal-overlay');
  modalClosers.forEach(closer => {
    closer.addEventListener('click', function (e) {
      if (e.target === this || this.classList.contains('modal-close-btn')) {
        const modal = this.closest('.modal-overlay');
        if (modal) {
          modal.classList.remove('active');
        }
      }
    });
  });

  // 4. Copy to Clipboard Utility
  const copyButtons = document.querySelectorAll('.copy-btn');
  copyButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const targetText = this.getAttribute('data-copy') || this.innerText;
      navigator.clipboard.writeText(targetText).then(() => {
        const originalText = this.innerHTML;
        this.innerHTML = '✓ Copied';
        this.classList.add('btn-copied');
        setTimeout(() => {
          this.innerHTML = originalText;
          this.classList.remove('btn-copied');
        }, 2000);
      });
    });
  });

  // 5. Live Table Filtering
  const filterInputs = document.querySelectorAll('.table-search-input');
  filterInputs.forEach(input => {
    input.addEventListener('keyup', function () {
      const filterValue = this.value.toLowerCase();
      const targetTable = document.querySelector(this.getAttribute('data-target-table'));
      if (!targetTable) return;

      const rows = targetTable.querySelectorAll('tbody tr');
      rows.forEach(row => {
        const rowText = row.innerText.toLowerCase();
        row.style.display = rowText.includes(filterValue) ? '' : 'none';
      });
    });
  });

  // 6. Back-to-top button
  const backToTop = document.querySelector('.back-to-top');
  if (backToTop) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 400) {
        backToTop.classList.add('visible');
      } else {
        backToTop.classList.remove('visible');
      }
    });
    backToTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // 7. Documentation scroll-spy (highlight active TOC link)
  const tocLinks = document.querySelectorAll('.doc-nav-link[href^="#"]');
  if (tocLinks.length > 0) {
    const sections = [];
    tocLinks.forEach(link => {
      const id = link.getAttribute('href').slice(1);
      const el = document.getElementById(id);
      if (el) sections.push({ id, el, link });
    });

    const onScroll = function () {
      let currentId = sections.length ? sections[0].id : null;
      for (const s of sections) {
        if (s.el.getBoundingClientRect().top <= 140) {
          currentId = s.id;
        }
      }
      tocLinks.forEach(link => {
        const id = link.getAttribute('href').slice(1);
        link.classList.toggle('active', id === currentId);
      });
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // 8. Animated stat counters
  const counters = document.querySelectorAll('[data-count]');
  counters.forEach(el => {
    const target = parseInt(el.getAttribute('data-count'), 10) || 0;
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 1600;
    let startTime = null;
    function step(ts) {
      if (!startTime) startTime = ts;
      const progress = Math.min((ts - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(target * eased).toLocaleString('en-US') + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  });

  // 9. Scroll-reveal animation
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length > 0) {
    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
      revealEls.forEach(el => io.observe(el));
    } else {
      revealEls.forEach(el => el.classList.add('in-view'));
    }
  }
});

/**
 * Live Certificate Revocation AJAX Handler
 * @param {number} certId 
 * @param {string} action 'revoke' | 'reinstate'
 */
function toggleCertRevocation(certId, action) {
  if (!confirm(`Are you sure you want to ${action.toUpperCase()} this certificate in the Spordha CA Directory? This immediately updates the CRL and OCSP responder status.`)) {
    return;
  }

  fetch('api/revoke_cert.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ cert_id: certId, action: action })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert(`Success: ${data.message}`);
      window.location.reload();
    } else {
      alert(`Error: ${data.message}`);
    }
  })
  .catch(err => {
    alert('Network error connecting to PKI Directory: ' + err);
  });
}

/**
 * Fill Report Search in Hero from Sample Code Pill
 */
function setVerifyQuery(code) {
  const input = document.getElementById('reportSearchInput');
  if (input) {
    input.value = code;
    input.focus();
  }
}
