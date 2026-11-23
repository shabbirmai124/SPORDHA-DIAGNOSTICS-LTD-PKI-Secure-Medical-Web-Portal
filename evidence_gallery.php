<!-- Md Shabbir Mia -->
<?php

$pageTitle = "Implementation Evidence Gallery (E-01 – E-31)";
require_once __DIR__ . '/includes/header.php';

$evidenceItems = [
    [
        'id' => 'E-01',
        'title' => 'VirtualBox VM Inventory — Lab Components',
        'img' => 'assets1/E01_screenshot.png',
        'cat' => 'isolation',
        'status' => 'PASS',
        'status_text' => 'All VMs fully isolated & operational; Root-CA offline verified',
        'desc' => 'VirtualBox Manager lists Root-CA, Issuing-CA, ULab-Server, and Branch-Client. Two-tier architecture is fully provisioned with Root-CA safely air-gapped and powered down post-signing to maintain an offline root trust anchor.'
    ],
    [
        'id' => 'E-02',
        'title' => 'Server Network Adapter — Host-Only Selection',
        'img' => 'assets1/E02_screenshot.png',
        'cat' => 'isolation',
        'status' => 'PASS',
        'status_text' => 'Host-Only Ethernet Adapter selected for server VM',
        'desc' => 'ULab-Server Adapter 2 is mapped to VirtualBox Host-Only Ethernet Adapter (10.0.2.0/24), establishing an isolated sandbox disconnected from public internet routing and external attack vectors.'
    ],
    [
        'id' => 'E-03',
        'title' => 'Initial Portal Lookup & DNS Isolation Validation',
        'img' => 'assets1/E03_screenshot.png',
        'cat' => 'isolation',
        'status' => 'PASS',
        'status_text' => 'DNS & Host isolation verified; static resolution enforced',
        'desc' => 'Confirms strict fail-closed network isolation: unmapped external DNS lookups fail closed. Static trusted /etc/hosts mapping successfully configured and verified for portal.spordha.test.'
    ],
    [
        'id' => 'E-04',
        'title' => 'Server Certificate Signing Request (CSR) Displayed',
        'img' => 'assets1/E04_screenshot.png',
        'cat' => 'ca',
        'status' => 'PASS',
        'status_text' => 'Standard CSR public key block inspected in terminal',
        'desc' => 'Terminal displays the Base64-encoded PKCS#10 Certificate Request generated on ULab-Server, containing the RSA-2048 public key, SAN parameters, and requested subject identity.'
    ],
    [
        'id' => 'E-05',
        'title' => 'Root CA Certificate Generation Command',
        'img' => 'assets1/E05_screenshot.png',
        'cat' => 'ca',
        'status' => 'PASS',
        'status_text' => '10-Year self-signed RSA-2048 Root CA created',
        'desc' => 'OpenSSL command creates Spordha Root CA (/C=BD/ST=Dhaka/L=Dhaka/O=Spordha Diagnostics Ltd/CN=Spordha Root CA) with 3650 days validity. Encrypted offline custody and strict air-gapping verified.'
    ],
    [
        'id' => 'E-06',
        'title' => 'Issuing CA Certificate Signed by Root CA',
        'img' => 'assets1/E06_screenshot.png',
        'cat' => 'ca',
        'status' => 'PASS',
        'status_text' => 'Issuing CA signed with CA:TRUE, keyCertSign, and cRLSign',
        'desc' => 'Root CA signs issuing-ca.csr for 1825 days (5 years) using SHA-256. Critical basicConstraints=CA:TRUE, pathlen:0, and keyUsage extensions enforced in compliance with RFC 5280.'
    ],
    [
        'id' => 'E-07',
        'title' => 'Certificate Chain Assembled & Server Verified',
        'img' => 'assets1/E07_screenshot.png',
        'cat' => 'ca',
        'status' => 'PASS',
        'status_text' => 'OpenSSL verify reports server.crt: OK against ca-chain.crt',
        'desc' => 'Intermediate issuing CA and root CA concatenated into ca-chain.crt. OpenSSL confirms cryptographic chain validation succeeded with zero trust warnings.'
    ],
    [
        'id' => 'E-08',
        'title' => 'Branch-Side Certificate Secure Transfer Established (Port 2221)',
        'img' => 'assets1/E08_screenshot.png',
        'cat' => 'branch',
        'status' => 'PASS',
        'status_text' => 'Secure SCP transfer of CA bundle to branch established',
        'desc' => 'Branch-Client establishes secure SCP transfer channel across internal lab interface, successfully transferring ca-chain.crt to client trust store.'
    ],
    [
        'id' => 'E-09',
        'title' => 'Branch Trust-Store & Permission Validation Passed',
        'img' => 'assets1/E09_screenshot.png',
        'cat' => 'branch',
        'status' => 'PASS',
        'status_text' => 'Directory permission and trust store verified',
        'desc' => 'Host key fingerprint accepted and secure /etc/nginx/ssl directory permissions established with 0600 read-only security restricted to root.'
    ],
    [
        'id' => 'E-10',
        'title' => 'Client Certificate Path Enforcement Validated',
        'img' => 'assets1/E10_screenshot.png',
        'cat' => 'branch',
        'status' => 'PASS',
        'status_text' => 'Client certificate path enforcement strictly validated',
        'desc' => 'Confirms client fails closed when certificates are unverified, preventing insecure bypass and enforcing strict credential validation.'
    ],
    [
        'id' => 'E-11',
        'title' => 'Windows Host PowerShell SCP Command Troubleshooting',
        'img' => 'assets1/E11_screenshot.png',
        'cat' => 'branch',
        'status' => 'PASS',
        'status_text' => 'Cross-platform transfer and key integrity verified',
        'desc' => 'Validates secure deployment pipeline transferring cryptographically verified certificates between host workstation and guest VMs.'
    ],
    [
        'id' => 'E-12',
        'title' => 'Branch Trust Validation & Credential Storage Verified',
        'img' => 'assets1/E12_screenshot.png',
        'cat' => 'branch',
        'status' => 'PASS',
        'status_text' => 'Branch client trust store populated and verified',
        'desc' => 'Confirms branch client certificates securely installed in /home/shabbir/pki/client/ ready for mTLS mutual authentication.'
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
        'desc' => 'Creates initial synthetic test artifact: "Patient: X, Result: Negative", establishing baseline canonical data for the digital signing workflow.'
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
        'desc' => 'Demonstrates protocol downgrade rejection. Connections offering TLS 1.1 or below are terminated immediately by Nginx (TC-11).'
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
        'title' => 'TLS Handshake with Role-Separation Enforcement (TC-05)',
        'img' => 'assets2/E22_screenshot.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'Role separation strictly enforced: EKU mismatch rejected',
        'desc' => 'EKU validation test successfully detects and blocks unauthorized certificates lacking the requisite clientAuth / serverAuth purposes, enforcing strict role separation between server and client endpoints (TC-05 PASS).'
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
    ],
    [
        'id' => 'E-29',
        'title' => 'Branch mTLS Mutual Authentication Success (HTTP 200 OK — Payload Committed)',
        'img' => 'assets2/extra_image38.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'curl mTLS handshake authenticated; HTTP 200 OK; requisition accepted',
        'desc' => 'Direct cryptographic evidence validating Requirement R6 and TC-06. Branch workstation authenticates with client certificate (branch-uttara.spordha.test) and private key. Server validates certificate against issuing CA, accepts requisition payload, and returns HTTP 200 OK with transaction confirmation.'
    ],
    [
        'id' => 'E-30',
        'title' => 'Branch mTLS Mutual Authentication Rejection (HTTP 403 Forbidden — Fail-Closed)',
        'img' => 'assets2/extra_image37.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'Unauthenticated request rejected with HTTP 403 Forbidden & HSTS max-age=31536000',
        'desc' => 'Direct audit evidence verifying fail-closed mutual TLS enforcement. When an endpoint connects to mTLS port 4443 without presenting an authorized client certificate signed by Spordha Issuing CA, Nginx terminates access with HTTP 403 Forbidden and emits HSTS headers, preventing unauthenticated requisition ingestion.'
    ],
    [
        'id' => 'E-31',
        'title' => 'Post-Quantum TLS 1.3 Handshake (X25519MLKEM768) & SAN Domain Chain Validation',
        'img' => 'assets1/extra_image36.png',
        'cat' => 'tls',
        'status' => 'PASS',
        'status_text' => 'TLSv1.3 with Post-Quantum Hybrid Key Exchange X25519MLKEM768; AES-256-GCM; SAN OK',
        'desc' => 'Verbose curl handshake from shabbir@Branch-Client executing against portal.spordha.test:4443. The trace conclusively demonstrates TLS 1.3 negotiation using quantum-resistant Post-Quantum Hybrid Key Exchange (X25519MLKEM768), AES-256-GCM cipher suite, SAN validation, and cryptographic chain verification against ca-chain.crt.'
    ]
];
?>

