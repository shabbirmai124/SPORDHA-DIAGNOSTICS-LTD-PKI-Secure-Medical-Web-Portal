<?php
/**
 * Spordha Diagnostics Ltd. - Branch Mutual TLS (mTLS) Upload Service
 * Implements Requirement R6 & Tests TC-05, TC-06, TC-07, TC-08, TC-09
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDBConnection();
$uploadStatus = null;
$terminalLog = '';

// Handle Requisition Upload Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branchId   = (int)($_POST['branch_id'] ?? 3);
    $patientName= trim($_POST['patient_name'] ?? '');
    $patientNid = trim($_POST['patient_nid'] ?? '');
    $testType   = trim($_POST['test_type'] ?? 'CBC & ESR');
    $certOption = $_POST['client_cert_mode'] ?? 'valid';

    // Retrieve branch details
    $bStmt = $db->prepare("SELECT * FROM branches WHERE id = ?");
    $bStmt->execute([$branchId]);
    $branch = $bStmt->fetch();

    $specimenId = 'SPEC-' . strtoupper(substr($branch['code'] ?? 'BR', 3, 3)) . '-' . rand(1000, 9999);

    if ($certOption === 'valid') {
        // Successful mTLS handshake (Evidence image38.png -> HTTP 200)
        $authStatus = 'SUCCESS_MTLS';
        $bytes = rand(12000, 25000);
        $clientCertUsed = 'branch-' . strtolower($branch['code']) . '.spordha.test';

        $ins = $db->prepare("INSERT INTO sample_uploads (branch_id, patient_name, patient_nid, test_type, specimen_id, client_cert_used, auth_status, bytes_transferred, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
        $ins->execute([$branchId, $patientName, $patientNid, $testType, $specimenId, $clientCertUsed, $authStatus, $bytes]);

        $uploadStatus = [
            'success' => true,
            'code' => 200,
            'message' => "HTTP/1.1 200 OK — mTLS Mutual Authentication Successful. Requisition stored securely in LIMS queue.",
            'specimen_id' => $specimenId
        ];

        $terminalLog = "shabbir@Branch-Client:~$ curl -s -o /dev/null -w \"%{http_code}\\n\" \\\n" .
            "  --cacert ~/pki/client/ca-chain.crt \\\n" .
            "  --cert ~/pki/client/branch-client.crt \\\n" .
            "  --key ~/pki/client/branch-client.key \\\n" .
            "  https://upload.spordha.test:4443/api/upload\n" .
            "200\n" .
            "[Nginx Access Log]: 10.0.2.15 - [09/Nov/2026:07:56:25] \"POST /api/upload HTTP/1.1\" 200 $bytes bytes - Client Cert: " . $clientCertUsed . " (OK)";

    } elseif ($certOption === 'missing') {
        // Missing Client Certificate (Evidence E-20 -> HTTP 400 Bad Request)
        $uploadStatus = [
            'success' => false,
            'code' => 400,
            'message' => "400 Bad Request: No required SSL certificate was sent (Evidence E-20). Upload refused; zero bytes stored on disk.",
            'specimen_id' => null
        ];

        $terminalLog = "shabbir@Branch-Client:~$ curl -v --cacert ~/pki/client/ca-chain.crt https://upload.spordha.test:4443/api/upload\n" .
            "* TLSv1.3 (IN), TLS handshake, Request CERT (13):\n" .
            "* TLSv1.3 (OUT), TLS handshake, Certificate (11):\n" .
            "< HTTP/1.1 400 Bad Request\n" .
            "< Server: nginx/1.28.3 (Ubuntu)\n" .
            "<html>\n" .
            "<head><title>400 No required SSL certificate was sent</title></head>\n" .
            "<body>\n" .
            "<center><h1>400 Bad Request</h1></center>\n" .
            "<center>No required SSL certificate was sent</center>\n" .
            "<hr><center>nginx/1.28.3 (Ubuntu)</center>\n" .
            "</body>\n" .
            "</html>\n" .
            "[Nginx Security Log]: Handshake aborted: client failed to present mandatory X.509 client certificate (Rule: R6, TC-06)";

    } elseif ($certOption === 'revoked') {
        // Revoked Client Certificate (Evidence E-23 -> CRL Blacklist)
        $uploadStatus = [
            'success' => false,
            'code' => 400,
            'message' => "400 Bad Request: The SSL certificate error (Revoked in CRL) (Evidence E-23). Upload rejected.",
            'specimen_id' => null
        ];

        $terminalLog = "shabbir@Branch-Client:~$ curl -v --cacert ~/pki/client/ca-chain.crt \\\n" .
            "  --cert ~/pki/client/revoked.crt \\\n" .
            "  --key ~/pki/client/revoked.key \\\n" .
            "  https://upload.spordha.test:4443/api/upload\n" .
            "< HTTP/1.1 400 Bad Request\n" .
            "< Server: nginx/1.28.3 (Ubuntu)\n" .
            "<html>\n" .
            "<head><title>400 The SSL certificate error</title></head>\n" .
            "<body>\n" .
            "<center><h1>400 Bad Request</h1></center>\n" .
            "<center>The SSL certificate error</center>\n" .
            "<hr><center>nginx/1.28.3 (Ubuntu)</center>\n" .
            "</body>\n" .
            "</html>\n" .
            "[Nginx Security Log]: Certificate serial 99:AA:BB:CC:DD:EE:00:11 matches entry in /etc/nginx/ssl/issuing-ca.crl (Revoked token - Rule R7, TC-09)";

    } elseif ($certOption === 'wrong_type') {
        // Wrong Certificate Type / Server Cert used as client (Evidence E-22)
        $uploadStatus = [
            'success' => false,
            'code' => 400,
            'message' => "400 The SSL certificate error: ExtendedKeyUsage missing clientAuth (Evidence E-22). Server certificate cannot be reused as client identity.",
            'specimen_id' => null
        ];

        $terminalLog = "shabbir@Branch-Client:~$ curl -v --cacert ~/pki/client/ca-chain.crt \\\n" .
            "  --cert ~/pki/server/server.crt \\\n" .
            "  --key ~/pki/server/server.key \\\n" .
            "  https://upload.spordha.test:4443/api/upload\n" .
            "* TLSv1.3 handshake negotiated TLS_AES_256_GCM_SHA384 / X25519MLKEM768\n" .
            "< HTTP/1.1 400 Bad Request\n" .
            "< Server: nginx/1.28.3 (Ubuntu)\n" .
            "<html><head><title>400 The SSL certificate error</title></head>\n" .
            "<body><center><h1>400 Bad Request</h1></center><center>The SSL certificate error</center></body></html>\n" .
            "[Nginx Security Log]: Certificate role separation violation: presented certificate has EKU=serverAuth only. Required: clientAuth (Rule R4/R6, TC-05)";
    }
}

// Fetch recent uploads
$recentUploads = $db->query("SELECT u.*, b.name as branch_name 
    FROM sample_uploads u 
    JOIN branches b ON u.branch_id = b.id 
    ORDER BY u.id DESC LIMIT 10")->fetchAll();

$branches = $db->query("SELECT * FROM branches ORDER BY id ASC")->fetchAll();

$pageTitle = "Branch mTLS Upload Service | Requirement R6";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <span class="section-badge">Mutual TLS (mTLS) • Requirement R6</span>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">
          Branch Upload Service
        </h1>
        <p style="color: var(--color-gray-dark); margin-top: 0.25rem;">
          Mandatory client certificate authentication for branch PC uploads, eliminating shared passwords and plain HTTP.
        </p>
      </div>
      <div>
        <a href="evidence_gallery.php#e20" class="btn btn-secondary">
          <span>View E-20 Evidence Proof</span>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <!-- mTLS Workflow Diagram Reference Card -->
  <div class="cards-grid-2" style="margin-bottom: 3rem;">
    <div>
      <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.75rem;">
        mTLS Transaction & Security Policy
      </h3>
      <p style="font-size: 0.875rem; color: var(--color-gray-dark); line-height: 1.6; margin-bottom: 1rem;">
        In the old system, branch uploads used plain HTTP with a single shared password. Under our new design (Figure 6 / D4), every branch device has its own cryptographic client certificate. Requests without a valid, unrevoked client certificate are immediately dropped at the TLS layer before reaching application storage.
      </p>
      <ul style="font-size: 0.85rem; color: var(--color-charcoal); line-height: 1.8; margin-left: 1.25rem;">
        <li><strong>Mandatory ClientAuth:</strong> <code>ssl_verify_client on; ssl_verify_depth 2;</code></li>
        <li><strong>CRL Enforcement:</strong> Blacklisted certificates in <code>issuing-ca.crl</code> refused (TC-09).</li>
        <li><strong>Zero Data Leakage:</strong> Rejections occur during TLS handshake; 0 bytes committed to database.</li>
        <li><strong>Intermittent Link Spool:</strong> Offline encrypted local queue retry upon connectivity restoration.</li>
      </ul>
    </div>

    <div class="diagram-preview-card" style="box-shadow: none;">
      <img src="assets1/D4_mtls_branch_upload_flow.png" alt="mTLS Branch Upload Flow D4">
      <div style="padding: 0.75rem 1rem; font-size: 0.75rem; color: var(--color-gray-mid); background: #fafafa; border-top: 1px solid var(--color-gray-border);">
        Figure 6 (D4): Branch upload validation and decision logic.
      </div>
    </div>
  </div>

  <!-- Interactive mTLS Simulator Form -->
  <div style="background-color: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-xl); padding: 2rem; box-shadow: var(--shadow-lg); margin-bottom: 3rem;">
    <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.5rem;">
      🧪 Interactive Branch mTLS Upload Simulator
    </h3>
    <p style="font-size: 0.85rem; color: var(--color-gray-mid); margin-bottom: 1.5rem;">
      Select an originating branch terminal and a client certificate state to test the server's mutual TLS enforcement:
    </p>

    <form action="branch_upload.php" method="POST">
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div>
          <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.4rem;">Originating Branch:</label>
          <select name="branch_id" class="form-control" required>
            <?php foreach ($branches as $b): ?>
              <?php if ($b['id'] > 1): ?>
                <option value="<?php echo $b['id']; ?>">
                  <?php echo htmlspecialchars($b['name']); ?> (<?php echo htmlspecialchars($b['ip_address']); ?>)
                </option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.4rem;">Patient Name:</label>
          <input type="text" name="patient_name" class="form-control" required placeholder="e.g. Mahfuzur Rahman" value="Mahfuzur Rahman">
        </div>

        <div>
          <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.4rem;">Patient NID:</label>
          <input type="text" name="patient_nid" class="form-control" required placeholder="e.g. 19882619851299" value="19882619851299">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
        <div>
          <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.4rem;">Investigation Requisition:</label>
          <select name="test_type" class="form-control" required>
            <option value="Dengue NS1 Antigen & CBC">Dengue NS1 Antigen & Complete Blood Count</option>
            <option value="Lipid Profile & HbA1c">Lipid Profile & HbA1c</option>
            <option value="Liver Function Test (LFT)">Liver Function Test (LFT)</option>
            <option value="COVID-19 RT-PCR">COVID-19 RT-PCR Screening</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.4rem;">Client Certificate Simulation State:</label>
          <select name="client_cert_mode" class="form-control" required style="font-weight: 600;">
            <option value="valid">🟢 Valid Client Certificate (EKU=clientAuth) — HTTP 200</option>
            <option value="missing">🔴 Missing Client Certificate (No cert sent) — HTTP 400 (E-20)</option>
            <option value="revoked">🔴 Revoked Certificate (Blacklisted on CRL) — HTTP 400 (E-23)</option>
            <option value="wrong_type">🔴 Wrong EKU (Server cert used as client) — HTTP 400 (E-22)</option>
          </select>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
        Transmit Sample Requisition via Simulated mTLS Handshake
      </button>
    </form>

    <?php if ($uploadStatus): ?>
      <div style="margin-top: 2rem; padding: 1.25rem; border-radius: var(--radius-lg); border: 2px solid <?php echo $uploadStatus['success'] ? '#86efac' : '#fca5a5'; ?>; background-color: <?php echo $uploadStatus['success'] ? '#f0fdf4' : '#fef2f2'; ?>;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
          <h4 style="font-size: 1.05rem; font-weight: 800; color: <?php echo $uploadStatus['success'] ? '#166534' : '#991b1b'; ?>;">
            <?php echo $uploadStatus['success'] ? '✓ MUTUAL TLS HANDSHAKE SUCCESSFUL' : '⚠ MUTUAL TLS HANDSHAKE REFUSED'; ?>
          </h4>
          <span class="badge <?php echo $uploadStatus['success'] ? 'badge-valid' : 'badge-revoked'; ?>">
            HTTP <?php echo $uploadStatus['code']; ?>
          </span>
        </div>
        <p style="font-size: 0.9rem; color: <?php echo $uploadStatus['success'] ? '#15803d' : '#b91c1c'; ?>; margin-bottom: 1rem;">
          <?php echo htmlspecialchars($uploadStatus['message']); ?>
        </p>

        <!-- Simulated Terminal Log Output -->
        <div class="terminal-window" style="margin: 0;">
          <div class="terminal-header">
            <span class="terminal-title">Nginx mTLS Gateway Trace (upload.spordha.test:4443)</span>
          </div>
          <div class="terminal-body" style="font-size: 0.75rem;">
<?php echo htmlspecialchars($terminalLog); ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Live Upload Requisition Queue Table -->
  <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1rem;">Recent Branch Transmission Queue</h3>
  <div class="table-responsive" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
    <table class="data-table">
      <thead>
        <tr>
          <th>Specimen ID</th>
          <th>Origin Branch</th>
          <th>Patient Name</th>
          <th>NID</th>
          <th>Test Requisition</th>
          <th>Client Certificate Used</th>
          <th>mTLS Status</th>
          <th>Payload Size</th>
          <th>Timestamp</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentUploads as $u): ?>
          <tr>
            <td><strong style="font-family: var(--font-mono);"><?php echo htmlspecialchars($u['specimen_id']); ?></strong></td>
            <td><strong><?php echo htmlspecialchars($u['branch_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($u['patient_name']); ?></td>
            <td><?php echo htmlspecialchars($u['patient_nid']); ?></td>
            <td><?php echo htmlspecialchars($u['test_type']); ?></td>
            <td><code style="font-size: 0.75rem;"><?php echo htmlspecialchars($u['client_cert_used']); ?></code></td>
            <td>
              <?php if ($u['auth_status'] === 'SUCCESS_MTLS'): ?>
                <span class="badge badge-valid">SUCCESS (mTLS)</span>
              <?php else: ?>
                <span class="badge badge-revoked"><?php echo htmlspecialchars($u['auth_status']); ?></span>
              <?php endif; ?>
            </td>
            <td><?php echo number_format($u['bytes_transferred']); ?> bytes</td>
            <td><?php echo formatDateTime($u['created_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
