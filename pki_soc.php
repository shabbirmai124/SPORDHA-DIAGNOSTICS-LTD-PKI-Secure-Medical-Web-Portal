<?php
/**
 * Spordha Diagnostics Ltd. - PKI & Security Operations Center (SOC)
 * Manages Two-Tier CA, Certificate Inventory, CRL Revocation, Expiry Warnings & AI Threats
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDBConnection();

// Fetch certificates
$certs = $db->query("SELECT * FROM certificates ORDER BY id ASC")->fetchAll();

// Fetch incidents
$incidents = $db->query("SELECT * FROM incident_logs ORDER BY id DESC")->fetchAll();

// Fetch test cases
$testCases = $db->query("SELECT * FROM test_cases ORDER BY id ASC")->fetchAll();

$pageTitle = "PKI Security Operations Center (SOC)";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-black); color: #ffffff; padding: 3.5rem 0; border-bottom: 1px solid var(--color-charcoal);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <span class="section-badge" style="background: var(--color-charcoal); color: #fff;">PKI Infrastructure & SOC</span>
        <h1 style="font-size: 2.35rem; font-weight: 800; letter-spacing: -0.75px; margin-top: 0.5rem; color: #fff;">
          Security Operations Center
        </h1>
        <p style="color: #a1a1aa; margin-top: 0.25rem; max-width: 800px;">
          Live telemetry for the Spordha Two-Tier Certificate Authority, TLS 1.3 enforcement, CRL revocation distribution, automated 30/14/7-day expiry alerts, and AI-impersonation defense audit trail.
        </p>
      </div>
      <div>
        <a href="evidence_gallery.php" class="btn btn-secondary btn-sm" style="background: #fff; color: #000;">
          Inspect Screenshot Evidence (E-01 – E-28) →
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <!-- PKI Telemetry Metrics -->
  <div class="metrics-row">
    <div class="metric-card">
      <div class="label">Offline Root CA</div>
      <div class="value" style="font-size: 1.35rem; color: #000;">AIR-GAPPED</div>
      <div class="subtext">RSA-2048 • 3650 Days (E-05)</div>
    </div>
    <div class="metric-card">
      <div class="label">Online Issuing CA</div>
      <div class="value" style="font-size: 1.35rem; color: #15803d;">OPERATIONAL</div>
      <div class="subtext">CA:TRUE, pathLen=0 (E-06)</div>
    </div>
    <div class="metric-card">
      <div class="label">Portal TLS Protocol</div>
      <div class="value" style="font-size: 1.35rem; color: #000;">TLS 1.3 / HSTS</div>
      <div class="subtext">AES-256-GCM / MLKEM768 (E-17)</div>
    </div>
    <div class="metric-card">
      <div class="label">AI Impersonation Log</div>
      <div class="value" style="font-size: 1.35rem; color: #b91c1c;">BLOCKED</div>
      <div class="subtext">Rule SOP-SEC-04 Enforced (E-28)</div>
    </div>
  </div>

  <!-- SOC Tabbed Navigation -->
  <div class="tab-container" style="margin-top: 2rem;">
    <div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--color-gray-border); margin-bottom: 2rem; flex-wrap: wrap;">
      <button class="btn btn-secondary tab-btn active" data-tab="tab-ca-tree" style="border-bottom: none; border-radius: 6px 6px 0 0;">
        Two-Tier CA Hierarchy
      </button>
      <button class="btn btn-secondary tab-btn" data-tab="tab-inventory" style="border-bottom: none; border-radius: 6px 6px 0 0;">
        Certificate Inventory (A1)
      </button>
      <button class="btn btn-secondary tab-btn" data-tab="tab-expiry" style="border-bottom: none; border-radius: 6px 6px 0 0;">
        Expiry Warnings (R7 / E-25)
      </button>
      <button class="btn btn-secondary tab-btn" data-tab="tab-ai-threat" style="border-bottom: none; border-radius: 6px 6px 0 0;">
        AI Threat &amp; Dual Approval (E-28)
      </button>
      <button class="btn btn-secondary tab-btn" data-tab="tab-test-matrix" style="border-bottom: none; border-radius: 6px 6px 0 0;">
        16 Test Cases Matrix (TC-01–16)
      </button>
    </div>

    <!-- TAB 1: CA HIERARCHY -->
    <div class="tab-pane active" id="tab-ca-tree">
      <div class="cards-grid-2">
        <div>
          <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.75rem;">
            Two-Tier CA Trust Chain Architecture
          </h3>
          <p style="font-size: 0.875rem; color: var(--color-gray-dark); line-height: 1.6; margin-bottom: 1rem;">
            As required by Requirement R4 and shown in Figure 5 (D2), our PKI separates root certification authority from everyday leaf issuance. The offline root signs only the issuing CA certificate and is subsequently powered down.
          </p>
          <div style="background: var(--color-gray-subtle); border: 1px solid var(--color-gray-border); border-radius: var(--radius-md); padding: 1.25rem; font-size: 0.85rem; line-height: 1.8;">
            <div><strong>1. Spordha Root CA (Tier 1):</strong> Offline VM, encrypted private key storage, validity 10 years (3650 days). Signs issuing CA only.</div>
            <div><strong>2. Spordha Issuing CA (Tier 2):</strong> Online VM, validity 5 years (1825 days), issues narrowly-scoped leaves with explicit EKUs.</div>
            <div><strong>3. Leaf Certificates (Tier 3):</strong> 90-day validity for server TLS and branch client authentication; 1-year validity for document signing.</div>
          </div>
        </div>

        <div class="diagram-preview-card" style="box-shadow: none;">
          <img src="assets1/D2_two_tier_ca_hierarchy.png" alt="Two-tier CA hierarchy diagram D2">
          <div style="padding: 0.75rem 1rem; font-size: 0.75rem; color: var(--color-gray-mid); background: #fafafa; border-top: 1px solid var(--color-gray-border);">
            Figure 5 (D2): Two-tier CA hierarchy and role-separated certificate profiles.
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: CERTIFICATE INVENTORY -->
    <div class="tab-pane" id="tab-inventory">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1.25rem; font-weight: 800;">Laboratory Certificate Directory & Revocation Controls</h3>
        <span style="font-size: 0.8rem; color: var(--color-gray-mid);">RFC 5280 X.509 Compliance</span>
      </div>

      <div class="table-responsive" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
        <table class="data-table">
          <thead>
            <tr>
              <th>Profile</th>
              <th>Common Name</th>
              <th>SAN</th>
              <th>Serial Number</th>
              <th>Algorithm</th>
              <th>Valid Until</th>
              <th>Status</th>
              <th>Revocation Control</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($certs as $c): ?>
              <tr>
                <td><code style="font-size: 0.75rem;"><?php echo htmlspecialchars($c['cert_type']); ?></code></td>
                <td><strong><?php echo htmlspecialchars($c['common_name']); ?></strong></td>
                <td><small><?php echo htmlspecialchars($c['san'] ?: 'None'); ?></small></td>
                <td><code style="font-size: 0.75rem;"><?php echo htmlspecialchars($c['serial_number']); ?></code></td>
                <td><?php echo htmlspecialchars($c['key_algorithm']); ?></td>
                <td><?php echo date('d M Y', strtotime($c['valid_to'])); ?></td>
                <td>
                  <?php if ($c['status'] === 'VALID'): ?>
                    <span class="badge badge-valid">VALID</span>
                  <?php else: ?>
                    <span class="badge badge-revoked">REVOKED</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($c['cert_type'] !== 'root_ca'): ?>
                    <?php if ($c['status'] === 'VALID'): ?>
                      <button onclick="toggleCertRevocation(<?php echo $c['id']; ?>, 'revoke')" class="btn btn-outline-dark btn-sm" style="color: #991b1b; border-color: #fca5a5;">
                        Revoke (CRL)
                      </button>
                    <?php else: ?>
                      <button onclick="toggleCertRevocation(<?php echo $c['id']; ?>, 'reinstate')" class="btn btn-secondary btn-sm">
                        Reinstate
                      </button>
                    <?php endif; ?>
                  <?php else: ?>
                    <span style="font-size: 0.75rem; color: var(--color-gray-mid);">Root Protected</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: EXPIRY WARNINGS (R7 / E-25) -->
    <div class="tab-pane" id="tab-expiry">
      <div class="cards-grid-2" style="margin-bottom: 2rem;">
        <div>
          <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.75rem;">
            Automated Expiry Warning Telemetry (R7)
          </h3>
          <p style="font-size: 0.875rem; color: var(--color-gray-dark); line-height: 1.6; margin-bottom: 1.5rem;">
            In 2025, Spordha experienced a 24-hour portal outage when its certificate expired unmonitored. Under our corrected design, automated alerts trigger at <strong>30-day (Notice)</strong>, <strong>14-day (Warning)</strong>, and <strong>7-day (Critical Outage Risk)</strong> intervals.
          </p>

          <!-- Timeline Gauge -->
          <?php foreach ($certs as $c): 
            $st = getCertExpiryStatus($c['valid_to'], $c['status']);
          ?>
            <div class="expiry-status-box">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <strong><?php echo htmlspecialchars($c['common_name']); ?></strong>
                <span class="badge <?php echo $st[3]; ?>"><?php echo $st[1] > 0 ? $st[1] . ' Days Remaining' : 'Expired/Revoked'; ?></span>
              </div>
              <div class="expiry-timeline-bar">
                <div class="expiry-timeline-progress" style="width: <?php echo min(100, max(5, ($st[1] / 365) * 100)); ?>%;"></div>
              </div>
              <div style="font-size: 0.75rem; color: var(--color-gray-mid); display: flex; justify-content: space-between;">
                <span>Valid: <?php echo date('M Y', strtotime($c['valid_from'])); ?></span>
                <span>Expires: <?php echo date('d M Y', strtotime($c['valid_to'])); ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Terminal Window Mimicking E-25 -->
        <div>
          <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.75rem;">
            Server Expiry Status Daemon (Evidence E-25)
          </h3>
          <div class="terminal-window" style="margin: 0;">
            <div class="terminal-header">
              <span class="terminal-title">shabbir@ULab-Server:~ (SSL Expiry Warning System)</span>
            </div>
            <div class="terminal-body" style="font-size: 0.8rem;">
<span class="terminal-prompt">shabbir@ULab-Server:~$</span> <span class="terminal-cmd">./check_expiry.sh</span>
=====================================================
SSL Expiry Warning System - Spordha Diagnostics Ltd
Server Certificate Expiry Date: Nov  9 04:51:25 2027 GMT
Status: Certificate Valid. Automated renewal alert scheduled.
=====================================================
[Cron Daemon]: Daily certificate health check active.
Next renewal trigger: Oct 10, 2027 (30 Days threshold).
Alert recipients: it-admin@spordha.test, it-officer2@spordha.test
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 4: AI THREAT & INCIDENT LOG (E-28) -->
    <div class="tab-pane" id="tab-ai-threat">
      <div class="cards-grid-2" style="margin-bottom: 2rem;">
        <div>
          <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.75rem;">
            Executive Impersonation & AI Voice-Clone Defense
          </h3>
          <p style="font-size: 0.875rem; color: var(--color-gray-dark); line-height: 1.6; margin-bottom: 1rem;">
            The project scenario documents an attacker using an AI voice clone of the Managing Director requesting immediate transmission of "the server certificate files". Section 5.1 mandates non-exportable private keys, directory callbacks, and dual approvers.
          </p>
          <div style="background: var(--color-gray-subtle); border-left: 4px solid var(--color-black); padding: 1rem; border-radius: 4px; font-size: 0.85rem; line-height: 1.6;">
            <strong>Mandatory Protocol (Rule SOP-SEC-04):</strong> Under no circumstances may an IT officer email, upload, or export server private keys. Any urgent audit request requires an offline multi-party quorum and CISO incident notification.
          </div>
        </div>

        <div class="diagram-preview-card" style="box-shadow: none;">
          <img src="assets1/D6_ai_impersonation_attack_tree.png" alt="AI impersonation attack tree diagram D6">
          <div style="padding: 0.75rem 1rem; font-size: 0.75rem; color: var(--color-gray-mid); background: #fafafa; border-top: 1px solid var(--color-gray-border);">
            Figure 4 (D6): AI-enabled impersonation attack tree with dual approval controls.
          </div>
        </div>
      </div>

      <!-- Real Incident Logs from Evidence E-28 -->
      <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1rem;">
        Spordha Diagnostics Incident Log (Evidence E-15, E-16, E-28)
      </h3>

      <?php foreach ($incidents as $inc): ?>
        <div style="background-color: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
              <span class="badge badge-revoked" style="margin-right: 0.5rem;"><?php echo htmlspecialchars($inc['dual_approval_status']); ?></span>
              <strong style="font-family: var(--font-mono); font-size: 0.85rem;"><?php echo htmlspecialchars($inc['incident_code']); ?></strong>
            </div>
            <span style="font-size: 0.8rem; color: var(--color-gray-mid);"><?php echo formatDateTime($inc['timestamp']); ?></span>
          </div>

          <h4 style="font-size: 1.05rem; font-weight: 800; margin-bottom: 0.5rem;">
            <?php echo htmlspecialchars($inc['threat_type']); ?>
          </h4>

          <div style="background: var(--color-gray-subtle); padding: 0.85rem 1rem; border-radius: 6px; font-size: 0.8125rem; margin-bottom: 0.75rem;">
            <div><strong>From:</strong> <?php echo htmlspecialchars($inc['sender']); ?></div>
            <div><strong>To:</strong> <?php echo htmlspecialchars($inc['recipient']); ?></div>
            <div><strong>Subject:</strong> <?php echo htmlspecialchars($inc['subject']); ?></div>
            <div style="margin-top: 0.5rem; color: var(--color-charcoal); font-style: italic;">
              <?php echo htmlspecialchars($inc['details']); ?>
            </div>
          </div>

          <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 0.75rem 1rem; border-radius: 6px; font-size: 0.8125rem;">
            <strong>SECURITY RESPONSE (Rule SOP-SEC-04):</strong><br>
            <?php echo htmlspecialchars($inc['action_taken']); ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- TAB 5: 16 TEST CASES MATRIX -->
    <div class="tab-pane" id="tab-test-matrix">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h3 style="font-size: 1.25rem; font-weight: 800;">Formal Security Testing Matrix (Section 10 / A7)</h3>
          <p style="font-size: 0.85rem; color: var(--color-gray-mid);">16 Verified Test Cases across Chain Trust, Wrong Issuance, Revocation, Downgrade & Signatures.</p>
        </div>
        <input type="text" class="form-control table-search-input" data-target-table="#testMatrixTable" placeholder="Search test cases..." style="max-width: 280px;">
      </div>

      <div class="table-responsive" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
        <table class="data-table" id="testMatrixTable">
          <thead>
            <tr>
              <th>Test ID</th>
              <th>Requirement</th>
              <th>Category</th>
              <th>Test Description</th>
              <th>Expected Behavior</th>
              <th>Observed Evidence</th>
              <th>Status</th>
              <th>Evidence ID</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($testCases as $tc): ?>
              <tr>
                <td><strong style="font-family: var(--font-mono); font-size: 0.85rem;"><?php echo htmlspecialchars($tc['test_id']); ?></strong></td>
                <td><code style="font-size: 0.75rem;"><?php echo htmlspecialchars($tc['req_id']); ?></code></td>
                <td><span class="badge badge-warning-notice"><?php echo htmlspecialchars($tc['category']); ?></span></td>
                <td><strong><?php echo htmlspecialchars($tc['description']); ?></strong></td>
                <td style="font-size: 0.8rem;"><?php echo htmlspecialchars($tc['expected_result']); ?></td>
                <td style="font-size: 0.8rem; color: var(--color-charcoal);"><?php echo htmlspecialchars($tc['observed_result']); ?></td>
                <td>
                  <span class="badge badge-valid"><?php echo htmlspecialchars($tc['status']); ?></span>
                </td>
                <td><strong style="font-family: var(--font-mono); font-size: 0.75rem;"><?php echo htmlspecialchars($tc['evidence_id']); ?></strong></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
