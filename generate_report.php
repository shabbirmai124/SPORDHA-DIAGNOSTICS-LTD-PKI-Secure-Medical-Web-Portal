<?php
/**
 * Spordha Diagnostics Ltd. - Printable Clinical Report Certificate
 * Authentic Medical Report View with Cryptographic Signature Seal
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDBConnection();
$uid = trim($_GET['uid'] ?? 'SPD-2026-90412');

$stmt = $db->prepare("SELECT r.*, u.name as patient_name, u.phone as patient_phone, u.nid as patient_nid, doc.name as doctor_name, doc.designation as doctor_desig, patho.name as pathologist_name, patho.designation as pathologist_desig, b.name as branch_name, b.location as branch_loc 
    FROM reports r 
    JOIN users u ON r.patient_id = u.id 
    LEFT JOIN users doc ON r.doctor_id = doc.id 
    LEFT JOIN users patho ON r.pathologist_id = patho.id 
    LEFT JOIN branches b ON r.branch_id = b.id 
    WHERE r.report_uid = ? LIMIT 1");
$stmt->execute([$uid]);
$report = $stmt->fetch();

if (!$report) {
    die("Report UID not found in laboratory archive.");
}

$resultsArray = json_decode($report['result_details'], true);
$pageTitle = "Report " . htmlspecialchars($report['report_uid']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($report['report_uid']); ?> — Spordha Diagnostics Ltd.</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <style>
    body { background-color: #f4f4f5; padding: 2rem 0; font-family: 'Times New Roman', Times, serif; }
    .no-print-bar {
      max-width: 850px;
      margin: 0 auto 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: var(--font-sans);
    }
  </style>
</head>
<body>

  <!-- Top Action Bar (Hidden on print) -->
  <div class="no-print-bar">
    <a href="verify.php?uid=<?php echo urlencode($report['report_uid']); ?>" class="btn btn-secondary btn-sm">
      ← Back to Verification Portal
    </a>
    <div style="display: flex; gap: 0.5rem;">
      <button onclick="window.print()" class="btn btn-primary btn-sm">
        🖨️ Print / Save as PDF
      </button>
    </div>
  </div>

  <!-- Clinical Report Sheet -->
  <div class="clinical-report-sheet">
    
    <!-- Tamper Alert Banner (if modified) -->
    <?php if ($report['is_tampered']): ?>
      <div style="background-color: #fef2f2; border: 2px solid #ef4444; color: #991b1b; padding: 1.25rem; border-radius: 6px; margin-bottom: 2rem; font-family: var(--font-sans); text-align: center;">
        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.35rem;">
          ⚠ CRITICAL INTEGRITY ALERT: FORGERY / TAMPER DETECTED
        </h3>
        <p style="font-size: 0.85rem; margin: 0;">
          The cryptographic hash of this document DOES NOT match the certifying pathologist's digital signature. This document has been illegally modified after issuance (Evidence E-27). Do not rely on this clinical document.
        </p>
      </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="report-header-sec">
      <div class="report-lab-brand">
        <h2>Spordha Diagnostics Ltd.</h2>
        <p>Central Laboratory: 124 Bir Uttam C.R. Datta Road, Dhaka-1205, Bangladesh</p>
        <p style="font-size: 0.75rem; color: #666;">Sample Branches: Dhanmondi • Uttara • Mirpur | Tel: +880 2 9876543 | www.spordha.test</p>
      </div>
      <div style="text-align: right; font-family: var(--font-sans);">
        <span class="badge <?php echo $report['is_tampered'] ? 'badge-tampered' : 'badge-valid'; ?>" style="font-size: 0.8rem;">
          <?php echo $report['is_tampered'] ? 'TAMPERED (TC-14)' : 'CRYPTOGRAPHICALLY SIGNED'; ?>
        </span>
        <div style="font-size: 0.75rem; color: #666; margin-top: 0.4rem; font-family: var(--font-mono);">
          UID: <?php echo htmlspecialchars($report['report_uid']); ?>
        </div>
      </div>
    </div>

    <!-- Patient Metadata Grid -->
    <div class="report-meta-grid">
      <div>
        <div class="meta-row">
          <span class="meta-label">Patient Name:</span>
          <span><?php echo htmlspecialchars($report['patient_name']); ?></span>
        </div>
        <div class="meta-row">
          <span class="meta-label">Patient Contact:</span>
          <span><?php echo htmlspecialchars($report['patient_phone']); ?></span>
        </div>
        <div class="meta-row">
          <span class="meta-label">National ID (NID):</span>
          <span><?php echo htmlspecialchars($report['patient_nid'] ?: 'N/A'); ?></span>
        </div>
        <div class="meta-row">
          <span class="meta-label">Sample Collection Branch:</span>
          <span><?php echo htmlspecialchars($report['branch_name'] ?: 'Central Laboratory'); ?></span>
        </div>
      </div>

      <div>
        <div class="meta-row">
          <span class="meta-label">Report ID / Barcode:</span>
          <span style="font-family: var(--font-mono); font-weight: 700;"><?php echo htmlspecialchars($report['report_uid']); ?></span>
        </div>
        <div class="meta-row">
          <span class="meta-label">Specimen Type:</span>
          <span><?php echo htmlspecialchars($report['specimen']); ?></span>
        </div>
        <div class="meta-row">
          <span class="meta-label">Referring Physician:</span>
          <span><?php echo htmlspecialchars($report['doctor_name'] ?: 'Self / Walk-in'); ?></span>
        </div>
        <div class="meta-row">
          <span class="meta-label">Authorization Timestamp:</span>
          <span><?php echo formatDateTime($report['signed_at']); ?></span>
        </div>
      </div>
    </div>

    <!-- Test Title Banner -->
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 0.5rem; margin-bottom: 1.5rem; font-family: var(--font-sans);">
      <h3 style="font-size: 1.35rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">
        DEPARTMENT OF <?php echo strtoupper(htmlspecialchars($report['test_category'])); ?>
      </h3>
      <h4 style="font-size: 1.1rem; font-weight: 700; color: #333; margin-top: 0.2rem;">
        <?php echo htmlspecialchars($report['test_name']); ?>
      </h4>
    </div>

    <!-- Clinical Parameters Table -->
    <table class="report-results-table">
      <thead>
        <tr>
          <th>Investigation Parameter</th>
          <th>Observed Clinical Value</th>
          <th>Standard Units</th>
          <th>Biological Reference Interval</th>
        </tr>
      </thead>
      <tbody>
        <?php if (is_array($resultsArray)): ?>
          <?php foreach ($resultsArray as $param => $val): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($param); ?></strong></td>
              <td>
                <?php if (strpos(strtolower($val['value'] ?? ''), 'positive') !== false || strpos(strtolower($val['value'] ?? ''), 'altered') !== false): ?>
                  <strong style="color: #b91c1c; font-size: 0.95rem;"><?php echo htmlspecialchars($val['value'] ?? ''); ?></strong>
                <?php else: ?>
                  <strong style="font-size: 0.95rem;"><?php echo htmlspecialchars($val['value'] ?? ''); ?></strong>
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($val['unit'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($val['ref'] ?? '-'); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4"><?php echo htmlspecialchars($report['result_details']); ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Diagnostic Impression Box -->
    <div style="border: 1px solid #000; padding: 1rem 1.25rem; margin-bottom: 2rem; font-family: var(--font-sans);">
      <h5 style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.35rem;">
        Diagnostic Impression & Clinical Comments:
      </h5>
      <p style="font-size: 0.9rem; margin: 0; line-height: 1.5;">
        <strong><?php echo htmlspecialchars($report['result_summary']); ?></strong>
      </p>
      <p style="font-size: 0.75rem; color: #555; margin-top: 0.4rem;">
        Note: Clinical correlation is required. In accordance with ISO 15189 laboratory standards, repeated test verification is conducted for critical panic values.
      </p>
    </div>

    <!-- Pathologist Signature & PKI Seal Block -->
    <div class="report-signature-block">
      <!-- Left: Pathologist Details -->
      <div>
        <div style="font-size: 1rem; font-weight: 800;">
          <?php echo htmlspecialchars($report['pathologist_name'] ?: 'Dr. Nazmul Huda'); ?>
        </div>
        <div style="font-size: 0.8rem; color: #444;">
          <?php echo htmlspecialchars($report['pathologist_desig'] ?: 'Consultant Pathologist, MBBS, MD (Clinical Pathology)'); ?>
        </div>
        <div style="font-size: 0.75rem; color: #666; margin-top: 0.2rem;">
          Head of Laboratory Medicine • Spordha Diagnostics Ltd.
        </div>
      </div>

      <!-- Right: PAdES Digital Seal -->
      <div class="digital-signature-seal">
        <h5>
          <span>🛡️</span> DIGITAL SIGNATURE VERIFICATION SEAL
        </h5>
        <div class="signature-cert-details">
          Certificate: Dr. Nazmul Huda - Document Signer<br>
          CA Issuer: Spordha Issuing CA (2-Tier PKI)<br>
          Algorithm: RSA-2048 / SHA-256 (RFC 5280)<br>
          Digest: <?php echo shortHash($report['signature_hash'], 28); ?><br>
          Online Status: https://portal.spordha.test/verify.php?uid=<?php echo urlencode($report['report_uid']); ?>
        </div>
      </div>
    </div>

    <!-- Document Legal Notice -->
    <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px dotted #ccc; font-family: var(--font-sans); font-size: 0.7rem; color: #777; text-align: center;">
      This medical document is protected under the Cyber Security Act 2026 (Act 81) and ICT Act 2006. Any unauthorized reproduction, modification, or presentation of tampered health records is a punishable cyber offence.
    </div>

  </div>

</body>
</html>