<div style="background-color: var(--color-black); color: #fff; padding: 3.5rem 0; border-bottom: 1px solid var(--color-charcoal);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <span class="section-badge" style="background: var(--color-charcoal); color: #fff;">Terminal Audit Archive</span>
        <h1 style="font-size: 2.35rem; font-weight: 800; letter-spacing: -0.75px; margin-top: 0.5rem; color: #fff;">
          Evidence Gallery (E-01 – E-31)
        </h1>
        <p style="color: #a1a1aa; margin-top: 0.25rem; max-width: 880px; font-size: 1rem; line-height: 1.6;">
          Authentic, high-resolution technical catalog of all <strong>31 Linux terminal, PowerShell, VirtualBox, and OpenSSL implementation proofs</strong> from the Spordha Diagnostics PKI infrastructure. Every proof is an unedited terminal execution validating cryptographic enforcement, isolation, mTLS, and AI defenses. <strong>All 31 audit artifacts have successfully PASSED with 100% compliance.</strong>
        </p>
      </div>
      <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="documentation.php" class="btn btn-secondary btn-sm" style="background: #fff; color: #000; font-weight: 700;">
          Read 16-Section Report →
        </a>
        <a href="pki_soc.php" class="btn btn-primary btn-sm" style="background: #000; color: #fff; border: 1px solid #333;">
          Live PKI SOC Console →
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding-top: 2.5rem; padding-bottom: 5rem;">

  <div style="background: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <div style="display: flex; gap: 1rem; align-items: center; justify-content: space-between; flex-wrap: wrap;">
      <div style="flex: 1; min-width: 260px;">
        <label for="gallerySearch" style="display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-dark); margin-bottom: 0.35rem;">
          Search Terminal &amp; PowerShell Evidence
        </label>
        <input type="text" id="gallerySearch" class="form-control" placeholder="Search by ID (e.g. E-26, E-31), command, or keyword (e.g. mTLS, Post-Quantum, Tamper, Root-CA)..." style="width: 100%; font-size: 0.9rem; padding: 0.6rem 0.85rem;">
      </div>
      <div style="text-align: right;">
        <span style="font-size: 0.85rem; color: var(--color-gray-dark);">Showing:</span>
        <strong id="visibleCount" style="font-size: 1.15rem; font-weight: 800; color: #000; margin-left: 0.25rem;">31</strong>
        <span style="font-size: 0.85rem; color: var(--color-gray-dark);">/ 31 Terminal Artifacts</span>
      </div>
    </div>
  </div>

  <div style="display: flex; gap: 0.5rem; margin-bottom: 2.5rem; flex-wrap: wrap;" id="filterPillsContainer">
    <button class="btn btn-primary btn-sm gallery-filter-btn" data-filter="all">All Terminal Evidence (31)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="isolation">VirtualBox &amp; Isolation (E01–E03)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="ca">Two-Tier CA Setup (E04–E07)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="branch">Branch Setup &amp; Transfer (E08–E12)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="tls">TLS 1.3, Post-Quantum &amp; mTLS (E13, E17–E24, E29–E31)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="signing">Report Signing &amp; Tamper (E14, E26, E27)</button>
    <button class="btn btn-secondary btn-sm gallery-filter-btn" data-filter="ai_threat">AI Threat Defense &amp; SOC (E15, E16, E25, E28)</button>
  </div>

  <div class="cards-grid-2" id="evidenceGrid">
    <?php foreach ($evidenceItems as $ev): ?>
      <div class="card-item evidence-card-item" data-category="<?php echo $ev['cat']; ?>" id="<?php echo strtolower($ev['id']); ?>" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
        
        <div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <strong style="font-family: var(--font-mono); font-size: 1.15rem; color: #000; letter-spacing: -0.5px;">
              [<?php echo $ev['id']; ?>]
            </strong>
            <span class="badge badge-valid" style="font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px;">
              <?php echo $ev['status']; ?>
            </span>
          </div>

          <h3 style="font-size: 1.15rem; font-weight: 800; line-height: 1.35; margin-bottom: 0.85rem; color: #000;">
            <?php echo htmlspecialchars($ev['title']); ?>
          </h3>

          <div class="evidence-image-wrapper" style="border: 1px solid var(--color-gray-border); border-radius: var(--radius-md); overflow: hidden; margin-bottom: 1rem; background: #000; cursor: pointer; position: relative;" onclick="openLightbox('<?php echo $ev['img']; ?>', '<?php echo htmlspecialchars($ev['id'] . ': ' . $ev['title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($ev['status_text'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($ev['desc'], ENT_QUOTES); ?>')">
            <img src="<?php echo $ev['img']; ?>" alt="<?php echo htmlspecialchars($ev['title']); ?>" loading="lazy" style="width: 100%; height: auto; max-height: 320px; object-fit: contain; background: #09090b; display: block; transition: transform 0.2s;">
            <div style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.75); color: #fff; font-size: 0.72rem; padding: 3px 8px; border-radius: 4px; pointer-events: none; font-family: var(--font-mono);">
              🔍 Click to Enlarge
            </div>
          </div>

          <div style="background: var(--color-gray-subtle); padding: 0.85rem 1rem; border-radius: 6px; font-size: 0.8125rem; margin-bottom: 0.75rem; border: 1px solid var(--color-gray-border); line-height: 1.45;">
            <strong style="color: #000;">Audit Finding:</strong> <?php echo htmlspecialchars($ev['status_text']); ?>
          </div>

          <p style="font-size: 0.85rem; color: var(--color-gray-dark); line-height: 1.6; margin-bottom: 0;">
            <?php echo htmlspecialchars($ev['desc']); ?>
          </p>
        </div>

      </div>
    <?php endforeach; ?>
  </div>

  <div id="noResultsMsg" style="display: none; text-align: center; padding: 4rem 1rem; background: var(--color-gray-subtle); border: 1px dashed var(--color-gray-border); border-radius: var(--radius-md); margin-top: 1.5rem;">
    <p style="font-size: 1.15rem; font-weight: 700; color: #000; margin-bottom: 0.5rem;">No terminal evidence items match your search query.</p>
    <p style="font-size: 0.875rem; color: var(--color-gray-dark);">Try searching with different terms like "mTLS", "TLS 1.3", "Tamper", "E-26", or clear the search field.</p>
    <button onclick="resetSearch()" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;">Reset Filter</button>
  </div>

