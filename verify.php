<?php
/**
 * Spordha Diagnostics Ltd. - Public Report Authenticity Verifier & Tamper Lab
 * Real-time SHA-256 Verification & Cryptographic Integrity Testing
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDBConnection();

$queryUid = trim($_GET['uid'] ?? '');
$searchedReport = null;
$verificationResult = null;

if (!empty($queryUid)) {
    $stmt = $db->prepare("SELECT r.*, u.name as patient_name, u.nid as patient_nid, doc.name as doctor_name, patho.name as pathologist_name, b.name as branch_name 
        FROM reports r 
        JOIN users u ON r.patient_id = u.id 
        LEFT JOIN users doc ON r.doctor_id = doc.id 
        LEFT JOIN users patho ON r.pathologist_id = patho.id 
        LEFT JOIN branches b ON r.branch_id = b.id 
        WHERE r.report_uid = ? LIMIT 1");
    $stmt->execute([$queryUid]);
    $searchedReport = $stmt->fetch();

    if ($searchedReport) {
        $verificationResult = verifyReportIntegrity($searchedReport);
    }
}

$pageTitle = "Report Verification & Tamper Detection Lab";
$loadVerifyScript = true;
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <span class="section-badge">Cryptographic Integrity Engine</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">
          Report Verification & Tamper Lab
        </h1>
        <p style="color: var(--color-gray-dark); margin-top: 0.25rem;">
          Authoritative validation of clinical pathology reports using SHA-256 digests and pathologist RSA signatures.
        </p>
      </div>
      <div>
        <a href="#interactive_lab" class="btn btn-secondary">
          <span>Jump to Live Tamper Playground ↓</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <!-- SECTION 1: REPORT SEARCH & LOOKUP -->
  <div style="background-color: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-xl); padding: 2rem; box-shadow: var(--shadow-md); margin-bottom: 3.5rem;">
    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">
      Search & Verify Document Signature
    </h3>
    <p style="font-size: 0.85rem; color: var(--color-gray-mid); margin-bottom: 1.5rem;">
      Enter the unique Report UID printed on the diagnostic report or encoded inside the cryptographic QR code:
    </p>

    <form action="verify.php" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
      <input type="text" name="uid" class="form-control" placeholder="e.g. SPD-2026-90412" required value="<?php echo htmlspecialchars($queryUid ?: 'SPD-2026-90412'); ?>" style="flex: 1; min-width: 280px;">
      <button type="submit" class="btn btn-primary" style="padding: 0.7rem 1.75rem;">
        Validate Signature
      </button>
    </form>

    <div class="sample-ids-help">
      Try Pre-seeded Reports: 
      <a href="verify.php?uid=SPD-2026-90412" style="color: #000; text-decoration: underline;">SPD-2026-90412 (Genuine Dengue NS1)</a> • 
      <a href="verify.php?uid=SPD-2026-90413" style="color: #000; text-decoration: underline;">SPD-2026-90413 (Lipid Profile)</a> • 
      <a href="verify.php?uid=SPD-2026-90499" style="color: #b91c1c; text-decoration: underline; font-weight: 700;">SPD-2026-90499 (Tampered Visa Medical)</a>
    </div>

    <!-- Verification Output Card -->
    <?php if ($searchedReport): ?>
      <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--color-gray-border);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
          <div>
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--color-gray-mid); text-transform: uppercase;">
              VERIFICATION RECORD FOR
            </span>
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-top: 0.2rem;">
              <?php echo htmlspecialchars($searchedReport['test_name']); ?>
            </h2>
            <div style="font-size: 0.85rem; color: var(--color-gray-dark);">
              Patient: <strong><?php echo htmlspecialchars($searchedReport['patient_name']); ?></strong> (NID: <?php echo htmlspecialchars($searchedReport['patient_nid'] ?: 'Confidential'); ?>) • UID: <code><?php echo htmlspecialchars($searchedReport['report_uid']); ?></code>
            </div>
          </div>

          <div>
            <?php if ($searchedReport['is_tampered']): ?>
              <span class="badge badge-tampered" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                ⚠ TAMPER DETECTED (INVALID)
              </span>
            <?php else: ?>
              <span class="badge badge-valid" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                ✓ CRYPTOGRAPHICALLY AUTHENTIC
              </span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Verification Detail Banner -->
        <div class="tamper-box <?php echo $searchedReport['is_tampered'] ? 'tampered' : 'genuine'; ?>" style="margin-bottom: 1.5rem;">
          <h4 style="margin-bottom: 0.5rem; color: <?php echo $searchedReport['is_tampered'] ? '#991b1b' : '#166534'; ?>;">
            <?php echo $searchedReport['is_tampered'] ? 'Integrity Check Failed: Signature Mismatch' : 'Integrity Check Passed: Authoritative Pathologist Sign-off'; ?>
          </h4>
          <p style="font-size: 0.875rem; line-height: 1.6; margin-bottom: 0.75rem;">
            <?php echo htmlspecialchars($verificationResult[1]); ?>
          </p>

          <div class="hash-display-box <?php echo $searchedReport['is_tampered'] ? 'tampered-hash' : ''; ?>">
            Digital Signature: <?php echo htmlspecialchars($searchedReport['signature_hash']); ?><br>
            Payload Digest:    <?php echo htmlspecialchars($searchedReport['raw_data_hash']); ?><br>
            Certifying Pathologist: <?php echo htmlspecialchars($searchedReport['pathologist_name'] ?: 'Dr. Nazmul Huda'); ?> (RSA-2048)<br>
            Signing Timestamp: <?php echo formatDateTime($searchedReport['signed_at']); ?>
          </div>
        </div>

        <div style="display: flex; gap: 0.75rem;">
          <a href="generate_report.php?uid=<?php echo urlencode($searchedReport['report_uid']); ?>" target="_blank" class="btn btn-primary">
            📄 Open Authoritative PDF Report Certificate
          </a>
        </div>

      </div>
    <?php elseif (!empty($queryUid)): ?>
      <div style="margin-top: 1.5rem; padding: 1.25rem; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius-md); color: #991b1b;">
        <strong>Report UID Not Found:</strong> The requested identifier <code><?php echo htmlspecialchars($queryUid); ?></code> does not exist in the Spordha Central Laboratory database. Ensure the UID was entered correctly.
      </div>
    <?php endif; ?>
  </div>

  <!-- SECTION 2: INTERACTIVE TAMPER PLAYGROUND -->
  <div id="interactive_lab" style="background-color: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-xl); padding: 2.5rem; box-shadow: var(--shadow-lg);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
      <div>
        <span class="section-badge">Live Cryptographic Lab</span>
        <h2 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.35rem;">
          Interactive Real-Time Tamper Testing Playground
        </h2>
        <p style="font-size: 0.875rem; color: var(--color-gray-dark); margin-top: 0.25rem;">
          Directly simulates Evidence E-26 vs E-27. Modify any character in the payload below to watch the SHA-256 hash recalculate and witness how post-issuance tampering is mathematically detected!
        </p>
      </div>

      <div style="display: flex; gap: 0.5rem;">
        <button id="simulateTamperBtn" class="btn btn-outline-dark btn-sm" style="color: #b91c1c; border-color: #fca5a5;">
          ⚡ Simulate Attacker Tampering (E-27)
        </button>
        <button id="resetPayloadBtn" class="btn btn-secondary btn-sm">
          ↺ Reset to Genuine Baseline (E-26)
        </button>
      </div>
    </div>

    <div class="tamper-lab-grid">
      <!-- Input Side -->
      <div>
        <label for="interactivePayloadInput" style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.4rem;">
          Clinical Report Text Payload (report.txt):
        </label>
        <textarea id="interactivePayloadInput" class="form-control" rows="6" style="font-family: var(--font-mono); font-size: 0.85rem; line-height: 1.5; resize: vertical;">Spordha Diagnostics Ltd - Patient: Test Patient, Test: Dengue NS1, Result: Negative</textarea>

        <div style="margin-top: 1rem;">
          <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.4rem;">
            Real-Time Computed SHA-256 Cryptographic Hash:
          </label>
          <div id="liveComputedHash" class="hash-display-box" style="margin: 0; font-size: 0.8rem;">
            Calculating...
          </div>
        </div>
      </div>

      <!-- Verification Status Output Side -->
      <div>
        <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.4rem;">
          Simulated OpenSSL Public Key Signature Verification:
        </label>
        <div id="interactiveVerifyResult" class="tamper-box genuine" style="min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
          <!-- Dynamically populated by assets/js/verify.js -->
        </div>
      </div>
    </div>

    <!-- Technical Note -->
    <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--color-gray-border); font-size: 0.8125rem; color: var(--color-gray-mid); line-height: 1.6;">
      <strong>Why this prevents forgery:</strong> Because an attacker does not possess the pathologist's RSA private key, any change to the clinical report (even altering a single character from "Negative" to "Positive") results in an entirely different SHA-256 digest via the avalanche effect. When decrypted with the pathologist's public key, the mathematical signature check fails completely.
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
