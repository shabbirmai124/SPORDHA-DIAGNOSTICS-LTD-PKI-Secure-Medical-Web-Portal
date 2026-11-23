// Md Shabbir Mia
function showSpordhaToast(message, isError) {
  const toast = document.getElementById('spordhaToast');
  if (!toast) return;
  toast.textContent = message;
  toast.style.background = isError ? '#991b1b' : '#09090b';
  toast.style.borderColor = isError ? '#ef4444' : '#27272a';
  toast.style.display = 'block';
  clearTimeout(window.__spordhaToastTimer);
  window.__spordhaToastTimer = setTimeout(function () {
    toast.style.display = 'none';
  }, 3500);
}

function activateTabById(tabId) {
  const btn = document.querySelector('.tab-btn[data-tab="' + tabId + '"]');
  if (!btn) return;
  const tabGroup = btn.closest('.tab-container') || document;
  tabGroup.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  tabGroup.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  const targetPane = tabGroup.querySelector('#' + tabId);
  if (targetPane) {
    targetPane.classList.add('active');
  }
}

document.addEventListener('DOMContentLoaded', function () {
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

  const tabButtons = document.querySelectorAll('.tab-btn');
  tabButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.getAttribute('data-tab');
      activateTabById(targetId);
    });
  });

  const hashTabMap = {
    '#ca_tree': 'tab-ca-tree',
    '#tab-ca-tree': 'tab-ca-tree',
    '#tab-inventory': 'tab-inventory',
    '#expiry_sec': 'tab-expiry',
    '#tab-expiry': 'tab-expiry',
    '#incident_sec': 'tab-ai-threat',
    '#tab-ai-threat': 'tab-ai-threat',
    '#tab-test-matrix': 'tab-test-matrix'
  };
  if (window.location.hash && hashTabMap[window.location.hash]) {
    activateTabById(hashTabMap[window.location.hash]);
  }
  window.addEventListener('hashchange', function () {
    if (window.location.hash && hashTabMap[window.location.hash]) {
      activateTabById(hashTabMap[window.location.hash]);
    }
  });

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

  const copyButtons = document.querySelectorAll('.copy-btn');
  copyButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      const targetText = this.getAttribute('data-copy') || this.innerText;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(targetText).then(() => {
          const originalText = this.innerHTML;
          this.innerHTML = '✓ Copied';
          this.classList.add('btn-copied');
          setTimeout(() => {
            this.innerHTML = originalText;
            this.classList.remove('btn-copied');
          }, 2000);
        });
      }
    });
  });

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

function toggleCertRevocation(certId, action) {
  fetch('api/revoke_cert.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ cert_id: certId, action: action })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      showSpordhaToast(data.message, false);
      setTimeout(function () {
        window.location.reload();
      }, 650);
    } else {
      showSpordhaToast(data.message || 'Revocation request failed.', true);
    }
  })
  .catch(err => {
    showSpordhaToast('Network error connecting to PKI Directory: ' + err, true);
  });
}

function setVerifyQuery(code) {
  const input = document.getElementById('reportSearchInput');
  if (input) {
    input.value = code;
    input.focus();
  }
}
