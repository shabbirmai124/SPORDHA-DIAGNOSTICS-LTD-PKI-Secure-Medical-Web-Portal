<?php
/**
 * Spordha Diagnostics Ltd. - Diagnostic Branch Network & mTLS Status
 * Central Laboratory (HQ Dhaka) & 3 Sample Collection Branches
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDBConnection();
$branches = $db->query("SELECT * FROM branches ORDER BY id ASC")->fetchAll();

$pageTitle = "Laboratory Branches & mTLS Endpoints";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <span class="section-badge">Physical & Cryptographic Topology</span>
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">
      Laboratory Branches & mTLS Endpoints
    </h1>
    <p style="color: var(--color-gray-dark); margin-top: 0.25rem;">
      Central processing laboratory in Dhaka supported by three dedicated sample collection branches with unique client certificates.
    </p>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <div class="cards-grid-2">
    <?php foreach ($branches as $b): ?>
      <div class="card-item" style="border-top: 4px solid #000;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
          <div>
            <span class="badge badge-warning-notice"><?php echo htmlspecialchars($b['code']); ?></span>
            <h3 style="font-size: 1.35rem; font-weight: 800; margin-top: 0.35rem;">
              <?php echo htmlspecialchars($b['name']); ?>
            </h3>
          </div>
          <div>
            <span class="badge badge-valid">mTLS ACTIVE</span>
          </div>
        </div>

        <p style="font-size: 0.875rem; color: var(--color-gray-dark); line-height: 1.6; margin-bottom: 1.25rem;">
          <strong>Location:</strong> <?php echo htmlspecialchars($b['location']); ?><br>
          <strong>Helpline:</strong> <?php echo htmlspecialchars($b['phone']); ?><br>
          <strong>Hours:</strong> 7:30 AM – 10:30 PM (Emergency 24/7 Rapid Diagnostics)
        </p>

        <!-- Cryptographic Device Credentials -->
        <div style="background: var(--color-gray-subtle); padding: 1rem; border-radius: var(--radius-md); font-size: 0.8rem; margin-bottom: 1.25rem; border: 1px solid var(--color-gray-border); line-height: 1.6;">
          <div><strong>X.509 Client Cert Serial:</strong> <code style="font-family: var(--font-mono);"><?php echo htmlspecialchars($b['client_cert_serial']); ?></code></div>
          <div><strong>mTLS Terminal IP:</strong> <code style="font-family: var(--font-mono);"><?php echo htmlspecialchars($b['ip_address']); ?></code></div>
          <div><strong>Extended Key Usage:</strong> <code>clientAuth (1.3.6.1.5.5.7.3.2)</code></div>
          <div><strong>Intermittent Link Spool:</strong> Offline Encrypted SQLite Local Buffer</div>
        </div>

        <div style="display: flex; gap: 0.5rem; margin-top: auto;">
          <?php if ($b['id'] > 1): ?>
            <a href="branch_upload.php" class="btn btn-primary btn-sm" style="flex: 1;">
              Simulate mTLS Upload from this Branch →
            </a>
          <?php else: ?>
            <a href="pki_soc.php" class="btn btn-secondary btn-sm" style="flex: 1;">
              Central PKI Tier Console
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
