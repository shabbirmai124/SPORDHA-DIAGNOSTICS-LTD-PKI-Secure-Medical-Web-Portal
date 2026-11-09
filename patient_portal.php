<?php
/**
 * Spordha Diagnostics Ltd. - Patient Portal
 * Confidential Medical Records & Digitally Signed Test Results
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// If not logged in, prompt to log in or login as demo patient
if (!isLoggedIn()) {
    header("Location: login.php?msg=" . urlencode("Please log in to access the Patient Portal."));
    exit;
}

$user = currentUser();
$db = getDBConnection();

// Fetch reports for this patient (or all reports if admin/tech evaluating)
if ($user['role'] === 'patient') {
    $stmt = $db->prepare("SELECT r.*, b.name as branch_name, doc.name as doctor_name, patho.name as pathologist_name 
        FROM reports r 
        LEFT JOIN branches b ON r.branch_id = b.id 
        LEFT JOIN users doc ON r.doctor_id = doc.id 
        LEFT JOIN users patho ON r.pathologist_id = patho.id 
        WHERE r.patient_id = ? 
        ORDER BY r.id DESC");
    $stmt->execute([$user['id']]);
} else {
    // If doctor/admin visits, show demo patient (Tanvir Ahmed, id=11)
    $stmt = $db->prepare("SELECT r.*, b.name as branch_name, doc.name as doctor_name, patho.name as pathologist_name 
        FROM reports r 
        LEFT JOIN branches b ON r.branch_id = b.id 
        LEFT JOIN users doc ON r.doctor_id = doc.id 
        LEFT JOIN users patho ON r.pathologist_id = patho.id 
        WHERE r.patient_id = 11 
        ORDER BY r.id DESC");
    $stmt->execute();
}
$reports = $stmt->fetchAll();

$pageTitle = "Patient Portal | Medical Records & Reports";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <span class="section-badge">Confidential Health Records</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">
          Patient Diagnostic Portal
        </h1>
        <p style="color: var(--color-gray-dark); margin-top: 0.25rem;">
          Welcome, <strong><?php echo htmlspecialchars($user['name']); ?></strong> (Patient ID: <code>P-10024</code>). All clinical documents are digitally signed and cryptographically verifiable.
        </p>
      </div>
      <div>
        <a href="verify.php" class="btn btn-primary">
          <span>Verify Any Report Signature</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">
  
  <!-- Security Notice Card -->
  <div style="background-color: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 2.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="width: 44px; height: 44px; background: #000; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        🔒
      </div>
      <div>
        <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.2rem;">Data Protection Guarantee (PDPA 2026)</h4>
        <p style="font-size: 0.8125rem; color: var(--color-gray-mid);">
          Your medical reports are encrypted at rest using AES-256 and transmitted over TLS 1.3. Authentic reports contain an embedded SHA-256 digital signature from the certifying pathologist.
        </p>
      </div>
    </div>
    <span class="badge badge-valid">TLS 1.3 Encrypted</span>
  </div>

  <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.25rem;">Diagnostic Test History</h3>

  <div class="cards-grid-2">
    <?php foreach ($reports as $r): ?>
      <div class="card-item" style="border-left: 4px solid <?php echo $r['is_tampered'] ? '#ef4444' : '#000000'; ?>;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
          <div>
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--color-gray-mid); text-transform: uppercase;">
              <?php echo htmlspecialchars($r['test_category']); ?>
            </span>
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-top: 0.2rem;">
              <?php echo htmlspecialchars($r['test_name']); ?>
            </h3>
            <span style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--color-gray-dark);">
              UID: <?php echo htmlspecialchars($r['report_uid']); ?>
            </span>
          </div>

          <div>
            <?php if ($r['is_tampered']): ?>
              <span class="badge badge-tampered">TAMPER DETECTED</span>
            <?php else: ?>
              <span class="badge badge-valid">DIGITALLY SIGNED</span>
            <?php endif; ?>
          </div>
        </div>

        <div style="background-color: var(--color-gray-subtle); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem; font-size: 0.875rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
            <span style="color: var(--color-gray-mid);">Specimen:</span>
            <strong><?php echo htmlspecialchars($r['specimen']); ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
            <span style="color: var(--color-gray-mid);">Pathologist:</span>
            <strong><?php echo htmlspecialchars($r['pathologist_name'] ?: 'Dr. Nazmul Huda'); ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
            <span style="color: var(--color-gray-mid);">Result Summary:</span>
            <strong style="color: <?php echo $r['is_tampered'] ? '#b91c1c' : '#000'; ?>;">
              <?php echo htmlspecialchars($r['result_summary']); ?>
            </strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--color-gray-mid);">Signed At:</span>
            <span><?php echo formatDateTime($r['signed_at']); ?></span>
          </div>
        </div>

        <!-- Cryptographic Details Block -->
        <div style="margin-bottom: 1.25rem;">
          <div style="font-size: 0.75rem; font-weight: 700; color: var(--color-gray-mid); margin-bottom: 0.3rem;">
            DIGITAL SIGNATURE DIGEST (SHA-256):
          </div>
          <code style="font-size: 0.75rem; background: #0c0d0e; color: #4ade80; padding: 0.4rem 0.6rem; border-radius: 4px; display: block; word-break: break-all;">
            <?php echo htmlspecialchars($r['signature_hash']); ?>
          </code>
        </div>

        <div style="display: flex; gap: 0.75rem; margin-top: auto;">
          <a href="generate_report.php?uid=<?php echo urlencode($r['report_uid']); ?>" target="_blank" class="btn btn-primary" style="flex: 1;">
            📄 Download / Print PDF Report
          </a>
          <a href="verify.php?uid=<?php echo urlencode($r['report_uid']); ?>" class="btn btn-secondary">
            🔍 Verify
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
