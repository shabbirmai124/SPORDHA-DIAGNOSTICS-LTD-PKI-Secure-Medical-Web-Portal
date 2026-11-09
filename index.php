<?php
/**
 * Spordha Diagnostics Ltd. - High-Security Diagnostic Portal Landing Page
 * Monochromatic Design System (White, Black, Charcoal & Neutral Grays)
 */

$pageTitle = "Home | High-Security Diagnostic Laboratory PKI Portal";
require_once __DIR__ . '/includes/header.php';

$db = getDBConnection();

// Fetch sample active reports
$recentReports = $db->query("SELECT r.*, u.name as patient_name, b.name as branch_name 
    FROM reports r 
    JOIN users u ON r.patient_id = u.id 
    LEFT JOIN branches b ON r.branch_id = b.id 
    ORDER BY r.id ASC LIMIT 4")->fetchAll();

// Fetch security incident count
$incidentCount = $db->query("SELECT count(*) FROM incident_logs")->fetchColumn();
$certCount = $db->query("SELECT count(*) FROM certificates WHERE status = 'VALID'")->fetchColumn();
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="hero-grid">
      <!-- Left Column: Vision & Trust -->
      <div class="hero-content reveal">
        <div class="hero-badge-tag">
          <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#000;"></span>
          <span>CSE487 Mini Project — Track 2: PKI &amp; Secure Communication</span>
        </div>
        <h1 class="hero-title">
          Trusted Clinical Diagnostics, Cryptographically Verified.
        </h1>
        <p class="hero-subtitle">
          Spordha Diagnostics Ltd. operates an enterprise-grade Two-Tier Public Key Infrastructure (PKI) securing 40,000+ patient health records, mutual TLS (mTLS) branch sample uploads, and SHA-256 digitally signed pathology reports across Dhaka.
        </p>
        
        <div class="hero-cta-group">
          <a href="verify.php" class="btn btn-primary btn-lg">
            <span>Verify Report Authenticity</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </a>
          <a href="pki_soc.php" class="btn btn-secondary btn-lg">
            <span>Security Operations Center</span>
          </a>
          <a href="documentation.php" class="btn btn-outline-dark btn-lg">
            <span>Project Specification</span>
          </a>
        </div>

        <div class="hero-stats-row">
          <div class="stat-item">
            <h3 data-count="40000" data-suffix="+">40,000+</h3>
            <p>Active Patient Records</p>
          </div>
          <div class="stat-item">
            <h3 data-count="1200">1,200</h3>
            <p>Daily Lab Samples</p>
          </div>
          <div class="stat-item">
            <h3 data-count="300" data-suffix="+">300+</h3>
            <p>Referring Physicians</p>
          </div>
          <div class="stat-item">
            <h3 data-count="100" data-suffix="%">100%</h3>
            <p>Tamper Detection Rate</p>
          </div>
        </div>
      </div>

      <!-- Right Column: Quick Report Verification & One-Click Demo Role Switcher -->
      <div class="hero-card-widget reveal">
        <div class="widget-header">
          <div>
            <h3>Report Verification</h3>
            <span style="font-size: 0.75rem; color: var(--color-gray-mid);">SHA-256 Digital Signature Lookup</span>
          </div>
          <span class="badge-live-pki">PAdES Verified</span>
        </div>

        <form action="verify.php" method="GET" class="verify-input-group">
          <label for="reportSearchInput">Enter Report Identification UID:</label>
          <div class="input-with-button">
            <input type="text" id="reportSearchInput" name="uid" class="form-control" placeholder="e.g. SPD-2026-90412" required value="SPD-2026-90412">
            <button type="submit" class="btn btn-primary">Check</button>
          </div>
          <div class="sample-ids-help">
            Sample UIDs: 
            <code onclick="setVerifyQuery('SPD-2026-90412')">SPD-2026-90412 (Genuine Dengue)</code> | 
            <code onclick="setVerifyQuery('SPD-2026-90413')">SPD-2026-90413 (Lipid)</code> | 
            <code onclick="setVerifyQuery('SPD-2026-90499')" style="color:#b91c1c;">SPD-2026-90499 (Tampered)</code>
          </div>
        </form>

        <!-- One-Click Demo Role Switcher -->
        <div class="demo-credentials-box">
          <h4>Instant Demo Role Sign-In</h4>
          <p style="font-size: 0.75rem; color: var(--color-gray-dark); margin-bottom: 0.75rem;">
            Authenticate immediately as any laboratory stakeholder to evaluate role-based controls:
          </p>
          <div class="demo-roles-pills">
            <a href="login.php?quick_role=patient" class="role-pill-btn">Patient (Tanvir)</a>
            <a href="login.php?quick_role=doctor" class="role-pill-btn">Doctor (Dr. Karim)</a>
            <a href="login.php?quick_role=pathologist" class="role-pill-btn">Pathologist (Dr. Huda)</a>
            <a href="login.php?quick_role=technician" class="role-pill-btn">Technologist (Sabbir)</a>
            <a href="login.php?quick_role=branch_staff" class="role-pill-btn">Branch Staff (Uttara)</a>
            <a href="login.php?quick_role=pki_admin" class="role-pill-btn" style="background:#000; color:#fff;">PKI Admin</a>
          </div>
        </div>

        <!-- Security Indicators -->
        <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--color-gray-border); display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--color-gray-mid);">
          <span>Root CA: <strong style="color: #000;">Offline (Power Down)</strong></span>
          <span>TLS 1.3: <strong style="color: #15803d;">Active</strong></span>
          <span>Active Certs: <strong style="color: #000;"><?php echo $certCount; ?> Valid</strong></span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Core Security Architecture Pillars -->
