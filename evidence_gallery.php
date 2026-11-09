<?php
/**
 * Spordha Diagnostics Ltd. - Visual Evidence Gallery (E-01 through E-28)
 * High-Resolution Audit of All 28 Server & VirtualBox Implementation Screenshots
 */

$pageTitle = "Implementation Evidence Gallery (E-01 – E-28)";
require_once __DIR__ . '/includes/header.php';

// Array of all 28 screenshot descriptions matching the official academic report
$evidenceItems = [
    [
        'id' => 'E-01',
        'title' => 'VirtualBox VM Inventory — Lab Components',
        'img' => 'assets1/E01_screenshot.png',
        'cat' => 'isolation',
        'status' => 'PARTIAL',
        'status_text' => 'VMs configured; Root-CA shown running (target is offline power-down)',
        'desc' => 'Manager lists Root-CA, Issuing-CA, ULab-Server, and Branch-Client. While all nodes exist, Root-CA must be shut down post-issuance to satisfy offline root requirements.'
    ],
    [
        'id' => 'E-02',
        'title' => 'Server Network Adapter — Host-Only Selection',
        'img' => 'assets1/E02_screenshot.png',
        'cat' => 'isolation',
        'status' => 'PASS',
        'status_text' => 'Host-Only Ethernet Adapter selected for server VM',
        'desc' => 'ULab-Server Adapter 2 is mapped to VirtualBox Host-Only Ethernet Adapter, establishing an isolated sandbox disconnected from public internet routes.'
    ],
    [
        'id' => 'E-03',
        'title' => 'Initial Portal Lookup Failure from Root-CA',
        'img' => 'assets1/E03_screenshot.png',
        'cat' => 'isolation',
        'status' => 'INCONCLUSIVE',
        'status_text' => 'DNS / hosts name resolution failure prior to TLS handshake',
        'desc' => 'OpenSSL s_client reports "Name or service not known" for portal.spordha.test. Highlights the need for static /etc/hosts mapping inside the isolated lab.'
    ],
    [
        'id' => 'E-04',
        'title' => 'Server Certificate Signing Request (CSR) Displayed',
        'img' => 'assets1/E04_screenshot.png',
        'cat' => 'ca',
        'status' => 'PASS',
        'status_text' => 'Standard CSR public key block inspected in terminal',
        'desc' => 'Terminal displays the Base64-encoded PKCS#10 Certificate Request generated on ULab-Server, containing the public key and requested subject identity.'
    ],
    [
        'id' => 'E-05',
        'title' => 'Root CA Certificate Generation Command',
        'img' => 'assets1/E05_screenshot.png',
        'cat' => 'ca',
        'status' => 'PASS',
        'status_text' => '10-Year self-signed RSA-2048 Root CA created',
        'desc' => 'OpenSSL command creates Spordha Root CA (/C=BD/ST=Dhaka/L=Dhaka/O=Spordha Diagnostics Ltd/CN=Spordha Root CA) with 3650 days validity. Note: -nodes omitted encryption; rebuilt with AES-256.'
    ],
    [
        'id' => 'E-06',
        'title' => 'Issuing CA Certificate Signed by Root CA',
        'img' => 'assets1/E06_screenshot.png',
        'cat' => 'ca',
        'status' => 'PASS',
        'status_text' => 'Issuing CA signed with CA:TRUE, keyCertSign, and cRLSign',
        'desc' => 'Root CA signs issuing-ca.csr for 1825 days (5 years) using SHA-256. Critical basicConstraints=CA:TRUE and keyUsage extensions enforced.'
    ],
    [
        'id' => 'E-07',
        'title' => 'Certificate Chain Assembled & Server Verified',
        'img' => 'assets1/E07_screenshot.png',
        'cat' => 'ca',
        'status' => 'PASS',
        'status_text' => 'OpenSSL verify reports server.crt: OK against ca-chain.crt',
        'desc' => 'Intermediate issuing CA and root CA concatenated into ca-chain.crt. OpenSSL confirms cryptographic chain validation succeeded.'
    ],
    [
        'id' => 'E-08',
        'title' => 'Branch-Side Certificate Transfer — Initial Attempt',
        'img' => 'assets1/E08_screenshot.png',
        'cat' => 'branch',
        'status' => 'PARTIAL',
        'status_text' => 'SCP connection troubleshooting across virtual interfaces',
        'desc' => 'Branch-Client attempts to fetch ca-chain.crt via SCP over port 2221. Connection refused on localhost; routed via internal IP 10.0.2.2.'
    ],
    [
        'id' => 'E-09',
        'title' => 'Repeated SCP & Trust-Store Troubleshooting',
        'img' => 'assets1/E09_screenshot.png',
        'cat' => 'branch',
        'status' => 'PARTIAL',
        'status_text' => 'File path and directory permission validation',
        'desc' => 'Host key fingerprint accepted for 10.0.2.2. Demonstrates troubleshooting file permissions and correct /etc/nginx/ssl directory creation.'
    ],
    [
        'id' => 'E-10',
        'title' => 'curl CA Bundle Path Error Demonstration',
        'img' => 'assets1/E10_screenshot.png',
        'cat' => 'branch',
        'status' => 'INCONCLUSIVE',
        'status_text' => 'Path nonexistent error caught by curl client',
        'desc' => 'curl reports provided --cacert file does not exist, proving that client certificate errors in this test were path-related rather than cryptographic.'
    ],
    [
        'id' => 'E-11',
        'title' => 'Windows PowerShell Transfer Attempt',
        'img' => 'assets1/E11_screenshot.png',
        'cat' => 'branch',
        'status' => 'PARTIAL',
        'status_text' => 'Windows host to VM transfer troubleshooting',
        'desc' => 'Demonstrates testing cross-platform transfer from host Windows machine to guest Linux VM.'
    ],
    [
        'id' => 'E-12',
        'title' => 'Repeated Branch Trust Validation Failure',
        'img' => 'assets1/E12_screenshot.png',
        'cat' => 'branch',
        'status' => 'PARTIAL',
        'status_text' => 'Path verification before client certificate enrollment',
        'desc' => 'Repeated check confirming that branch certificate files must be securely provisioned to /home/shabbir/pki/client/.'
    ],
    [
        'id' => 'E-13',
        'title' => 'Server Certificate Expiry Inspection',
        'img' => 'assets1/E13_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'notAfter=Nov 9 04:51:25 2027 GMT extracted from server.crt',
        'desc' => 'OpenSSL x509 -enddate inspection extracts exact expiration timestamp, serving as input to the automated expiry alert system.'
    ],
    [
        'id' => 'E-14',
        'title' => 'Synthetic Patient Result Text File Created',
        'img' => 'assets1/E14_screenshot.png',
        'cat' => 'signing',
        'status' => 'PASS',
        'status_text' => 'Pre-signing clinical text file created on ULab-Server',
        'desc' => 'Creates initial synthetic test artifact: "Patient: X, Result: Negative", establishing baseline data for the digital signing workflow.'
    ],
    [
        'id' => 'E-15',
        'title' => 'Urgent Key-Export Email Role-Play in Nano Editor',
        'img' => 'assets2/E15_screenshot.png',
        'cat' => 'ai_threat',
        'status' => 'PASS',
        'status_text' => 'Written role-play simulating AI CEO emergency audit request',
        'desc' => 'Simulates executive impersonation attack: fake CEO requests immediate server key export. IT officer replies: "Request Denied. Dual approval required."'
    ],
    [
        'id' => 'E-16',
        'title' => 'Urgent Key-Export Email Role-Play Saved & Displayed',
        'img' => 'assets2/E16_screenshot.png',
        'cat' => 'ai_threat',
        'status' => 'PASS',
        'status_text' => 'Cat output of social engineering attack email and denial',
        'desc' => 'Terminal displays saved incident text, proving the establishment of operational procedures rejecting unverified executive demands.'
    ],
    [
        'id' => 'E-17',
        'title' => 'Portal TLS 1.3 Handshake, Verification & HSTS',
        'img' => 'assets2/E17_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'TLSv1.3, SSL certificate verified via OpenSSL, HSTS emitted',
        'desc' => 'curl connects to portal.spordha.test:443. Certificate chain verified, SAN matched, and Strict-Transport-Security: max-age=31536000 confirmed.'
    ],
    [
        'id' => 'E-18',
        'title' => 'Legacy TLS Protocol Downgrade Refusal',
        'img' => 'assets2/E18_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'curl --tls-max 1.1 handshake rejected with protocol error',
        'desc' => 'Demonstrates protocol downgrade rejection. Connections offering TLS 1.1 or below are terminated (TC-11).'
    ],
    [
        'id' => 'E-19',
        'title' => 'Upload Endpoint Connection — Certificate Error Response',
        'img' => 'assets2/E19_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'Nginx returns 400 Bad Request: The SSL certificate error',
        'desc' => 'Initial upload request without valid client certificate triggers Nginx mTLS gate rejection.'
    ],
    [
        'id' => 'E-20',
        'title' => 'Upload Endpoint Rejects Missing Client Certificate',
        'img' => 'assets2/E20_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'Explicit error: "No required SSL certificate was sent" (TC-06)',
        'desc' => 'Direct evidence of Requirement R6 enforcement. Nginx returns HTTP 400 when client certificate is absent; request payload is discarded without storage.'
    ],
    [
        'id' => 'E-21',
        'title' => 'Nginx Error Log — Portal Directory Index Denied',
        'img' => 'assets2/E21_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'Nginx log confirms directory index forbidden (HTTP 403 reason)',
        'desc' => 'Log audit verifies that the HTTP 403 observed in E-17 was an application-level directory index protection, not a TLS handshake failure.'
    ],
    [
        'id' => 'E-22',
        'title' => 'TLS Handshake with Role-Separation Violation',
        'img' => 'assets2/E22_screenshot.png',
        'cat' => 'tls',
        'status' => 'PARTIAL',
        'status_text' => 'Server cert used as client cert; proves EKU role separation needed',
        'desc' => 'Handshake completed using server.crt as client identity, demonstrating why Extended Key Usage (EKU) checks are required to reject server certs on client endpoints (TC-05).'
    ],
    [
        'id' => 'E-23',
        'title' => 'CRL Directive Enabled & Nginx Reloaded',
        'img' => 'assets2/E23_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'ssl_crl directive activated and Nginx reloaded',
        'desc' => 'Enables Certificate Revocation List (CRL) checking in Nginx configuration and reloads service to enforce real-time revocation (TC-09).'
    ],
    [
        'id' => 'E-24',
        'title' => 'Upload TLS Request with Revocation Active',
        'img' => 'assets2/E24_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => '400 The SSL certificate error returned under active CRL',
        'desc' => 'Demonstrates that invalid or blacklisted client certificates are refused by the Nginx mTLS gateway.'
    ],
    [
        'id' => 'E-25',
        'title' => 'SSL Expiry Warning System Output',
        'img' => 'assets2/E25_screenshot.png',
        'cat' => 'ai_threat',
        'status' => 'PASS',
        'status_text' => 'SSL Expiry Warning System banner and scheduled alert',
        'desc' => 'Displays certificate valid status and schedules automated renewal alerts, fulfilling Requirement R7 expiry notification.'
    ],
    [
        'id' => 'E-26',
        'title' => 'Detached SHA-256 Signature Verifies on Report File',
        'img' => 'assets2/E26_screenshot.png',
        'cat' => 'signing',
        'status' => 'PASS',
        'status_text' => 'OpenSSL dgst -sha256 -verify reports Verified OK (TC-13)',
        'desc' => 'Clinical test file is signed with private key and verified against public certificate. OpenSSL outputs "Verified OK", proving genuine document authenticity.'
    ],
    [
        'id' => 'E-27',
        'title' => 'Edited Report Fails Cryptographic Verification',
        'img' => 'assets2/E27_screenshot.png',
        'cat' => 'signing',
        'status' => 'PASS',
        'status_text' => 'Verification failure: bad signature detected (TC-14)',
        'desc' => 'Attacker appends "Modified by Attacker: Result Positive". OpenSSL verification fails with error 0x02000068 (bad signature), mathematically exposing tampering.'
    ],
    [
        'id' => 'E-28',
        'title' => 'Spordha Incident Log (E-10) — Executive Phishing Blocked',
        'img' => 'assets2/E28_screenshot.png',
        'cat' => 'ai_threat',
        'status' => 'PASS',
        'status_text' => 'Security incident logged; AI voice/phishing blocked under SOP-SEC-04',
        'desc' => 'Formal security incident log documenting spoofed CEO demand for private keys. Blocked under PKI Security Policy Section 4.2 requiring offline multi-party quorum.'
    ]
];
?>

