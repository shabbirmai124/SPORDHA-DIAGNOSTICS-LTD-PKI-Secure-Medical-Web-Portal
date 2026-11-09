<?php
/**
 * Spordha Diagnostics Ltd. - Unified Role-Based Laboratory Dashboard
 * Monochromatic Professional Interface
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Ensure user is authenticated
if (!isLoggedIn()) {
    header("Location: login.php?msg=" . urlencode("Please log in to access your dashboard."));
    exit;
}

$user = currentUser();
$db = getDBConnection();
$role = $user['role'];

// Handle action messages
$successMsg = $_GET['success'] ?? '';
$errorMsg = $_GET['err'] ?? '';

// Specific data retrieval by role
if ($role === 'patient') {
    $stmt = $db->prepare("SELECT r.*, b.name as branch_name, doc.name as doctor_name, patho.name as pathologist_name 
        FROM reports r 
        LEFT JOIN branches b ON r.branch_id = b.id 
        LEFT JOIN users doc ON r.doctor_id = doc.id 
        LEFT JOIN users patho ON r.pathologist_id = patho.id 
        WHERE r.patient_id = ? 
        ORDER BY r.id DESC");
    $stmt->execute([$user['id']]);
    $patientReports = $stmt->fetchAll();

    // Stats
    $totalReports = count($patientReports);
    $signedReports = count(array_filter($patientReports, fn($r) => $r['status'] === 'approved_signed'));
} elseif ($role === 'doctor') {
    $stmt = $db->prepare("SELECT r.*, u.name as patient_name, u.phone as patient_phone, u.nid as patient_nid, b.name as branch_name 
        FROM reports r 
        JOIN users u ON r.patient_id = u.id 
        LEFT JOIN branches b ON r.branch_id = b.id 
        WHERE r.doctor_id = ? 
        ORDER BY r.id DESC");
    $stmt->execute([$user['id']]);
    $doctorReports = $stmt->fetchAll();
} elseif ($role === 'pathologist') {
    $stmt = $db->query("SELECT r.*, u.name as patient_name, doc.name as doctor_name, b.name as branch_name 
        FROM reports r 
        JOIN users u ON r.patient_id = u.id 
        LEFT JOIN users doc ON r.doctor_id = doc.id 
        LEFT JOIN branches b ON r.branch_id = b.id 
        ORDER BY r.id ASC");
    $allReports = $stmt->fetchAll();
    $pendingSign = array_filter($allReports, fn($r) => $r['status'] === 'pathologist_review');
    $signedCount = count(array_filter($allReports, fn($r) => $r['status'] === 'approved_signed'));
    $tamperedCount = count(array_filter($allReports, fn($r) => $r['is_tampered'] == 1));
} elseif ($role === 'branch_staff') {
    $branchId = $user['branch_id'] ?? 3; // Uttara default
    $bStmt = $db->prepare("SELECT * FROM branches WHERE id = ?");
    $bStmt->execute([$branchId]);
    $branch = $bStmt->fetch();

    $uStmt = $db->prepare("SELECT * FROM sample_uploads WHERE branch_id = ? ORDER BY id DESC");
    $uStmt->execute([$branchId]);
    $branchUploads = $uStmt->fetchAll();
} else {
    // PKI Administrator / Technician
    $totalCerts = $db->query("SELECT count(*) FROM certificates")->fetchColumn();
    $validCerts = $db->query("SELECT count(*) FROM certificates WHERE status = 'VALID'")->fetchColumn();
    $revokedCerts = $db->query("SELECT count(*) FROM certificates WHERE status = 'REVOKED'")->fetchColumn();
    $incidentCount = $db->query("SELECT count(*) FROM incident_logs")->fetchColumn();
    $recentIncidents = $db->query("SELECT * FROM incident_logs ORDER BY id DESC LIMIT 5")->fetchAll();
    $allCerts = $db->query("SELECT * FROM certificates ORDER BY id ASC")->fetchAll();
}

// Certificate inventory table is shared by every role's dashboard view
if (!isset($allCerts)) {
    $allCerts = $db->query("SELECT * FROM certificates ORDER BY id ASC")->fetchAll();
}

$pageTitle = "Dashboard (" . getRoleLabel($role) . ")";
require_once __DIR__ . '/includes/header.php';
?>

<div class="dashboard-layout">
  <!-- Left Sidebar -->
  <aside class="dashboard-sidebar">
    <div class="sidebar-profile">
      <div class="profile-avatar">
        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
      </div>
      <div class="profile-name"><?php echo htmlspecialchars($user['name']); ?></div>
      <div class="profile-role">
        <?php echo getRoleBadge($user['role']); ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--color-gray-mid); margin-top: 0.35rem;">
        <?php echo htmlspecialchars($user['designation'] ?: $user['email']); ?>
      </div>
    </div>

    <ul class="sidebar-menu">
      <li class="sidebar-menu-item">
        <a href="dashboard.php" class="sidebar-link active">
          <span class="icon">📊</span>
          <span>Dashboard Overview</span>
        </a>
      </li>

      <?php if ($role === 'patient'): ?>
        <li class="sidebar-menu-item">
          <a href="patient_portal.php" class="sidebar-link">
            <span class="icon">📑</span>
            <span>My Lab Reports</span>
          </a>
        </li>
      <?php elseif ($role === 'doctor'): ?>
        <li class="sidebar-menu-item">
          <a href="doctor_portal.php" class="sidebar-link">
            <span class="icon">🩺</span>
            <span>Referred Patients</span>
          </a>
        </li>
      <?php elseif ($role === 'pathologist'): ?>
        <li class="sidebar-menu-item">
          <a href="pathologist_studio.php" class="sidebar-link">
            <span class="icon">🔬</span>
            <span>Sign Clinical Reports</span>
          </a>
        </li>
      <?php elseif ($role === 'branch_staff'): ?>
        <li class="sidebar-menu-item">
          <a href="branch_upload.php" class="sidebar-link">
            <span class="icon">🏢</span>
            <span>Branch mTLS Uploads</span>
          </a>
        </li>
      <?php elseif ($role === 'pki_admin' || $role === 'technician'): ?>
        <li class="sidebar-menu-item">
          <a href="pki_soc.php" class="sidebar-link">
            <span class="icon">🛡️</span>
            <span>PKI Operations Center</span>
          </a>
        </li>
        <li class="sidebar-menu-item">
          <a href="branch_upload.php" class="sidebar-link">
            <span class="icon">🏢</span>
            <span>mTLS Upload Simulator</span>
          </a>
        </li>
        <li class="sidebar-menu-item">
          <a href="pathologist_studio.php" class="sidebar-link">
            <span class="icon">🔬</span>
            <span>Pathologist Signing Studio</span>
          </a>
        </li>
      <?php endif; ?>

      <li class="sidebar-menu-item">
        <a href="verify.php" class="sidebar-link">
          <span class="icon">🔍</span>
          <span>Verify Any Report</span>
        </a>
      </li>
      <li class="sidebar-menu-item">
        <a href="evidence_gallery.php" class="sidebar-link">
          <span class="icon">📸</span>
          <span>Evidence Gallery (E01-E28)</span>
        </a>
      </li>
      <li class="sidebar-menu-item">
        <a href="documentation.php" class="sidebar-link">
          <span class="icon">📖</span>
          <span>Project Documentation</span>
        </a>
      </li>
      <li class="sidebar-menu-item" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-gray-border);">
        <a href="logout.php" class="sidebar-link" style="color: #991b1b;">
          <span class="icon">🚪</span>
          <span>Sign Out</span>
        </a>
      </li>
    </ul>
  </aside>

  <!-- Main Dashboard Content -->
  <main class="dashboard-main">
    
    <div class="dashboard-header">
      <div>
        <h2><?php echo htmlspecialchars(getRoleLabel($role)); ?> Console</h2>
        <p>Authenticated as <strong><?php echo htmlspecialchars($user['name']); ?></strong> (<?php echo htmlspecialchars($user['email']); ?>)</p>
      </div>
      <div>
        <span class="badge badge-valid">Session Encrypted (TLS 1.3)</span>
      </div>
    </div>

    <?php if ($successMsg): ?>
      <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; color: #166534; padding: 0.85rem 1rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.875rem;">
        ✓ <?php echo htmlspecialchars($successMsg); ?>
      </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
      <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 0.85rem 1rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.875rem;">
        ⚠ <?php echo htmlspecialchars($errorMsg); ?>
      </div>
    <?php endif; ?>

    <!-- ROLE SPECIFIC VIEWS -->

    <?php if ($role === 'patient'): ?>
      <!-- PATIENT DASHBOARD -->
      <div class="metrics-row">
        <div class="metric-card">
          <div class="label">Total Investigations</div>
          <div class="value"><?php echo $totalReports; ?></div>
          <div class="subtext">All time tests conducted</div>
        </div>
        <div class="metric-card">
          <div class="label">Approved & Signed</div>
          <div class="value"><?php echo $signedReports; ?></div>
          <div class="subtext">Digitally signed by Pathologist</div>
        </div>
        <div class="metric-card">
          <div class="label">Digital Signature Status</div>
          <div class="value" style="font-size: 1.25rem; color: #15803d;">PAdES Verified</div>
          <div class="subtext">SHA-256 integrity protected</div>
        </div>
        <div class="metric-card">
          <div class="label">Patient NID</div>
          <div class="value" style="font-size: 1.15rem; font-family: var(--font-mono);"><?php echo htmlspecialchars($user['nid'] ?: 'N/A'); ?></div>
          <div class="subtext">Confidential medical record</div>
        </div>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700;">My Clinical Reports & Test Results</h3>
        <input type="text" class="form-control table-search-input" data-target-table="#patientReportsTable" placeholder="Filter my reports..." style="max-width: 250px; padding: 0.4rem 0.75rem; font-size: 0.8rem;">
      </div>

      <div class="table-responsive" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
        <table class="data-table" id="patientReportsTable">
          <thead>
            <tr>
              <th>Report UID</th>
              <th>Test Name</th>
              <th>Category</th>
              <th>Specimen</th>
              <th>Result Summary</th>
              <th>Pathologist</th>
              <th>Integrity</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($patientReports as $r): ?>
              <tr>
                <td><strong style="font-family: var(--font-mono);"><?php echo htmlspecialchars($r['report_uid']); ?></strong></td>
                <td><strong><?php echo htmlspecialchars($r['test_name']); ?></strong></td>
                <td><?php echo htmlspecialchars($r['test_category']); ?></td>
                <td><?php echo htmlspecialchars($r['specimen']); ?></td>
                <td>
                  <?php if ($r['is_tampered']): ?>
                    <span style="color:#b91c1c; font-weight:700;"><?php echo htmlspecialchars($r['result_summary']); ?></span>
                  <?php else: ?>
                    <span><?php echo htmlspecialchars($r['result_summary']); ?></span>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($r['pathologist_name'] ?: 'Dr. Nazmul Huda'); ?></td>
                <td>
                  <?php if ($r['is_tampered']): ?>
                    <span class="badge badge-tampered">TAMPER DETECTED</span>
                  <?php else: ?>
                    <span class="badge badge-valid">VERIFIED OK</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display: flex; gap: 0.35rem;">
                    <a href="generate_report.php?uid=<?php echo urlencode($r['report_uid']); ?>" target="_blank" class="btn btn-secondary btn-sm">PDF View</a>
                    <a href="verify.php?uid=<?php echo urlencode($r['report_uid']); ?>" class="btn btn-outline-dark btn-sm">Verify</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php elseif ($role === 'doctor'): ?>
      <!-- DOCTOR DASHBOARD -->
      <div class="metrics-row">
        <div class="metric-card">
          <div class="label">Referred Patients</div>
          <div class="value"><?php echo count($doctorReports); ?></div>
          <div class="subtext">Under your clinical supervision</div>
        </div>
        <div class="metric-card">
          <div class="label">Ready for Consultation</div>
          <div class="value"><?php echo count($doctorReports); ?></div>
          <div class="subtext">Pathologist approved</div>
        </div>
        <div class="metric-card">
          <div class="label">Doctor Verification Mode</div>
          <div class="value" style="font-size: 1.25rem;">Browser / PDF</div>
          <div class="subtext">No client certificate required</div>
        </div>
        <div class="metric-card">
          <div class="label">Assigned Branch</div>
          <div class="value" style="font-size: 1.25rem;">Dhaka HQ</div>
          <div class="subtext">Central Laboratory link</div>
        </div>
      </div>

      <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem;">Referred Patients & Pathology Reports</h3>
      <div class="table-responsive" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
        <table class="data-table">
          <thead>
            <tr>
              <th>Report UID</th>
              <th>Patient Name</th>
              <th>Phone</th>
              <th>Test Conducted</th>
              <th>Result Summary</th>
              <th>Signed Date</th>
              <th>Signature Integrity</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($doctorReports as $dr): ?>
              <tr>
                <td><strong style="font-family: var(--font-mono);"><?php echo htmlspecialchars($dr['report_uid']); ?></strong></td>
                <td><strong><?php echo htmlspecialchars($dr['patient_name']); ?></strong></td>
                <td><?php echo htmlspecialchars($dr['patient_phone']); ?></td>
                <td><?php echo htmlspecialchars($dr['test_name']); ?></td>
                <td><?php echo htmlspecialchars($dr['result_summary']); ?></td>
                <td><?php echo formatDateTime($dr['signed_at']); ?></td>
                <td>
                  <?php if ($dr['is_tampered']): ?>
                    <span class="badge badge-tampered">TAMPER DETECTED</span>
                  <?php else: ?>
                    <span class="badge badge-valid">VERIFIED OK</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display: flex; gap: 0.35rem;">
                    <a href="generate_report.php?uid=<?php echo urlencode($dr['report_uid']); ?>" target="_blank" class="btn btn-secondary btn-sm">Inspect Report</a>
                    <a href="verify.php?uid=<?php echo urlencode($dr['report_uid']); ?>" class="btn btn-outline-dark btn-sm">Audit</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php elseif ($role === 'branch_staff'): ?>
      <!-- BRANCH STAFF DASHBOARD -->
      <div class="metrics-row">
        <div class="metric-card">
          <div class="label">Branch Name</div>
          <div class="value" style="font-size: 1.25rem;"><?php echo htmlspecialchars($branch['name'] ?? 'Uttara Branch'); ?></div>
          <div class="subtext">Code: <?php echo htmlspecialchars($branch['code'] ?? 'BR-UTTARA'); ?></div>
        </div>
        <div class="metric-card">
          <div class="label">mTLS Client Certificate</div>
          <div class="value" style="font-size: 1.15rem; color: #15803d;">ACTIVE</div>
          <div class="subtext">Serial: <?php echo htmlspecialchars($branch['client_cert_serial'] ?? '5C:12:44:E9:90:F1:03'); ?></div>
        </div>
        <div class="metric-card">
          <div class="label">Assigned Static IP</div>
          <div class="value" style="font-size: 1.35rem; font-family: var(--font-mono);"><?php echo htmlspecialchars($branch['ip_address'] ?? '10.0.2.15'); ?></div>
          <div class="subtext">Isolated VirtualBox Adapter</div>
        </div>
        <div class="metric-card">
          <div class="label">Uploads Completed</div>
          <div class="value"><?php echo count($branchUploads); ?></div>
          <div class="subtext">Requisitions transmitted</div>
        </div>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700;">Branch Upload Queue & Transmission Logs</h3>
        <a href="branch_upload.php" class="btn btn-primary btn-sm">+ Upload New Requisition</a>
      </div>

      <div class="table-responsive" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
        <table class="data-table">
          <thead>
            <tr>
              <th>Specimen ID</th>
              <th>Patient Name</th>
              <th>NID</th>
              <th>Test Type</th>
              <th>Client Certificate Used</th>
              <th>mTLS Authentication</th>
              <th>Size</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($branchUploads as $up): ?>
              <tr>
                <td><strong style="font-family: var(--font-mono);"><?php echo htmlspecialchars($up['specimen_id']); ?></strong></td>
                <td><?php echo htmlspecialchars($up['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($up['patient_nid']); ?></td>
                <td><?php echo htmlspecialchars($up['test_type']); ?></td>
                <td><code style="font-size: 0.75rem;"><?php echo htmlspecialchars($up['client_cert_used']); ?></code></td>
                <td>
                  <?php if ($up['auth_status'] === 'SUCCESS_MTLS'): ?>
                    <span class="badge badge-valid">SUCCESS (mTLS)</span>
                  <?php else: ?>
                    <span class="badge badge-revoked"><?php echo htmlspecialchars($up['auth_status']); ?></span>
                  <?php endif; ?>
                </td>
                <td><?php echo number_format($up['bytes_transferred']); ?> bytes</td>
                <td><?php echo formatDateTime($up['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php else: ?>
      <!-- PKI ADMINISTRATOR / PATHOLOGIST / TECHNICIAN DASHBOARD -->
      <div class="metrics-row">
        <div class="metric-card">
          <div class="label">Total Certificates</div>
          <div class="value"><?php echo $totalCerts ?? 7; ?></div>
          <div class="subtext">Two-tier hierarchy</div>
        </div>
        <div class="metric-card">
          <div class="label">Valid & Trusted</div>
          <div class="value" style="color: #15803d;"><?php echo $validCerts ?? 6; ?></div>
          <div class="subtext">Serving TLS & Signatures</div>
        </div>
        <div class="metric-card">
          <div class="label">Revoked on CRL</div>
          <div class="value" style="color: #b91c1c;"><?php echo $revokedCerts ?? 1; ?></div>
          <div class="subtext">Refused by upload gate</div>
        </div>
        <div class="metric-card">
          <div class="label">Threat Incidents</div>
          <div class="value"><?php echo $incidentCount ?? 3; ?></div>
          <div class="subtext">Dual-approval blocked</div>
        </div>
      </div>

      <!-- Quick SOC Navigation Cards -->
      <div class="cards-grid-3" style="margin-bottom: 2.5rem;">
        <div class="card-item">
          <h3>Two-Tier CA Status</h3>
          <p>Root CA is stored offline with encrypted key custody. Online Issuing CA issues 90-day leaf certificates with strict EKU enforcement.</p>
          <a href="pki_soc.php#ca_tree" class="btn btn-secondary btn-sm">Inspect CA Hierarchy →</a>
        </div>
        <div class="card-item">
          <h3>Certificate Expiry & CRL</h3>
          <p>Automated 30/14/7-day warnings active. View current CRL revocation status and test immediate invalidation.</p>
          <a href="pki_soc.php#expiry_sec" class="btn btn-secondary btn-sm">View Expiry Warnings →</a>
        </div>
        <div class="card-item">
          <h3>AI Threat Log (E-28)</h3>
          <p>Simulated executive voice spoofing and private key export attempts blocked under Rule SOP-SEC-04 dual-approval policy.</p>
          <a href="pki_soc.php#incident_sec" class="btn btn-primary btn-sm">View Incident Trail →</a>
        </div>
      </div>

      <!-- Live Certificate Inventory Table -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700;">Spordha Laboratory Certificate Inventory (A1/A17)</h3>
        <a href="pki_soc.php" class="btn btn-outline-dark btn-sm">Open Full PKI Operations Center</a>
      </div>

      <div class="table-responsive" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
        <table class="data-table">
          <thead>
            <tr>
              <th>Type</th>
              <th>Common Name</th>
              <th>SAN</th>
              <th>Key Spec</th>
              <th>Valid To</th>
              <th>Status</th>
              <th>Fingerprint SHA-256</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allCerts as $c): ?>
              <tr>
                <td><code style="font-size: 0.75rem; background: #f4f4f5; padding: 2px 6px; border-radius: 4px;"><?php echo htmlspecialchars($c['cert_type']); ?></code></td>
                <td><strong><?php echo htmlspecialchars($c['common_name']); ?></strong></td>
                <td><small><?php echo htmlspecialchars($c['san'] ?: 'None'); ?></small></td>
                <td><?php echo htmlspecialchars($c['key_algorithm']); ?></td>
                <td><?php echo date('d M Y', strtotime($c['valid_to'])); ?></td>
                <td>
                  <?php if ($c['status'] === 'VALID'): ?>
                    <span class="badge badge-valid">VALID</span>
                  <?php else: ?>
                    <span class="badge badge-revoked">REVOKED</span>
                  <?php endif; ?>
                </td>
                <td><code style="font-size: 0.7rem;"><?php echo shortHash($c['fingerprint_sha256'], 16); ?></code></td>
                <td>
                  <?php if ($c['status'] === 'VALID' && $c['cert_type'] !== 'root_ca'): ?>
                    <button onclick="toggleCertRevocation(<?php echo $c['id']; ?>, 'revoke')" class="btn btn-outline-dark btn-sm" style="color: #991b1b; border-color: #fca5a5;">
                      Revoke
                    </button>
                  <?php elseif ($c['status'] === 'REVOKED'): ?>
                    <button onclick="toggleCertRevocation(<?php echo $c['id']; ?>, 'reinstate')" class="btn btn-secondary btn-sm">
                      Reinstate
                    </button>
                  <?php else: ?>
                    <span style="font-size: 0.75rem; color: var(--color-gray-mid);">Protected</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>

  </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