<section class="features-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-badge">Security Architecture</span>
      <h2 class="section-title">Engineered to Resist Forgery, Impersonation & Downgrade</h2>
      <p class="section-desc">
        Comprehensive implementation of the CSE487 Mini-Project requirements (R1–R18), adhering strictly to RFC 9846, RFC 5280, NIST SP 800-52 Rev. 2, and the Cyber Security Act 2026.
      </p>
    </div>

    <div class="cards-grid-4 reveal">
      <!-- Feature 1: Two-Tier CA -->
      <div class="card-item">
        <div class="card-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V9l7-5 7 5v12"/><path d="M9 21v-6h6v6"/></svg>
        </div>
        <h3>Two-Tier CA Hierarchy</h3>
        <p>
          Air-gapped Offline Root CA with encrypted RSA-4096 keys signing only an intermediate Online Issuing CA. Strict separation of roles prevents leaf compromise from invalidating root trust.
        </p>
        <a href="pki_soc.php#ca_tree" class="card-link">View CA Hierarchy →</a>
      </div>

      <!-- Feature 2: mTLS Uploads -->
      <div class="card-item">
        <div class="card-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 15v2"/></svg>
        </div>
        <h3>Branch Mutual TLS (mTLS)</h3>
        <p>
          Eliminates shared passwords. Every branch client device (Dhanmondi, Uttara, Mirpur) authenticates with an individual X.509 client certificate containing <code>clientAuth</code> EKU.
        </p>
        <a href="branch_upload.php" class="card-link">Simulate mTLS Upload →</a>
      </div>

      <!-- Feature 3: Digital Signatures -->
      <div class="card-item">
        <div class="card-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M8 17c2-4 4 1 6-2"/></svg>
        </div>
        <h3>Cryptographic PDF Signing</h3>
        <p>
          Pathologists digitally sign clinical PDFs with dedicated document-signing certificates. Detached SHA-256 signatures immediately expose post-release tampering (Evidence E-26 vs E-27).
        </p>
        <a href="verify.php" class="card-link">Tamper Detection Lab →</a>
      </div>

      <!-- Feature 4: AI Defense -->
      <div class="card-item">
        <div class="card-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 4.5-3 8-8 9-5-1-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
        </div>
        <h3>AI-Impersonation Defense</h3>
        <p>
          Blocks executive voice-spoofing and deepfake emergency audits. Enforces mandatory out-of-band directory callbacks, dual approvers, and strictly non-exportable server private keys (E-28).
        </p>
        <a href="pki_soc.php#incident_sec" class="card-link">Incident Logs & Audit →</a>
      </div>
    </div>
  </div>
</section>