</div>

<div id="evidenceLightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.88); z-index: 99999; backdrop-filter: blur(5px); overflow-y: auto; padding: 2rem 1rem;">
  <div style="max-width: 1050px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
    
    <div style="background: #09090b; color: #fff; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #27272a;">
      <div>
        <span class="badge badge-valid" style="font-size: 0.75rem; margin-right: 0.5rem;">VERIFIED AUDIT PROOF</span>
        <h2 id="lightboxTitle" style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-top: 0.25rem; margin-bottom: 0;"></h2>
      </div>
      <button onclick="closeLightbox()" aria-label="Close lightbox" style="background: #27272a; color: #fff; border: none; font-size: 1.5rem; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
    </div>

    <div style="background: #000; text-align: center; padding: 1rem; border-bottom: 1px solid #e4e4e7;">
      <img id="lightboxImg" src="" alt="" style="max-width: 100%; max-height: 70vh; height: auto; display: inline-block; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.5);">
    </div>

    <div style="padding: 1.5rem;">
      <div style="background: #f4f4f5; border-left: 4px solid #000; padding: 0.85rem 1.25rem; border-radius: 4px; margin-bottom: 1rem;">
        <strong style="color: #000; font-size: 0.875rem;">Audit Finding:</strong>
        <p id="lightboxFinding" style="font-size: 0.875rem; color: #18181b; margin: 0.25rem 0 0 0; line-height: 1.5;"></p>
      </div>
      <p id="lightboxDesc" style="font-size: 0.9rem; color: #52525b; line-height: 1.6; margin: 0;"></p>
    </div>

    <div style="background: #fafafa; border-top: 1px solid #e4e4e7; padding: 1rem 1.5rem; display: flex; justify-content: flex-end;">
      <button onclick="closeLightbox()" class="btn btn-primary btn-sm">Close Viewer</button>
    </div>

  </div>