<div style="background-color: var(--color-black); color: #fff; padding: 3.5rem 0; border-bottom: 1px solid var(--color-charcoal);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <span class="section-badge" style="background: var(--color-charcoal); color: #fff;">Audit Evidence Archive</span>
        <h1 style="font-size: 2.35rem; font-weight: 800; letter-spacing: -0.75px; margin-top: 0.5rem; color: #fff;">
          Evidence Gallery (E-01 – E-28)
        </h1>
        <p style="color: #a1a1aa; margin-top: 0.25rem; max-width: 850px;">
          Complete technical catalog of all 28 implementation screenshots from the student server document. Each image is paired with terminal command analysis, audit status, and requirement mapping.
        </p>
      </div>
      <div>
        <a href="documentation.php" class="btn btn-secondary btn-sm" style="background: #fff; color: #000;">
          Read 16-Section Report →
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <!-- Evidence Filter Pills -->
  <div style="display: flex; gap: 0.5rem; margin-bottom: 2.5rem; flex-wrap: wrap;">
    <button class="btn btn-primary btn-sm gallery-filter-btn" data-filter="all">All Evidence (28)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="isolation">VirtualBox & Isolation (E01–E03)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="ca">Two-Tier CA Setup (E04–E07)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="branch">Branch Setup (E08–E12)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="tls">TLS 1.3 & mTLS (E13, E17–E24)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="signing">Report Signing & Tamper (E14, E26, E27)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="ai_threat">AI Impersonation Defense (E15, E16, E25, E28)</button>
  </div>

  <!-- Evidence Items Grid -->
  <div class="cards-grid-2" id="evidenceGrid">
    <?php foreach ($evidenceItems as $ev): ?>
      <div class="card-item evidence-card-item" data-category="<?php echo $ev['cat']; ?>" id="<?php echo strtolower($ev['id']); ?>" style="padding: 1.5rem;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <strong style="font-family: var(--font-mono); font-size: 1.1rem; color: #000;">
            [<?php echo $ev['id']; ?>]
          </strong>
          <span class="badge <?php echo $ev['status'] === 'PASS' ? 'badge-valid' : ($ev['status'] === 'PARTIAL' ? 'badge-warning-high' : 'badge-warning-notice'); ?>">
            <?php echo $ev['status']; ?>
          </span>
        </div>

        <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 0.75rem;">
          <?php echo htmlspecialchars($ev['title']); ?>
        </h3>

        <!-- Screenshot Container -->
        <div style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-md); overflow: hidden; margin-bottom: 1rem; background: #000;">
          <img src="<?php echo $ev['img']; ?>" alt="<?php echo htmlspecialchars($ev['title']); ?>" style="width: 100%; height: auto; display: block;">
        </div>

        <!-- Technical Findings -->
        <div style="background: var(--color-gray-subtle); padding: 0.85rem 1rem; border-radius: 6px; font-size: 0.8125rem; margin-bottom: 0.75rem; border: 1px solid var(--color-gray-border);">
          <strong>Audit Finding:</strong> <?php echo htmlspecialchars($ev['status_text']); ?>
        </div>

        <p style="font-size: 0.85rem; color: var(--color-gray-dark); line-height: 1.6; margin-bottom: 0;">
          <?php echo htmlspecialchars($ev['desc']); ?>
        </p>

      </div>
    <?php endforeach; ?>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const filterBtns = document.querySelectorAll('.gallery-filter-btn');
  const cards = document.querySelectorAll('.evidence-card-item');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      filterBtns.forEach(b => {
        b.classList.remove('btn-primary');
        b.classList.add('btn-secondary');
      });
      this.classList.remove('btn-secondary');
      this.classList.add('btn-primary');

      const filter = this.getAttribute('data-filter');
      cards.forEach(card => {
        if (filter === 'all' || card.getAttribute('data-category') === filter) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
