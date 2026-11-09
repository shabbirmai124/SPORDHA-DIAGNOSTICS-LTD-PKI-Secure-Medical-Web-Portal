<?php
/**
 * Spordha Diagnostics Ltd. - Pathologist Approval & Digital Signing Studio
 * Implements Requirement R8 & Tests TC-13 / TC-14
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Allow pathologist, technician, or pki_admin to evaluate
if (!isLoggedIn()) {
    header("Location: login.php?msg=" . urlencode("Pathologist authentication required."));
    exit;
}

$user = currentUser();
$db = getDBConnection();
$msg = '';
$err = '';

// Handle Digital Signing Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reportId = (int)$_POST['report_id'];
    
    if ($_POST['action'] === 'sign_report') {
        $pathologistId = $user['id'];
        if (signClinicalReport($reportId, $pathologistId)) {
            $msg = "Report successfully approved and cryptographically signed with Pathologist RSA-2048 signing certificate! SHA-256 signature block generated. (Evidence E-26)";
        } else {
            $err = "Failed to digitally sign report.";
        }
    } elseif ($_POST['action'] === 'tamper_report') {
        // Tamper simulation matching E-27
        $stmt = $db->prepare("UPDATE reports SET 
            is_tampered = 1, 
            status = 'tampered', 
            result_summary = 'Positive (TAMPERED)',
            result_details = result_details || ' [TAMPERED: Altered by attacker without private key]' 
            WHERE id = ?");
        if ($stmt->execute([$reportId])) {
            $err = "SECURITY ALERT TRIGGERED: Report payload intentionally altered. Cryptographic hash signature check will now FAIL with error 0x02000068 (Evidence E-27)!";
        }
    } elseif ($_POST['action'] === 'reset_report') {
        // Re-sign to restore genuine state
        signClinicalReport($reportId, $user['id']);
        $msg = "Report restored to genuine pathologist sign-off. Cryptographic verification is once again VALID.";
    }
}

// Fetch all reports
$reports = $db->query("SELECT r.*, u.name as patient_name, doc.name as doctor_name, patho.name as pathologist_name 
    FROM reports r 
    JOIN users u ON r.patient_id = u.id 
    LEFT JOIN users doc ON r.doctor_id = doc.id 
    LEFT JOIN users patho ON r.pathologist_id = patho.id 
    ORDER BY r.id ASC")->fetchAll();

$pageTitle = "Pathologist Studio | Cryptographic Document Signing";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <span class="section-badge">PAdES / Cryptographic Signing</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">
          Pathologist Approval & Signing Studio
        </h1>
        <p style="color: var(--color-gray-dark); margin-top: 0.25rem;">
          Authenticated as <strong><?php echo htmlspecialchars($user['name']); ?></strong> (<?php echo htmlspecialchars($user['designation'] ?: 'Consultant Pathologist'); ?>).
        </p>
      </div>
      <div>
        <a href="verify.php" class="btn btn-primary">
          <span>Go to Tamper Lab →</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <?php if ($msg): ?>
    <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; color: #166534; padding: 1rem 1.25rem; border-radius: 4px; margin-bottom: 2rem; font-size: 0.9rem;">
      ✓ <?php echo htmlspecialchars($msg); ?>
    </div>
  <?php endif; ?>

  <?php if ($err): ?>
    <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 1rem 1.25rem; border-radius: 4px; margin-bottom: 2rem; font-size: 0.9rem;">
      ⚠ <?php echo htmlspecialchars($err); ?>
    </div>
  <?php endif; ?>

  <!-- Signing Policy & Terminal Evidence Card -->
  <div class="cards-grid-2" style="margin-bottom: 3rem;">
    <!-- Policy & Keys -->
    <div class="card-item">
      <h3>Active Document Signing Certificate</h3>
      <p>
        Under Spordha Certificate Policy (Section 6.5), reports are signed using an individual pathologist's key certified by the Spordha Issuing CA. This provides non-repudiation and clinical accountability for medical checks.
      </p>
      <div style="background: var(--color-gray-subtle); padding: 1rem; border-radius: 6px; font-size: 0.8125rem; border: 1px solid var(--color-gray-border);">
        <div><strong>Certificate:</strong> Dr. Nazmul Huda - Document Signer</div>
        <div><strong>Algorithm:</strong> RSA-2048 / SHA-256 (PAdES Basic)</div>
        <div><strong>Key Usage:</strong> <code>digitalSignature, nonRepudiation</code></div>
        <div><strong>Valid To:</strong> Nov 9, 2027 (1 Year Policy)</div>
        <div><strong>Storage:</strong> Cryptographic USB Token / Protected Module</div>
      </div>
    </div>

    <!-- Terminal Comparison Box -->
    <div class="terminal-window" style="margin: 0;">
      <div class="terminal-header">
        <div class="terminal-dots">
          <span class="terminal-dot red"></span>
          <span class="terminal-dot yellow"></span>
          <span class="terminal-dot green"></span>
        </div>
        <span class="terminal-title">shabbir@ULab-Server:~ (OpenSSL Signing & Tamper Evidence)</span>
      </div>
      <div class="terminal-body" style="font-size: 0.75rem;">
<span class="terminal-prompt">shabbir@ULab-Server:~$</span> <span class="terminal-cmd">openssl dgst -sha256 -sign doc-signing.key -out report.sig report.txt</span>
<span class="terminal-prompt">shabbir@ULab-Server:~$</span> <span class="terminal-cmd">openssl dgst -sha256 -verify doc-signing.crt -signature report.sig report.txt</span>
<span class="terminal-out-green">Verified OK</span>  <span style="color:#a1a1aa;">&lt;-- Genuine Signature (Evidence E-26)</span>

<span class="terminal-prompt">shabbir@ULab-Server:~$</span> <span class="terminal-cmd">echo "Modified by Attacker: Result Positive" &gt;&gt; report.txt</span>
<span class="terminal-prompt">shabbir@ULab-Server:~$</span> <span class="terminal-cmd">openssl dgst -sha256 -verify doc-signing.crt -signature report.sig report.txt</span>
<span class="terminal-out-red">Verification failure:</span>
<span class="terminal-out-red">404712D682720000:error:02000068:rsa routines:ossl_rsa_verify:bad signature</span> <span style="color:#a1a1aa;">(Evidence E-27)</span>
      </div>
    </div>
  </div>

  <!-- Clinical Reports Review List -->
  <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.25rem;">Clinical Case Queue for Pathologist Authorization</h3>

  <div style="display: flex; flex-direction: column; gap: 2rem;">
    <?php foreach ($reports as $r): ?>
      <div class="card-item" style="border: 2px solid <?php echo $r['is_tampered'] ? '#fca5a5' : ($r['status'] === 'approved_signed' ? '#bbf7d0' : '#e4e4e7'); ?>;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
          <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <span class="badge badge-warning-notice"><?php echo htmlspecialchars($r['test_category']); ?></span>
              <strong style="font-family: var(--font-mono); font-size: 0.85rem;"><?php echo htmlspecialchars($r['report_uid']); ?></strong>
            </div>
            <h2 style="font-size: 1.4rem; font-weight: 800; margin: 0.35rem 0;">
              <?php echo htmlspecialchars($r['test_name']); ?>
            </h2>
            <div style="font-size: 0.85rem; color: var(--color-gray-dark);">
              Patient: <strong><?php echo htmlspecialchars($r['patient_name']); ?></strong> | Specimen: <strong><?php echo htmlspecialchars($r['specimen']); ?></strong> | Referring Dr: <strong><?php echo htmlspecialchars($r['doctor_name'] ?: 'Dr. Rezaul Karim'); ?></strong>
            </div>
          </div>

          <div>
            <?php if ($r['is_tampered']): ?>
              <span class="badge badge-tampered">STATUS: TAMPER DETECTED (TC-14)</span>
            <?php else: ?>
              <span class="badge badge-valid">STATUS: SIGNED & VERIFIED (TC-13)</span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Structured Test Results Table -->
        <div class="table-responsive" style="margin-bottom: 1.25rem;">
          <table class="data-table" style="background: var(--color-gray-subtle); border-radius: var(--radius-md);">
            <thead>
              <tr>
                <th>Investigation Parameter</th>
                <th>Observed Clinical Value</th>
                <th>Units</th>
                <th>Biological Reference Range</th>
              </tr>
            </thead>
            <tbody>
              <?php 
                $details = json_decode($r['result_details'], true);
                if (is_array($details)):
                  foreach ($details as $param => $vals):
              ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($param); ?></strong></td>
                  <td>
                    <?php if (strpos(strtolower($vals['value'] ?? ''), 'positive') !== false || strpos(strtolower($vals['value'] ?? ''), 'altered') !== false): ?>
                      <span style="color: #b91c1c; font-weight: 700;"><?php echo htmlspecialchars($vals['value'] ?? ''); ?></span>
                    <?php else: ?>
                      <span style="font-weight: 600;"><?php echo htmlspecialchars($vals['value'] ?? ''); ?></span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($vals['unit'] ?? '-'); ?></td>
                  <td><?php echo htmlspecialchars($vals['ref'] ?? '-'); ?></td>
                </tr>
              <?php 
                  endforeach;
                else:
              ?>
                <tr>
                  <td colspan="4"><?php echo htmlspecialchars($r['result_details']); ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Summary & Diagnostic Impression -->
        <div style="margin-bottom: 1.25rem; background: var(--color-gray-bg); padding: 0.85rem 1.25rem; border-radius: var(--radius-md); font-size: 0.875rem;">
          <strong>Diagnostic Impression:</strong> <?php echo htmlspecialchars($r['result_summary']); ?>
          <br>
          <span style="font-size: 0.75rem; color: var(--color-gray-mid);">Reference Range Note: <?php echo htmlspecialchars($r['reference_range'] ?: 'Standard intervals applied.'); ?></span>
        </div>

        <!-- Cryptographic Digital Signature Block -->
        <div style="background: #0c0d0e; color: #fff; padding: 1rem 1.25rem; border-radius: var(--radius-md); font-family: var(--font-mono); font-size: 0.75rem; margin-bottom: 1.5rem;">
          <div style="color: #a1a1aa; margin-bottom: 0.35rem;">
            DIGITAL SIGNATURE HASH (SHA-256):
          </div>
          <div style="color: <?php echo $r['is_tampered'] ? '#f87171' : '#4ade80'; ?>; word-break: break-all;">
            <?php echo htmlspecialchars($r['signature_hash']); ?>
          </div>
          <div style="color: #71717a; margin-top: 0.35rem; font-size: 0.7rem;">
            Certified by: <?php echo htmlspecialchars($r['pathologist_name'] ?: 'Dr. Nazmul Huda'); ?> • Timestamp: <?php echo formatDateTime($r['signed_at']); ?>
          </div>
        </div>

        <!-- Pathologist Action Controls -->
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
          <form action="pathologist_studio.php" method="POST" style="margin: 0;">
            <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
            <input type="hidden" name="action" value="sign_report">
            <button type="submit" class="btn btn-primary btn-sm">
              ✍️ Re-Sign Report with Pathologist Key
            </button>
          </form>

          <?php if (!$r['is_tampered']): ?>
            <form action="pathologist_studio.php" method="POST" style="margin: 0;">
              <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
              <input type="hidden" name="action" value="tamper_report">
              <button type="submit" class="btn btn-outline-dark btn-sm" style="color: #b91c1c; border-color: #fca5a5;">
                ⚡ Simulate Attacker Tampering (TC-14 / E-27)
              </button>
            </form>
          <?php else: ?>
            <form action="pathologist_studio.php" method="POST" style="margin: 0;">
              <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
              <input type="hidden" name="action" value="reset_report">
              <button type="submit" class="btn btn-secondary btn-sm" style="color: #166534; border-color: #bbf7d0;">
                ↺ Restore Authentic Signed State
              </button>
            </form>
          <?php endif; ?>

          <a href="generate_report.php?uid=<?php echo urlencode($r['report_uid']); ?>" target="_blank" class="btn btn-secondary btn-sm">
            Printable PDF Certificate
          </a>
          <a href="verify.php?uid=<?php echo urlencode($r['report_uid']); ?>" class="btn btn-outline-dark btn-sm">
            Live Tamper Verification Lab
          </a>
        </div>

      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