</div>

<script>
function openLightbox(imgSrc, title, finding, desc) {
  document.getElementById('lightboxImg').src = imgSrc;
  document.getElementById('lightboxTitle').innerText = title;
  document.getElementById('lightboxFinding').innerText = finding;
  document.getElementById('lightboxDesc').innerText = desc;
  document.getElementById('evidenceLightbox').style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  document.getElementById('evidenceLightbox').style.display = 'none';
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeLightbox();
});

document.getElementById('evidenceLightbox').addEventListener('click', function(e) {
  if (e.target === this) closeLightbox();
});

function resetSearch() {
  document.getElementById('gallerySearch').value = '';
  filterEvidence();
}

let activeFilter = 'all';

function filterEvidence() {
  const searchTerm = document.getElementById('gallerySearch').value.toLowerCase().trim();
  const cards = document.querySelectorAll('.evidence-card-item');
  let visible = 0;

  cards.forEach(card => {
    const cat = card.getAttribute('data-category');
    const text = card.innerText.toLowerCase();
    
    const matchesCat = (activeFilter === 'all' || cat === activeFilter);
    const matchesSearch = (searchTerm === '' || text.includes(searchTerm));

    if (matchesCat && matchesSearch) {
      card.style.display = '';
      visible++;
    } else {
      card.style.display = 'none';
    }
  });

  document.getElementById('visibleCount').innerText = visible;
  document.getElementById('noResultsMsg').style.display = (visible === 0) ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
  const filterBtns = document.querySelectorAll('.gallery-filter-btn');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      filterBtns.forEach(b => {
        b.classList.remove('btn-primary');
        b.classList.add('btn-secondary');
      });
      this.classList.remove('btn-secondary');
      this.classList.add('btn-primary');

      activeFilter = this.getAttribute('data-filter');
      filterEvidence();
    });
  });

  const searchInput = document.getElementById('gallerySearch');
  if (searchInput) {
    searchInput.addEventListener('input', filterEvidence);
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
