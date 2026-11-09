<?php
/**
 * Spordha Diagnostics Ltd. - Referring Doctor Portal
 * Clinical Case Review & Direct Pathology Verification
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php?msg=" . urlencode("Please sign in with your Doctor credentials."));
    exit;
}

$user = currentUser();
$db = getDBConnection();

// Fetch referred patients' reports
$stmt = $db->query("SELECT r.*, u.name as patient_name, u.phone as patient_phone, u.nid as patient_nid, b.name as branch_name, patho.name as pathologist_name 
    FROM reports r 
    JOIN users u ON r.patient_id = u.id 
    LEFT JOIN branches b ON r.branch_id = b.id 
    LEFT JOIN users patho ON r.pathologist_id = patho.id 
    ORDER BY r.id DESC");
$reports = $stmt->fetchAll();

$pageTitle = "Referring Doctor Portal | Clinical Diagnostic Review";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <span class="section-badge">Physician Consultation Console</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">
          Referring Doctor Portal
        </h1>
        <p style="color: var(--color-gray-dark); margin-top: 0.25rem;">
          Welcome, <strong><?php echo htmlspecialchars($user['name']); ?></strong> (<?php echo htmlspecialchars($user['designation'] ?: 'Consultant Physician'); ?>).
        </p>
      </div>
      <div>
        <a href="verify.php" class="btn btn-outline-dark">
          <span>Batch Report Verifier</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <!-- Physician Guide Box -->
  <div style="background-color: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2.5rem;">
    <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">
      🩺 Physician Verification Protocol for Forwarded Reports
    </h3>
    <p style="font-size: 0.875rem; color: var(--color-gray-dark); line-height: 1.6; margin-bottom: 1rem;">
      Patients frequently forward PDF test results via WhatsApp, email, or printouts. To protect clinical decisions from forged or edited results (such as fraudulent visa or employment medicals), verify reports using one of two methods:
    </p>
    <div class="cards-grid-3">
      <div style="background: var(--color-gray-subtle); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-gray-border);">
        <strong style="display: block; font-size: 0.85rem; margin-bottom: 0.25rem;">1. Visual PAdES Badge</strong>
        <span style="font-size: 0.8rem; color: var(--color-gray-mid);">Open report in Adobe Acrobat or PDF viewer; verify "All signatures are valid" with Spordha Issuing CA anchor.</span>
      </div>
      <div style="background: var(--color-gray-subtle); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-gray-border);">
        <strong style="display: block; font-size: 0.85rem; margin-bottom: 0.25rem;">2. Instant Portal Lookup</strong>
        <span style="font-size: 0.8rem; color: var(--color-gray-mid);">Enter the Report UID printed at top-right into our public verification portal to check live authenticity.</span>
      </div>
      <div style="background: var(--color-gray-subtle); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-gray-border);">
        <strong style="display: block; font-size: 0.85rem; margin-bottom: 0.25rem;">3. No Doctor Client Cert</strong>
        <span style="font-size: 0.8rem; color: var(--color-gray-mid);">Doctors do not require client certificates; web authentication operates seamlessly over standard TLS 1.3.</span>
      </div>
    </div>
  </div>

  <!-- Reports Table -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
    <h3 style="font-size: 1.35rem; font-weight: 800;">Referred Diagnostic Results</h3>
    <input type="text" class="form-control table-search-input" data-target-table="#doctorTable" placeholder="Search by patient, NID, test..." style="max-width: 300px;">
  </div>

  <div class="table-responsive" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
    <table class="data-table" id="doctorTable">
      <thead>
        <tr>
          <th>Report UID</th>
          <th>Patient Name</th>
          <th>Contact & NID</th>
          <th>Test Name</th>
          <th>Result Summary</th>
          <th>Pathologist</th>
          <th>Integrity Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reports as $r): ?>
          <tr>
            <td><strong style="font-family: var(--font-mono);"><?php echo htmlspecialchars($r['report_uid']); ?></strong></td>
            <td><strong><?php echo htmlspecialchars($r['patient_name']); ?></strong></td>
            <td>
              <small><?php echo htmlspecialchars($r['patient_phone']); ?></small><br>
              <small style="color: var(--color-gray-mid);">NID: <?php echo htmlspecialchars($r['patient_nid'] ?: 'N/A'); ?></small>
            </td>
            <td><?php echo htmlspecialchars($r['test_name']); ?></td>
            <td>
              <?php if ($r['is_tampered']): ?>
                <span style="color: #b91c1c; font-weight: 700;"><?php echo htmlspecialchars($r['result_summary']); ?></span>
              <?php else: ?>
                <span><?php echo htmlspecialchars($r['result_summary']); ?></span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($r['pathologist_name'] ?: 'Dr. Nazmul Huda'); ?></td>
            <td>
              <?php if ($r['is_tampered']): ?>
                <span class="badge badge-tampered">TAMPER DETECTED</span>
              <?php else: ?>
                <span class="badge badge-valid">AUTHENTIC</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display: flex; gap: 0.35rem;">
                <a href="generate_report.php?uid=<?php echo urlencode($r['report_uid']); ?>" target="_blank" class="btn btn-secondary btn-sm">View PDF</a>
                <a href="verify.php?uid=<?php echo urlencode($r['report_uid']); ?>" class="btn btn-outline-dark btn-sm">Audit</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
