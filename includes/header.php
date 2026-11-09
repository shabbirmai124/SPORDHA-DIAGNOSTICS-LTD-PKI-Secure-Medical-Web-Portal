<?php
/**
 * Spordha Diagnostics Ltd. - Shared Header
 * Monochromatic Professional Theme
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?>Spordha Diagnostics Ltd. — PKI & Secure Communication Portal</title>

  <link rel="icon" type="image/svg+xml" href="assets1/favicon.svg">
  <meta name="theme-color" content="#09090b">

  <!-- Modern Clean Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- CSS Stylesheets -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  
  <meta name="description" content="Spordha Diagnostics Ltd. PKI-Secured Medical Diagnostic and Laboratory Portal. Two-tier CA, TLS 1.3, mTLS branch upload, and cryptographic report verification.">
</head>
<body>

  <!-- Top Security Infrastructure Telemetry Bar -->
  <aside class="security-top-banner">
    <div class="container">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-weight: 700; color: #ffffff; letter-spacing: 0.5px;">SECURITY STATUS:</span>
        <span>Isolated Lab (.spordha.test)</span>
      </div>
      <ul class="security-badges-list">
        <li><span class="sec-pill" title="Root CA key is kept offline and powered off except during key signing ceremonies"><span class="dot offline"></span> Root CA: Offline</span></li>
        <li><span class="sec-pill" title="Online Issuing CA validates all leaf certificate requests"><span class="dot"></span> Issuing CA: Online</span></li>
        <li><span class="sec-pill" title="Enforcing TLS 1.3 with AES-256-GCM and HSTS max-age=31536000"><span class="dot"></span> TLS 1.3 / HSTS</span></li>
        <li><span class="sec-pill" title="Mandatory mutual TLS (mTLS) client certificate authentication for branch uploads"><span class="dot"></span> Branch mTLS: Enforced</span></li>
      </ul>
    </div>
  </aside>

  <!-- Main Sticky Navigation Bar -->
  <header class="site-header">
    <div class="container">
      <nav class="navbar">
        <a href="index.php" class="brand-logo">
          <div class="brand-icon-box">S</div>
          <div class="brand-text">
            <h1>Spordha Diagnostics</h1>
            <span>PKI SECURE LABORATORY • DHAKA</span>
          </div>
        </a>

        <!-- Desktop Navigation Links -->
        <ul class="nav-links">
          <li><a href="index.php" class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a></li>
          <li><a href="about.php" class="nav-link <?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">About</a></li>
          <li><a href="services.php" class="nav-link <?php echo $currentPage === 'services.php' ? 'active' : ''; ?>">Services</a></li>
          <li><a href="verify.php" class="nav-link <?php echo $currentPage === 'verify.php' ? 'active' : ''; ?>">Verify Report</a></li>
          <li><a href="branches.php" class="nav-link <?php echo $currentPage === 'branches.php' ? 'active' : ''; ?>">Branches</a></li>
          <li><a href="pki_soc.php" class="nav-link <?php echo $currentPage === 'pki_soc.php' ? 'active' : ''; ?>">PKI & SOC</a></li>
          <li><a href="documentation.php" class="nav-link <?php echo $currentPage === 'documentation.php' ? 'active' : ''; ?>">Report</a></li>
          <li><a href="evidence_gallery.php" class="nav-link <?php echo $currentPage === 'evidence_gallery.php' ? 'active' : ''; ?>">Evidence</a></li>
          <li><a href="contact.php" class="nav-link <?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
        </ul>

        <!-- Right Action Buttons / User Menu -->
        <div class="nav-actions">
          <?php if ($user): ?>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <a href="dashboard.php" class="btn btn-secondary btn-sm">
                <span>Dashboard</span>
              </a>
              <?php echo getRoleBadge($user['role']); ?>
              <a href="logout.php" class="btn btn-outline-dark btn-sm" title="Log Out">
                Sign Out
              </a>
            </div>
          <?php else: ?>
            <a href="verify.php" class="btn btn-secondary btn-sm" style="display: none;" id="quickVerifyBtn">Quick Verify</a>
            <a href="register.php" class="btn btn-secondary btn-sm">
              <span>Register</span>
            </a>
            <a href="login.php" class="btn btn-primary btn-sm">
              <span>Sign In</span>
            </a>
          <?php endif; ?>

          <button class="mobile-toggle" aria-label="Toggle Navigation">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>
        </div>
      </nav>
    </div>
  </header>

  <main class="main-content">