<!-- Target Architecture & Lab Topology Visual Preview -->
<section class="architecture-section">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-badge">Verified System Models</span>
      <h2 class="section-title">Architecture & Isolated Lab Topology</h2>
      <p class="section-desc">
        Derived directly from project report diagrams D1 and D3, illustrating trust boundaries and the host-only virtual laboratory.
      </p>
    </div>

    <div class="cards-grid-2 reveal">
      <!-- Diagram 1 -->
      <div class="diagram-preview-card">
        <img src="assets1/D1_target_architecture.png" alt="Spordha Diagnostics Ltd. Target Architecture Diagram D1">
        <div class="diagram-caption">
          <div>
            <h4>Figure 1 (D1): Target System Architecture</h4>
            <p>Trust boundaries separating patients, doctors, DMZ, LIMS, and two-tier PKI.</p>
          </div>
          <a href="documentation.php#architecture" class="btn btn-secondary btn-sm">Read Specification</a>
        </div>
      </div>

      <!-- Diagram 2 -->
      <div class="diagram-preview-card">
        <img src="assets1/D3_virtual_lab_topology.png" alt="Spordha Diagnostics Ltd. Isolated Virtual Lab Topology D3">
        <div class="diagram-caption">
          <div>
            <h4>Figure 2 (D3): Isolated Virtual Lab Topology</h4>
            <p>VirtualBox Host-only network isolating Root-CA, Issuing-CA, ULab-Server, and Branch-Client.</p>
          </div>
          <a href="evidence_gallery.php" class="btn btn-secondary btn-sm">Inspect Evidence</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Active Reports Showcase -->
<section class="features-section" style="background-color: var(--color-white);">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-badge">Authoritative Clinical Records</span>
      <h2 class="section-title">Authoritative Laboratory Reports</h2>
      <p class="section-desc">
        Live clinical records approved by certified pathologists and backed by immutable SHA-256 signature blocks.
      </p>
    </div>

    <div class="table-responsive reveal" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-lg); overflow: hidden;">
      <table class="data-table">
        <thead>
          <tr>
            <th>Report UID</th>
            <th>Patient Name</th>
            <th>Diagnostic Test</th>
            <th>Category</th>
            <th>Result Summary</th>
            <th>Integrity Status</th>
            <th>Digital Signature</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentReports as $rep): ?>
            <tr>
              <td>
                <strong style="font-family: var(--font-mono); font-size: 0.85rem;"><?php echo htmlspecialchars($rep['report_uid']); ?></strong>
              </td>
              <td><?php echo htmlspecialchars($rep['patient_name']); ?></td>
              <td><strong><?php echo htmlspecialchars($rep['test_name']); ?></strong></td>
              <td><?php echo htmlspecialchars($rep['test_category']); ?></td>
              <td>
                <?php if ($rep['is_tampered']): ?>
                  <span style="color: #b91c1c; font-weight: 700;"><?php echo htmlspecialchars($rep['result_summary']); ?></span>
                <?php else: ?>
                  <span style="font-weight: 600;"><?php echo htmlspecialchars($rep['result_summary']); ?></span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($rep['is_tampered']): ?>
                  <span class="badge badge-tampered">TAMPER DETECTED</span>
                <?php else: ?>
                  <span class="badge badge-valid">VERIFIED OK</span>
                <?php endif; ?>
              </td>
              <td>
                <code style="font-size: 0.75rem; background: #f4f4f5; padding: 2px 6px; border-radius: 4px;">
                  <?php echo shortHash($rep['signature_hash'], 16); ?>
                </code>
              </td>
              <td>
                <div style="display: flex; gap: 0.4rem;">
                  <a href="verify.php?uid=<?php echo urlencode($rep['report_uid']); ?>" class="btn btn-secondary btn-sm" title="Verify Signature">
                    Verify
                  </a>
                  <a href="generate_report.php?uid=<?php echo urlencode($rep['report_uid']); ?>" target="_blank" class="btn btn-outline-dark btn-sm" title="Print/View PDF Certificate">
                    PDF View
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- Statutory Compliance Banner -->
<section style="background-color: var(--color-black); color: #fff; padding: 3.5rem 0; border-top: 1px solid var(--color-charcoal);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;">
      <div style="max-width: 750px;">
        <span style="color: var(--color-gray-light); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
          Statutory & Ethical Compliance
        </span>
        <h2 style="font-size: 1.85rem; font-weight: 800; margin: 0.5rem 0 1rem; color: #fff;">
          Full Compliance with Bangladesh Cyber Laws & ACM Ethics
        </h2>
        <p style="font-size: 0.95rem; color: #a1a1aa; line-height: 1.6;">
          Engineered to satisfy the Cyber Security Act 2026 (Act 81) against unauthorized access and digital forgery, the Personal Data Protection Act 2026 (Act 63) for sensitive health data confidentiality, and the ICT Act 2006 for secure electronic signatures.
        </p>
      </div>
      <div>
        <a href="documentation.php#legal" class="btn btn-secondary btn-lg" style="background:#fff; color:#000;">
          Read Legal & Ethics Analysis →
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
