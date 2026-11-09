<?php
/**
 * Spordha Diagnostics Ltd. — Academic Project Report & Security Specification
 * Complete 16-section report + A1–A9 templates + evidence appendix (CSE487 Track 2).
 */

function docTable(array $headers, array $rows): string {
    $html = '<div class="table-responsive"><table class="data-table"><thead><tr>';
    foreach ($headers as $h) {
        $html .= '<th>' . $h . '</th>';
    }
    $html .= '</tr></thead><tbody>';
    foreach ($rows as $r) {
        $html .= '<tr>';
        foreach ($r as $c) {
            $html .= '<td>' . $c . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table></div>';
    return $html;
}

function callout(string $type, string $title, string $body): string {
    return '<div class="callout callout-' . $type . '"><strong>' . $title . '</strong>' . $body . '</div>';
}

function riskScore(string $s): string {
    if (strpos($s, 'Critical') !== false) { $c = 'risk-critical'; }
    elseif (strpos($s, 'High') !== false) { $c = 'risk-high'; }
    elseif (strpos($s, 'Medium') !== false) { $c = 'risk-medium'; }
    else { $c = 'risk-low'; }
    return '<span class="' . $c . '">' . $s . '</span>';
}

$pageTitle = "Academic Report & Security Specification (16 Sections)";
require_once __DIR__ . '/includes/header.php';
?>

<div class="doc-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="index.php">Home</a><span class="sep">/</span>
      <a href="documentation.php">Documentation</a><span class="sep">/</span>
      <span style="color:#fff;">Project Report</span>
    </nav>
    <span class="section-badge" style="background:#1f1f24; color:#fff;">East West University • CSE487 • Fall 2026</span>
    <h1 style="font-size:2.4rem; font-weight:800; letter-spacing:-0.75px; margin-top:0.5rem; color:#fff;">
      PKI &amp; Secure Communication — Full Project Report
    </h1>
    <p style="color:#a1a1aa; margin-top:0.4rem; max-width:860px; line-height:1.6;">
      Design, implementation evidence, security testing, and legal &amp; ethical analysis for
      <strong style="color:#fff;">Spordha Diagnostics Ltd.</strong> — a fictional academic case identity
      operating the reserved, lab-only <code style="color:#fff;">.spordha.test</code> namespace.
    </p>
    <div class="doc-meta-row">
      <span class="doc-meta-chip">📄 16 report sections</span>
      <span class="doc-meta-chip">🧾 Appendices A1–A9</span>
      <span class="doc-meta-chip">🧪 16 test cases (TC-01–16)</span>
      <span class="doc-meta-chip">🖼️ 28 evidence items (E-01–28)</span>
      <button onclick="window.print()" class="doc-meta-chip" style="cursor:pointer; background:#fff; color:#000; border-color:#fff;">🖨️ Print / Save PDF</button>
    </div>
  </div>
</div>

<div class="container">
  <div class="doc-layout">

    <aside class="doc-sidebar" aria-label="Report table of contents">
      <div class="doc-sidebar-heading">Report Sections</div>
      <ul class="doc-nav-list">
        <?php
        $toc = [
            'sec1' => '01 · Executive Summary',
            'sec2' => '02 · System &amp; Assets',
            'sec3' => '03 · Stakeholders',
            'sec4' => '04 · Threat Model &amp; Risks',
            'sec5' => '05 · AI-Driven Threat',
            'sec6' => '06 · Security Design',
            'sec7' => '07 · Standards &amp; Gaps',
            'sec8' => '08 · Design Decisions',
            'sec9' => '09 · Implementation',
            'sec10' => '10 · Testing &amp; Risk',
            'sec11' => '11 · Legal Analysis',
            'sec12' => '12 · Ethical Analysis',
            'sec13' => '13 · Interactions &amp; Innovation',
            'sec14' => '14 · Adaptation &amp; Reflection',
            'sec15' => '15 · Resources &amp; Conclusion',
            'sec16' => '16 · References',
        ];
        foreach ($toc as $id => $label) {
            $n = substr($label, 0, 2);
            echo '<li><a class="doc-nav-link" href="#' . $id . '"><span class="toc-num">' . $n . '</span>' . substr($label, 5) . '</a></li>';
        }
        ?>
      </ul>
      <div class="doc-sidebar-heading" style="margin-top:1rem;">Appendices</div>
      <ul class="doc-nav-list">
        <?php
        $app = [
            'a1' => 'A1 · Asset Inventory', 'a2' => 'A2 · Stakeholders', 'a3' => 'A3 · Risk Register',
            'a4' => 'A4 · Decision Record', 'a5' => 'A5 · Standards Gaps', 'a6' => 'A6 · Interactions',
            'a7' => 'A7 · Test Cases', 'a8' => 'A8 · Resources', 'a9' => 'A9 · Learning Log',
            'evidence' => 'B · Evidence E-01–28',
        ];
        foreach ($app as $id => $label) {
            $n = substr($label, 0, 2);
            echo '<li><a class="doc-nav-link" href="#' . $id . '"><span class="toc-num">' . $n . '</span>' . substr($label, 5) . '</a></li>';
        }
        ?>
      </ul>
    </aside>

    <div class="doc-content">

      <!-- ============ SECTION 1 ============ -->
      <article class="doc-section" id="sec1">
        <div class="doc-section-head">
          <span class="doc-section-num">S01</span>
          <div>
            <h2>Executive Summary</h2>
            <p>What was protected, the demonstrated controls, and the decisions management must take.</p>
          </div>
        </div>
        <p>
          <strong>Spordha Diagnostics Ltd.</strong> is a Dhaka-based diagnostic laboratory with a central laboratory,
          three sample-collection branches (Dhanmondi, Uttara, Mirpur), about 140 staff, ~40,000 patients,
          ~300 referring doctors and ~1,200 samples per day. The project goal is to design, build and test a small
          <strong>two-tier Public-Key Infrastructure (PKI)</strong> and the secure-communication controls that depend on it,
          so the patient portal, branch uploads and laboratory reports resist interception, impersonation, tampering and
          certificate misuse.
        </p>
        <div class="kv-grid">
          <div class="kv-card"><div class="kv-label">Problem solved</div><div class="kv-value">Shared branch passwords, self-signed portal cert, editable PDF reports, untracked expiry.</div></div>
          <div class="kv-card"><div class="kv-label">Core deliverable</div><div class="kv-value">Offline Root CA → Online Issuing CA → purpose-scoped leaf certificates.</div></div>
          <div class="kv-card"><div class="kv-label">Standards baseline</div><div class="kv-value">RFC 5280, RFC 9846/8446, NIST SP 800-52/57, OWASP, CCA.</div></div>
          <div class="kv-card"><div class="kv-label">Legal baseline</div><div class="kv-value">Cyber Security Act 2026, PDPA 2026, ICT Act 2006.</div></div>
        </div>
        <?php echo callout('success', 'Demonstrated security controls', '<ul style="margin-left:1.25rem;margin-top:0.4rem;">
          <li>Isolated VirtualBox lab with host-only networking (E-01, E-02).</li>
          <li>Offline Root CA certifying an online Issuing CA (E-05, E-06) with a validated chain (E-07).</li>
          <li>Portal TLS 1.3 handshake with verified certificate and HSTS (E-17).</li>
          <li>Upload endpoint rejecting a missing client certificate (E-20).</li>
          <li>Detached SHA-256 signature that detects a modified report (E-26, E-27).</li>
          <li>AI voice-clone key-export request blocked under dual approval (E-15, E-16, E-28).</li>
        </ul>'); ?>
        <?php echo callout('warning', 'Overall assessment', 'The prototype evidence is <strong>partial</strong>. It establishes chain validation, a TLS 1.3 portal connection with HSTS, rejection of a missing client certificate, and text-file tamper detection. It does <em>not</em> yet demonstrate all 13 required tests, an offline root in the screenshots, correct branch mTLS with a real client certificate, effective CRL/OCSP revocation enforcement, automated expiry alerts, or a dedicated PDF-signing certificate. The corrected design closes these gaps.'); ?>
        <?php echo callout('note', 'Priority decisions for the team', 'Standardize on <strong>Spordha Diagnostics Ltd.</strong> and <code>.spordha.test</code>; replace the unencrypted RSA-2048 root key and take the root VM offline; complete and record the 13 minimum tests with correct certificates and revocation status; use a dedicated document-signing key for PDF reports; and complete the legal/ethical approval record before submission.'); ?>
      </article>

      <!-- ============ SECTION 2 ============ -->
      <article class="doc-section" id="sec2">
        <div class="doc-section-head">
          <span class="doc-section-num">S02</span>
          <div>
            <h2>System, Diagrams &amp; Assets</h2>
            <p>Real system, isolated lab copy, trust boundaries and the A1 asset inventory.</p>
          </div>
        </div>
        <p>
          The target system comprises a patient portal, doctor portal, branch upload service, laboratory information
          management system (LIMS), report server and remote administration. It holds sensitive health data and may store
          phone numbers and NID information for some corporate clients. The most important security properties are
          <strong>confidentiality</strong> of patient data, <strong>integrity &amp; provenance</strong> of results,
          <strong>availability</strong> of emergency reports, and <strong>accountability</strong> for certificate and
          signing-key use.
        </p>
        <div class="cards-grid-2" style="margin:1.5rem 0;">
          <div class="diagram-preview-card">
            <img src="assets1/D1_target_architecture.png" alt="Target architecture D1">
            <div class="diagram-caption"><div><h4>Figure 1 (D1): Target Architecture</h4><p>Trust boundaries across users, DMZ, core lab and PKI.</p></div></div>
          </div>
          <div class="diagram-preview-card">
            <img src="assets1/D3_virtual_lab_topology.png" alt="Lab topology D3">
            <div class="diagram-caption"><div><h4>Figure 2 (D3): Isolated Lab Topology</h4><p>Host-only network: Root-CA, Issuing-CA, ULab-Server, Branch-Client.</p></div></div>
          </div>
        </div>
        <div class="diagram-preview-card" style="margin:1.5rem 0;">
          <img src="assets1/D7_trust_boundaries_stride.png" alt="Trust boundaries D7">
          <div class="diagram-caption"><div><h4>Figure 3 (D7): Trust-Boundary Map &amp; STRIDE</h4><p>STRIDE review points attached to each boundary.</p></div></div>
        </div>
        <h3>Key trust boundaries</h3>
        <?php echo docTable(
            ['Boundary', 'Crossing point', 'Required control'],
            [
                ['TB-1 · user/WAN', 'Public users &amp; doctors → portal', 'HTTPS, server-identity validation, MFA/session controls, RBAC, rate limiting.'],
                ['TB-2 · branch/DMZ', 'Branch client → upload API', 'mTLS, unique device cert, EKU=clientAuth, SAN/device mapping, CRL/OCSP, audit log.'],
                ['TB-3 · DMZ/core', 'Nginx/app → LIMS &amp; report store', 'Service authentication, least-privilege accounts, network ACLs, encryption at rest &amp; in transit.'],
                ['TB-4 · PKI', 'Root/issuing CA → systems', 'Offline root, protected CA key, approved issuance, certificate inventory, signed revocation data.'],
                ['TB-5 · operators', 'Human operator → privileged workflows', 'Named accounts, role separation, two-person approval, known-number callback, immutable audit.'],
            ]
        ); ?>
        <h3 id="a1">Appendix A1 — Asset inventory (A-01 … A-18)</h3>
        <?php echo docTable(
            ['ID', 'Asset', 'Type / location', 'Owner', 'C / I / A', 'Protection'],
            [
                ['A-01', 'Offline root private key', 'Key; offline CA VM', 'PKI lead', 'H / H / M', 'Encrypted offline custody; existing E-05 key used <code>-nodes</code> → replace.'],
                ['A-02', 'Root CA certificate', 'Certificate; offline CA', 'PKI lead', 'L / H / M', 'Fingerprint inventory; distribute public cert to lab trust stores only.'],
                ['A-03', 'Issuing CA private key', 'Key; issuing CA VM', 'PKI operator', 'H / H / H', 'TPM/token or protected software token; two-person access.'],
                ['A-04', 'Issuing CA certificate', 'Certificate; CA service', 'PKI operator', 'L / H / H', 'Root-signed; pathLen=0; track expiry/status.'],
                ['A-05', 'Portal TLS private key', 'Key; portal server', 'IT officer', 'H / H / H', 'Separate key; restricted permissions/TPM.'],
                ['A-06', 'Portal TLS certificate', 'Certificate; Nginx', 'IT officer', 'L / H / H', 'SAN <code>portal.spordha.test</code>, serverAuth.'],
                ['A-07', 'Upload TLS private key', 'Key; upload server', 'IT officer', 'H / H / H', 'Separate from portal and branch keys.'],
                ['A-08', 'Upload TLS certificate', 'Certificate; Nginx', 'IT officer', 'L / H / H', 'SAN <code>upload.spordha.test</code>, serverAuth.'],
                ['A-09', 'Branch client private keys', 'Key; each branch PC', 'Branch owner / IT', 'H / H / M', 'Unique per device; no shared key.'],
                ['A-10', 'Branch client certificates', 'Certificate; branch devices', 'IT officer', 'L / H / M', 'clientAuth EKU, branch SAN, revocation status.'],
                ['A-11', 'Pathologist signing keys', 'Key; signing service', 'Pathologist + PKI lead', 'H / H / M', 'Distinct from TLS key.'],
                ['A-12', 'Document-signing certificates', 'Certificate; signer store', 'PKI lead', 'L / H / M', 'Signer identity, expiry, status, timestamp.'],
                ['A-13', 'CRL / OCSP response data', 'Revocation data; status service', 'PKI operator', 'M / H / H', 'Signed, fresh, monitored, backed up.'],
                ['A-14', 'Patient result data', 'Clinical records; LIMS', 'Data controller', 'H / H / H', 'Role-based access, encryption, retention.'],
                ['A-15', 'PDF medical reports', 'Documents; report store', 'Pathologist / LIMS', 'H / H / H', 'PAdES signature, immutable version.'],
                ['A-16', 'CA &amp; access audit logs', 'Logs; protected store', 'Security officer', 'M / H / H', 'Append-only, time-synchronised, restricted.'],
                ['A-17', 'Certificate inventory / approvals', 'Database / workflow', 'PKI lead', 'M / H / H', 'Owner, serial, purpose, dates, approvals.'],
                ['A-18', 'Encrypted branch upload queue', 'Data; branch device', 'Branch manager', 'H / H / H', 'Encryption at rest, integrity, revalidation.'],
            ]
        ); ?>
      </article>

      <!-- ============ SECTION 3 ============ -->
      <article class="doc-section" id="sec3">
        <div class="doc-section-head">
          <span class="doc-section-num">S03</span>
          <div>
            <h2>Stakeholders &amp; Conflicts</h2>
            <p>Needs, conflicts and how the design resolves each one (A2).</p>
          </div>
        </div>
        <?php echo docTable(
            ['Stakeholder', 'Main needs', 'Conflict / response'],
            [
                ['Patients (~40,000)', 'Private results, easy mobile access, genuine reports.', 'Authentication friction vs. access → clear recovery and patient-friendly verification.'],
                ['Referring doctors (~300)', 'Fast access and simple verification of forwarded reports.', 'Extra validation steps vs. speed → signatures visible in ordinary PDF readers + one-page guide.'],
                ['Pathologists (12)', 'Fast signing, control of their identity, traceable approvals.', 'Emergency throughput vs. key protection → one key per signer, short sessions, emergency countersigning.'],
                ['Technicians (~70)', 'Reliable LIMS workflow and correct result attribution.', 'Speed vs. separation of duties → role-based approval, immutable result history.'],
                ['Branch / front-desk staff', 'Uploads that work on unstable links with clear errors.', 'mTLS failures vs. continuity → per-device certs, offline encrypted queue, runbook.'],
                ['IT officers (2)', 'Manageable renewal, monitoring and recovery at any hour.', 'High security vs. limited staffing → automation, expiry alerts, documented ceremonies.'],
                ['Management', 'Low cost, no outage, legal and reputational protection.', 'Budget vs. secure key custody → prioritise root CA, signing key and patient TLS.'],
                ['Employers / corporate clients', 'Reliable way to verify medical reports.', 'Convenient forwarded copies vs. trust → report-ID verification without over-exposure.'],
                ['Regulators / law enforcement', 'Lawful processing, accountable access, usable evidence.', 'Confidentiality vs. disclosure → verify authority, disclose minimum, retain audit trail.'],
            ]
        ); ?>
        <h3 id="a2">Key conflicts and design response</h3>
        <?php echo docTable(
            ['Conflict', 'Why it matters', 'Resolution'],
            [
                ['Availability vs. revocation checking', 'A branch link can fail while emergency reports are needed.', 'Fail closed for new uploads; encrypt and queue locally, revalidate after connectivity returns.'],
                ['Usability vs. mTLS', 'Staff face upload failures when device certs expire.', '30/14/7-day warnings, clear error codes, rapid reissue workflow.'],
                ['Low cost vs. key assurance', 'A small lab has no HSM and only two IT officers.', 'Offline encrypted root, TPM-backed issuing key, restricted software-token fallback.'],
                ['Fast release vs. signing accountability', 'A compromised signer key can delay emergency reports.', 'Revoke suspect key, reissue under a second pathologist, flag and re-verify the window.'],
            ]
        ); ?>
      </article>

      <!-- ============ SECTION 4 ============ -->
      <article class="doc-section" id="sec4">
        <div class="doc-section-head">
          <span class="doc-section-num">S04</span>
          <div>
            <h2>Threat Model &amp; Risk Register</h2>
            <p>STRIDE across every boundary, two attack trees, and the A3 risk register.</p>
          </div>
        </div>
        <?php echo docTable(
            ['STRIDE', 'Example in this system', 'Main control'],
            [
                ['Spoofing', 'Impersonate portal, branch device, pathologist or IT officer.', 'Trusted CA chain, SAN validation, mTLS, MFA, callback.'],
                ['Tampering', 'Modify uploads, a result record or a released report.', 'TLS integrity, signed PDFs, role separation, content hashes.'],
                ['Repudiation', 'Deny issuing/revoking a certificate or approving a report.', 'Signed audit events, named accounts, two-person approval.'],
                ['Information disclosure', 'Expose patient results, NID, CA keys or signing keys.', 'Encryption, least privilege, key separation, secure backups.'],
                ['Denial of service', 'Branch-link loss, expiry, revocation outage or TLS mismatch.', 'Encrypted local queue, monitoring, recovery, tested failover.'],
                ['Elevation of privilege', 'Branch device or operator gains CA/signing authority.', 'EKU &amp; role restrictions, separate CA/signing roles, approval.'],
            ]
        ); ?>
        <h3 id="a3">Appendix A3 — Risk register</h3>
        <?php
        $risks = [
            ['T-01', 'Wrongly issued trusted portal certificate', '4', '5', riskScore('20 · Critical'), 'Dual approval, SAN allow-list, issuance log/alert; TC-04.'],
            ['T-02', 'Root or issuing-CA private-key theft', '4', '5', riskScore('20 · Critical'), 'Offline encrypted root, TPM/token for issuing CA, revoke/rebuild; compromise exercise.'],
            ['T-03', 'Expired or revoked certificate still accepted', '4', '5', riskScore('20 · Critical'), 'CRL/OCSP hard-fail for new uploads; expiry warning; TC-08/09/10.'],
            ['T-04', 'Protocol downgrade or weak cipher', '3', '5', riskScore('15 · High'), 'TLS 1.3 by default, TLS 1.0/1.1 disabled, AEAD-only; TC-11/12.'],
            ['T-05', 'Untrusted branch device intercepts upload data', '4', '4', riskScore('16 · High'), 'mTLS, unique clientAuth cert, identity mapping; TC-06/07.'],
            ['T-06', 'Forged or modified PDF appears genuine', '4', '5', riskScore('20 · Critical'), 'Dedicated signer key, PAdES signature, recipient verification; TC-13/14.'],
            ['T-07', 'AI-assisted impersonation causes key export/rogue issuance', '4', '5', riskScore('20 · Critical'), 'Callback, two-person approval, non-exportable key, alerts; TC-15.'],
            ['T-08', 'Branch-link outage prevents time-critical upload', '4', '4', riskScore('16 · High'), 'Encrypted local queue, retry after revalidation; availability test.'],
            ['T-09', 'Certificate hostname mismatch / wrong EKU', '3', '4', riskScore('12 · High'), 'SAN allow-list, EKU/keyUsage profile enforcement; TC-03/05.'],
        ];
        echo docTable(['ID', 'Threat / asset', 'L', 'I', 'Score', 'Planned control &amp; test'], $risks);
        ?>
        <h3>Attack trees</h3>
        <p><strong>Goal A — obtain a trusted portal certificate:</strong></p>
        <ul>
          <li><strong>Path A:</strong> compromise the root/issuing key → sign an unauthorised SAN. <em>Mitigate:</em> offline root, TPM/token issuing key, two-person release, out-of-policy alert.</li>
          <li><strong>Path B:</strong> trick an operator into issuing a certificate for the attacker’s name. <em>Mitigate:</em> name allow-list, independent request-owner check, SAN validation, approval ticket.</li>
          <li><strong>Path C:</strong> exploit a client that skips validation or accepts a hostname mismatch. <em>Mitigate:</em> strict chain &amp; SAN validation, no “click through”, hostname-mismatch test.</li>
        </ul>
        <p><strong>Goal B — make a forged report look genuine:</strong></p>
        <ul>
          <li><strong>Path A:</strong> steal/reuse the report-signing key. <em>Mitigate:</em> dedicated signer key, protected custody, short validity, revocation.</li>
          <li><strong>Path B:</strong> alter a result before signing via a compromised account. <em>Mitigate:</em> separation of duties, approval, immutable logs, re-authentication at signing.</li>
          <li><strong>Path C:</strong> change the PDF after release or reuse a genuine signature block. <em>Mitigate:</em> PDF signature over the final document, document ID, signer identity, timestamp.</li>
        </ul>
        <?php echo callout('evidence', 'Evidence limitation', 'The supplied signature screenshots prove a detached signature over a <em>text file</em> and detect a later edit. They do not prove that a PDF has been signed with a dedicated document-signing certificate, or that the signer’s certificate was valid and unrevoked at signing time.'); ?>
      </article>

      <!-- ============ SECTION 5 ============ -->
      <article class="doc-section" id="sec5">
        <div class="doc-section-head">
          <span class="doc-section-num">S05</span>
          <div>
            <h2>AI-Driven Threat &amp; Impersonation Defense</h2>
            <p>Cloned voices, deepfakes and AI-written emails — analysis, controls and test.</p>
          </div>
        </div>
        <p>
          The scenario describes a caller who sounded like the Managing Director and asked an IT officer to send server
          certificate files. A voice clone or AI-written email can create urgency, mimic authority and make an unauthorised
          issuance, revocation or key-export request look legitimate. Authentication of the requester must therefore use a
          <strong>trusted channel independent of the call or email itself</strong>.
        </p>
        <div class="cards-grid-2" style="margin:1.5rem 0;">
          <div class="diagram-preview-card">
            <img src="assets1/D6_ai_impersonation_attack_tree.png" alt="AI impersonation attack tree D6">
            <div class="diagram-caption"><div><h4>Figure 4 (D6): AI-impersonation attack tree</h4><p>Prevention and detection controls.</p></div></div>
          </div>
          <div>
            <?php echo docTable(
                ['Control layer', 'Required action'],
                [
                    ['Identity verification', 'Call back on a stored directory number; confirm via a second channel.'],
                    ['Dual control', 'Issuance, revocation, backup/restore need two named approvers.'],
                    ['Key protection', 'Keys never sent by email/chat/share/AI; non-exportable where TPM exists.'],
                    ['Least privilege', 'Separate requester, approver, CA operator, report approver, auditor.'],
                    ['Detection', 'Monitor mailbox for every issuance/revocation/key-access; append-only log.'],
                    ['Emergency continuity', 'Break-glass path: two approvers, narrow scope, short expiry, post-event review.'],
                ]
            ); ?>
          </div>
        </div>
        <?php echo callout('warning', 'Role-play evidence &amp; interpretation', 'E-15/E-16 show a fabricated “CEO” message asking for the server private key “immediately” and an IT-officer reply denying it (dual approval required); E-28 shows a simulated blocked incident record. This supports a written awareness/role-play control — <strong>not</strong> enforced email-gateway blocking, hardware non-exportability, or a real immutable log. E-28 also mentions “HSM procedures” although the scenario has no HSM; the final report must state the actual TPM/software-token design.'); ?>
        <h3>Test case TC-15</h3>
        <?php echo docTable(
            ['ID', 'Stimulus', 'Expected result', 'Evidence status'],
            [['TC-15', 'Urgent unverified caller/email asks for a CA/server key or requests issuance/revocation without the second approver.', 'Operator refuses; no export, no certificate action; alert + incident record created.', 'PARTIAL — role-play text only (E-15, E-16, E-28). Run a controlled workflow test and capture audit output.']]
        ); ?>
      </article>

      <!-- ============ SECTION 6 ============ -->
      <article class="doc-section" id="sec6">
        <div class="doc-section-head">
          <span class="doc-section-num">S06</span>
          <div>
            <h2>Security Design</h2>
            <p>Two-tier PKI, certificate policy, TLS, mTLS, revocation, PDF signing and key management.</p>
          </div>
        </div>
        <h3>Certificate policy</h3>
        <?php echo docTable(
            ['Certificate type', 'Key &amp; profile', 'Validity', 'Approval / intended use'],
            [
                ['Root CA', 'RSA-4096+; SHA-384+; CA:TRUE, pathLen=1; keyCertSign + cRLSign.', '10 years', 'Offline ceremony; signs only the issuing CA; encrypted backup held separately.'],
                ['Issuing CA', 'RSA-3072+; CA:TRUE, pathLen=0; keyCertSign + cRLSign.', '5 years', 'Two-person governance; issues only allow-listed leaf profiles; logs every action.'],
                ['Portal TLS server', 'ECDSA P-256 / RSA-3072; SAN <code>portal.spordha.test</code>; EKU serverAuth.', '90 days', 'Service owner + IT/PKI approval; portal TLS only.'],
                ['Upload TLS server', 'ECDSA P-256 / RSA-3072; SAN <code>upload.spordha.test</code>; EKU serverAuth.', '90 days', 'Upload-service owner + IT/PKI approval.'],
                ['Branch device', 'ECDSA P-256 / RSA-3072; unique SAN/device; EKU clientAuth.', '90 days', 'Branch manager confirms device + IT approves; one certificate per device.'],
                ['Report signing', 'EC P-256 / RSA-3072; digitalSignature/contentCommitment; no TLS EKU.', '1 year', 'Pathologist identity verified; individual key preferred for attribution.'],
            ]
        ); ?>
        <h3>TLS configuration policy</h3>
        <ul>
          <li>Prefer <strong>TLS 1.3</strong> for portal and upload; disable TLS 1.0/1.1; permit TLS 1.2 only with strong AEAD suites and a documented exception.</li>
          <li>Serve the full chain, validate hostnames via SAN, redirect HTTP→HTTPS, set HSTS after confirming HTTPS-only operation.</li>
          <li>Use TLS 1.3 AEAD suites (AES-GCM / ChaCha20-Poly1305); disable compression and obsolete ciphers; keep OpenSSL/Nginx patched.</li>
          <li>Isolate the legacy branch PC on a dedicated, restricted endpoint — do not weaken the patient-facing portal.</li>
        </ul>
        <h3>Mutual TLS for branch uploads</h3>
        <div class="cards-grid-2" style="margin:1rem 0;">
          <div class="diagram-preview-card">
            <img src="assets1/D4_mtls_branch_upload_flow.png" alt="mTLS flow D4">
            <div class="diagram-caption"><div><h4>Figure 6 (D4): mTLS upload transaction</h4><p>Validate identity &amp; authorization before committing payload.</p></div></div>
          </div>
          <div class="diagram-preview-card">
            <img src="assets1/D5_clinical_report_signing_workflow.png" alt="Report signing D5">
            <div class="diagram-caption"><div><h4>Figure 7 (D5): Signed-report workflow</h4><p>PAdES signature over the final PDF with a dedicated key.</p></div></div>
          </div>
        </div>
        <p>
          Each branch device receives its own client certificate. The upload server validates chain, time validity, EKU,
          SAN/device identifier and revocation status, then maps the device to a single branch. Missing, untrusted, expired,
          revoked or wrong-type certificates are rejected and logged; the body is never stored on rejection. A server
          certificate must never be reused as a client certificate (the mistake visible in E-22).
        </p>
        <h3>Revocation, renewal and expiry</h3>
        <ul>
          <li>Maintain a certificate inventory: serial, subject/SAN, issuer, fingerprint, type/EKU, owner, dates, status, renewal contact.</li>
          <li>Publish a signed CRL and/or OCSP; target a 15-minute revocation SLA; verify the server/client actually checks status.</li>
          <li>Fail closed for branch identity when status cannot be checked; queue approved payloads in encrypted local storage and revalidate before retry.</li>
          <li>Alert owners at 30, 14 and 7 days before expiry; a shell echo is not an automated scheduler.</li>
        </ul>
        <h3>PDF signing and verification</h3>
        <p>
          The LIMS creates the final PDF only after pathologist approval. A dedicated document-signing key signs it using a
          PDF-aware profile such as PAdES; the chain, certificate status and a trusted timestamp are included or retrievable.
          Any modification, unknown signer, expired/revoked certificate or missing validation evidence is flagged. Clinical
          reports must not be signed with a web-server TLS key.
        </p>
        <h3>Key management</h3>
        <?php echo docTable(
            ['Key class', 'Creation &amp; storage', 'Backup / rotation / compromise'],
            [
                ['Root CA key', 'Offline Root-CA VM; encrypted storage; passphrase controlled separately; no <code>-nodes</code>.', 'Offline encrypted backup under two-person custody. On compromise: distrust root, rebuild, reissue, notify.'],
                ['Issuing CA key', 'CA host; TPM/token or restricted software token; two-person issuance.', 'Encrypted offline backup if policy permits. On compromise: revoke at root, replace issuing CA, reissue leaves.'],
                ['Portal/upload TLS keys', 'Unique key per service; restricted permissions or TPM.', 'Automated renewal with overlap. On compromise: replace, revoke old cert, investigate logs.'],
                ['Branch client keys', 'Unique key per device; TPM/secure store or encrypted file.', 'Do not copy from server. Revoke lost device, issue replacement after approval.'],
                ['Report-signing keys', 'Separate key per pathologist; protected signer environment.', 'On suspicion: suspend/revoke, identify window, re-sign/re-verify, inform relying parties.'],
            ]
        ); ?>
      </article>

      <!-- ============ SECTION 7 ============ -->
      <article class="doc-section" id="sec7">
        <div class="doc-section-head" id="a5">
          <span class="doc-section-num">S07</span>
          <div>
            <h2>Standards &amp; Gaps</h2>
            <p>Comparison against RFCs, NIST, OWASP and CCA guidance (A5).</p>
          </div>
        </div>
        <?php echo docTable(
            ['Standard / guide', 'How applied', 'Gap / resolution'],
            [
                ['RFC 5280 — X.509 &amp; CRL', 'Basic constraints, key usage, EKU, SAN, CRL distribution, path validation.', 'Screenshot CA profile shows only CA:TRUE + keyCertSign/cRLSign. Add pathLen, SKI/AKI, leaf EKUs/SAN, policies; test key-purpose rejection.'],
                ['RFC 9846 — TLS 1.3', 'TLS 1.3 default for portal and upload; TLS 1.0/1.1 excluded.', 'RFC 9846 (Jul 2026) obsoletes RFC 8446. Update references; confirm OpenSSL support and the observed <code>X25519MLKEM768</code> string.'],
                ['NIST SP 800-52 Rev. 2', 'TLS configuration benchmark.', 'Under review (planning note 7 May 2026); guidance for US federal systems — apply as benchmark, monitor successor.'],
                ['NIST SP 800-57 Pt. 1 Rev. 5', 'Key lifecycle: generation, protection, backup, rotation, revocation.', 'Supplied root command uses <code>-nodes</code> and RSA-2048. Replace with encrypted, protected key material and documented ceremonies.'],
                ['OWASP TLS Cheat Sheet', 'TLS 1.3 preferred; TLS 1.0/1.1 disabled; strong AEAD; no weak/anonymous suites.', 'Legacy PC is a usability issue — isolate a temporary endpoint rather than weakening the primary endpoint.'],
                ['Bangladesh CCA policies', 'Root CA Certificate Policy 2023; PKI audit/licensing guidance.', 'An internal lab CA is not automatically a CCA-licensed CA; external statutory recognition needs separate legal assessment.'],
                ['GDPR Art. 9 / 32', 'Benchmark for health data, risk-based security and breach response.', 'Applies only if EU territorial/material scope is met; assess establishment/offering/monitoring triggers.'],
                ['HIPAA Security Rule', 'Comparative benchmark for access, audit, integrity, authentication, transmission security.', 'Applies to US covered entities/business associates, not automatically to a fictional Bangladesh lab.'],
            ]
        ); ?>
        <?php echo callout('note', 'Standards conflicts resolved', 'The brief names RFC 8446, which RFC 9846 obsoletes — the report cites both. Public-Web-PKI (CA/Browser Forum) lifetime rules are not automatically binding on a closed <code>.test</code> CA; the team adopts short-lived 90-day leaves as an internal risk decision. The old branch PC is isolated or upgraded rather than enabling obsolete TLS on the public portal. Fail-closed revocation is preserved through an encrypted local queue, not by accepting unverified identities.'); ?>
      </article>

      <!-- ============ SECTION 8 ============ -->
      <article class="doc-section" id="sec8">
        <div class="doc-section-head" id="a4">
          <span class="doc-section-num">S08</span>
          <div>
            <h2>Design Decisions &amp; Trade-offs</h2>
            <p>Recorded choices, alternatives and residual risk (A4).</p>
          </div>
        </div>
        <?php echo docTable(
            ['ID', 'Decision', 'Alternatives', 'Choice &amp; rationale', 'Residual risk / test'],
            [
                ['D-01', 'TLS compatibility', 'TLS 1.3 only; TLS 1.2 with strong suites; legacy TLS on main endpoint.', 'TLS 1.3 by default; isolate the old PC on a restricted endpoint or upgrade it.', 'Old PC may fail until upgraded; TC-11/12.'],
                ['D-02', 'Revocation outage', 'Fail open; fail closed; fail closed + encrypted queue.', 'Fail closed for branch identity; queue locally and revalidate before retry.', 'Queue capacity/expiry risk; TC-09 + link-loss test.'],
                ['D-03', 'Certificate lifetime', 'Long-lived; 90-day leaves; very short-lived auto-renewal.', '90-day service/branch leaves with 30/14/7-day warnings; adopt ACME only after tests.', 'Renewal workload for two IT officers; expiry/rotation tests.'],
                ['D-04', 'Report signer key', 'One organisational key; individual pathologist keys.', 'Individual keys preferred for accountability on employment/visa reports.', 'Individual key lifecycle burden; TC-13/14.'],
                ['D-05', 'Root custody', 'Always-online root; offline root with manual ceremony.', 'Offline root; the screenshot state is non-compliant (root shown running, key unencrypted).', 'Ceremony availability and backup custody; re-demo powered-off root.'],
                ['D-06', 'Branch identity', 'Shared password; shared cert; unique device certificate.', 'One unique client certificate per device, mapped to a branch; least privilege.', 'Device replacement adds provisioning work; TC-06/07/08/09.'],
            ]
        ); ?>
      </article>

      <!-- ============ SECTION 9 ============ -->
      <article class="doc-section" id="sec9">
        <div class="doc-section-head">
          <span class="doc-section-num">S09</span>
          <div>
            <h2>Implementation</h2>
            <p>Environment, build sequence evidenced and the corrected sequence.</p>
          </div>
        </div>
        <h3>Environment and build sequence evidenced</h3>
        <?php echo docTable(
            ['Stage', 'Observed action', 'Assessment'],
            [
                ['Virtual machines', 'VirtualBox lists Root-CA, Issuing-CA, ULab-Server, Branch-Client (E-01).', 'Environment created; root shown running, so offline state not evidenced.'],
                ['Network', 'ULab-Server Adapter 2 selects a host-only adapter (E-02).', 'Host-only adapter evidenced; verify every VM NIC.'],
                ['Server CSR', '<code>server.csr</code> displayed (E-04).', 'CSR exists; no private key disclosed.'],
                ['Root CA', 'OpenSSL self-signed RSA-2048, 3,650-day root with <code>-nodes</code> (E-05).', 'CA creation evidenced but key protection weak; root not offline.'],
                ['Issuing CA', 'Issuing CA signed by root, 1,825 days, SHA-256, CA usages (E-06).', 'Signature evidenced; add pathLen and fuller policy extensions.'],
                ['Chain', 'CA files copied to Nginx; <code>openssl verify</code> returns OK (E-07).', 'Positive chain validation; repeat hostname/purpose verification.'],
                ['Branch distribution', 'SCP/curl attempts fail on path/trust-store errors (E-08–E-12).', 'File delivery/trust-store setup not completed; fix transfer procedure.'],
                ['TLS portal', 'TLS 1.3 verifies cert; HSTS returned; HTTP 403 (E-17).', 'Positive HTTPS proof; 403 is a directory-index/config issue.'],
                ['mTLS', 'Upload endpoint rejects missing client certificate (E-20).', 'Positive refusal; valid branch certificate test still missing.'],
                ['Report integrity', 'Detached SHA-256 signature verifies/detects edit (E-26–E-27).', 'Proof-of-concept only; wrong key purpose and wrong artifact for R8.'],
                ['Expiry / AI', 'Expiry message and incident log printed as shell/text (E-15/16/25/28).', 'Procedures demonstrated as text, not automated enforcement.'],
            ]
        ); ?>
        <h3>Corrected implementation sequence</h3>
        <ol>
          <li>Create the offline root CA with an encrypted key and policy-approved size; document the ceremony; shut the root VM down after signing the issuing CA.</li>
          <li>Create the issuing CA (CA:TRUE, pathLen=0, keyCertSign, cRLSign); protect its key with TPM/token where available; log and dual-approve all operations.</li>
          <li>Generate independent key pairs/CSRs for portal, upload, each branch device and each signer; apply SAN, keyUsage and EKU profiles.</li>
          <li>Install the correct chain, server certificates and revocation material; verify chain + hostname; configure TLS 1.3, HSTS, redirect and mandatory mTLS.</li>
          <li>Issue unique branch certificates; validate a real successful upload, then missing/untrusted/expired/revoked/wrong-type tests.</li>
          <li>Sign a synthetic PDF with a dedicated report-signing certificate; verify the original, modify a copy, revoke a test cert, check the verifier.</li>
        </ol>
        <?php echo callout('note', 'Project identity &amp; hostname standard', 'The project consistently uses <strong>Spordha Diagnostics Ltd.</strong> with <code>portal.spordha.test</code>, <code>upload.spordha.test</code> and <code>branch-01.spordha.test</code> in the reserved <code>.test</code> namespace — intended only for the isolated student lab, with no claim that a real organisation or production domain is represented.'); ?>
      </article>

      <!-- ============ SECTION 10 ============ -->
      <article class="doc-section" id="sec10">
        <div class="doc-section-head">
          <span class="doc-section-num">S10</span>
          <div>
            <h2>Testing &amp; Remaining Risk</h2>
            <p>Demonstrated vs. outstanding, and the A7 test matrix.</p>
          </div>
        </div>
        <p>
          The brief requires at least 13 tests across chain/trust, wrong issuance, expiry/revocation, downgrade, report
          signing and the AI-driven threat. A test marked PARTIAL or INCONCLUSIVE must not be counted as a complete passing
          test for the final rubric. The full interactive matrix (TC-01 … TC-16) is available in the
          <a href="pki_soc.php#tab-test-matrix" style="font-weight:700;color:#000;">PKI Security Operations Center</a>.
        </p>
        <?php echo docTable(
            ['Evidence-backed narrow results', 'Outstanding before full test coverage'],
            [
                ['Chain validation and portal cert accepted for the shown name (E-07/E-17).', 'Untrusted CA, hostname mismatch, wrong-type certificate and unauthorised-issuance tests.'],
                ['Portal TLS 1.3 plus HSTS header observed (E-17).', 'Server-side downgrade refusal and weak-cipher refusal; HTTP→HTTPS redirect test.'],
                ['Upload endpoint asks for a client certificate and rejects no-certificate requests (E-20).', 'Successful upload with a unique branch cert; untrusted/expired/revoked/wrong-EKU rejection; zero-bytes-stored proof.'],
                ['Detached text-file signature verifies; tampering detected (E-26/E-27).', 'Dedicated report-signing certificate, actual PDF/PAdES, revoked-signer test, doctor-friendly walkthrough.'],
                ['Human role-play denies a private-key export request (E-15/16/28).', 'Enforced independent verification, dual-approval workflow, non-exportable key, real append-only audit.'],
            ]
        ); ?>
        <h3 id="a7">Appendix A7 — Test cases (TC-01 … TC-16)</h3>
        <?php
        $tc = [
            ['TC-01', 'R4/R5', 'Chain &amp; Trust', 'Trusted server chain and portal hostname.', 'Trusted chain and SAN accepted.', 'E-07/E-17 chain OK; portal cert verified, SAN matched.', 'PASS'],
            ['TC-02', 'R4', 'Chain &amp; Trust', 'Certificate from an untrusted CA.', 'Client refuses connection.', 'No untrusted-CA test supplied.', 'NOT RUN'],
            ['TC-03', 'R5', 'Chain &amp; Trust', 'Hostname mismatch.', 'Client refuses mismatched SAN.', 'No mismatch test supplied.', 'NOT RUN'],
            ['TC-04', 'R4', 'Wrong issuance', 'Issuance without required approval.', 'Issue blocked or alert raised.', 'No issuance-policy enforcement test supplied.', 'NOT RUN'],
            ['TC-05', 'R4/R6', 'Wrong issuance', 'Wrong certificate type / EKU.', 'Branch cert rejected as server; server cert rejected as branch.', 'E-22 uses server cert as client identity — invalid setup, not a rejection.', 'NOT RUN'],
            ['TC-06', 'R6', 'Branch mTLS', 'Missing branch client certificate.', 'Request refused; nothing stored; logged.', 'E-20 “No required SSL certificate was sent”; no storage/log proof.', 'PARTIAL'],
            ['TC-07', 'R6', 'Branch mTLS', 'Untrusted branch client certificate.', 'Handshake refused and logged.', 'No test supplied.', 'NOT RUN'],
            ['TC-08', 'R6/R7', 'Expiry &amp; revocation', 'Expired branch client certificate.', 'Expired cert refused; expiry alert.', 'E-13 shows a cert end date only; no expired-client attempt.', 'NOT RUN'],
            ['TC-09', 'R7', 'Expiry &amp; revocation', 'Revoked branch client certificate.', 'Revoked cert refused within SLA.', 'E-23 shows a CRL directive edit/reload, no CRL contents or rejection.', 'NOT RUN'],
            ['TC-10', 'R7', 'Expiry &amp; revocation', 'Expiry warning.', 'Automated warning at thresholds.', 'E-25 is a shell echo; no scheduler or delivery evidence.', 'PARTIAL'],
            ['TC-11', 'R5', 'Downgrade', 'Protocol downgrade (TLS 1.0/1.1).', 'Server refuses legacy protocol.', 'E-18 “no protocols available” may be client-side.', 'INCONCLUSIVE'],
            ['TC-12', 'R5', 'Downgrade', 'Weak cipher suite.', 'Server refuses the weak cipher.', 'No server-side weak-suite scan/test supplied.', 'NOT RUN'],
            ['TC-13', 'R8', 'Report signing', 'Genuine signed PDF verifies.', 'PDF signature validates with dedicated cert.', 'E-26 verifies a detached text signature using the TLS key.', 'PARTIAL'],
            ['TC-14', 'R8', 'Report signing', 'Edited report fails verification.', 'Modified report flagged invalid.', 'E-27 modified text file fails with bad signature.', 'PASS*'],
            ['TC-15', 'R13', 'AI-driven threat', 'Urgent unverified request blocked.', 'Key export/issuance/revocation refused; alert logged.', 'E-15/16/28 written role-play/simulated log only.', 'PARTIAL'],
            ['TC-16', 'R5', 'TLS &amp; HSTS', 'Portal TLS 1.3 and HSTS.', 'TLS 1.3 chain validates; HSTS emitted.', 'E-17 TLSv1.3 + HSTS header observed.', 'PASS'],
        ];
        echo docTable(['ID', 'Req', 'Category', 'Test', 'Expected', 'Observed evidence', 'Status'], $tc);
        ?>
        <p style="font-size:0.78rem;color:var(--color-gray-mid);">*TC-14 passes for detached text-file tamper detection only; the PDF/document-signing requirement remains outstanding.</p>
      </article>

      <!-- ============ SECTION 11 ============ -->
      <article class="doc-section" id="sec11">
        <div class="doc-section-head">
          <span class="doc-section-num">S11</span>
          <div>
            <h2>Legal Analysis</h2>
            <p>Bangladeshi cyber, data-protection, digital-signature, telecoms and licensing law.</p>
          </div>
        </div>
        <?php echo callout('note', 'Legal research scope', 'This section is an academic analysis, not legal advice. The legal position was checked against official Bangladesh law sources on 9 November 2026; the team must re-check the latest Gazette, rules and regulator guidance before submission or any real deployment.'); ?>
        <h3>11.1 Cyber offences and lawful testing</h3>
        <p>
          The <strong>Cyber Security Act 2026 (Act No. 81 of 2026)</strong> is the current named statute (deemed effective 21 May 2025).
          <strong>§18</strong> addresses unlawful access to computers/systems/networks; <strong>§19</strong> damage and harmful/phishing conduct;
          <strong>§21</strong> cyber-space forgery (including unauthorised creation or alteration of digital documents by a person or AI agent);
          <strong>§22</strong> cyber-space fraud. The <strong>Cyber Security (Amendment) Act 2026 (Act No. 99)</strong> repealed §20 of the 2026 Act.
          An attacker who accesses, changes records, forges a report or uses a false identity to obtain a certificate may engage one
          or more offences depending on intent and authorisation. The group’s testing is lawful only within the instructor-authorized
          scope: isolated VMs, host-only network, synthetic data, <code>.test</code> domains.
        </p>
        <h3>11.2 Personal-data protection and health information</h3>
        <p>
          The <strong>Personal Data Protection Act 2026 (Act No. 63 of 2026)</strong> classifies health information as <em>sensitive personal data</em>
          (s.2(21)); s.7 sets conditions for sensitive-data processing; s.16 restricts disclosure; s.17 requires appropriate technical and
          organisational security measures (encryption/pseudonymisation, confidentiality, integrity, availability, resilience, risk assessment,
          periodic testing); ss.18–19 address retention (register kept ≥5 years); s.20 requires breach notification where significant harm is likely.
          Do not assume a “72-hour” deadline unless a current regulation says so. Sections 23 and 31–35 are deferred pending a Government Gazette notification.
        </p>
        <h3>11.3 Digital signatures and certifying authorities</h3>
        <p>
          The <strong>ICT Act 2006</strong> defines an electronic signature by reference to its link to the record, ability to identify the signer,
          secure creation under the signer’s control, and ability to detect later changes. The Act provides a licensing framework (s.22 licensing;
          s.31 requires a CA to use secure hardware/software and protect signature secrecy). A private lab CA can establish <em>technical</em> trust
          within its relying-party group, but it does not automatically have the same statutory status as a <strong>CCA-licensed</strong> certifying
          authority. For job/visa or external legal decisions, Spordha should obtain Bangladeshi legal advice and consider a licensed CCA certificate
          or approved e-signature service.
        </p>
        <h3>11.4 Telecommunications, lawful access and incident requests</h3>
        <p>
          The <strong>Bangladesh Telecommunications (Amendment) Act 2026 (Act No. 51 of 2026)</strong> is listed in the official register. Spordha should
          not infer a general decryption obligation. Any lawful request should be validated for statutory authority, scope, necessity and deadline;
          escalated to legal counsel; limited to the minimum responsive data; and recorded with chain-of-custody evidence.
        </p>
        <h3>11.5 Software licences and ownership</h3>
        <p>
          The <strong>Copyright Act 2023 (Act No. 34 of 2023)</strong> recognises IT-based digital works and governs ownership/licensing. The project must
          record exact versions and licences of OpenSSL, Nginx, VirtualBox, libraries and test tools, preserve notices/licence texts, comply with
          redistribution obligations, and document who owns team-written scripts and configuration files.
        </p>
        <h3>11.6 GDPR and HIPAA comparison</h3>
        <p>
          GDPR Art. 9 treats health data as a special category; Art. 32 requires risk-appropriate measures including encryption, resilience, restoration
          and testing — but GDPR applies only where its territorial/material scope is met. The US HIPAA Security Rule (administrative, physical and
          technical safeguards for ePHI) is a useful comparison but is not automatically applicable to a Bangladesh lab.
        </p>
        <h3>11.7 Consequences of a failure</h3>
        <ul>
          <li>Patients may suffer privacy, discrimination, employment or medical-decision harms from disclosure or falsified results.</li>
          <li>Doctors/employers may act on a false report, lose trust in authentic reports, or need to re-check reports signed in a compromise window.</li>
          <li>Spordha may face operational disruption, contract disputes, regulatory investigation, reputational damage and potential statutory liability.</li>
          <li>The lab must preserve trustworthy evidence: certificate serials/fingerprints, signed audit events, report versions, incident chronology and approvals.</li>
        </ul>
      </article>

      <!-- ============ SECTION 12 ============ -->
      <article class="doc-section" id="sec12">
        <div class="doc-section-head">
          <span class="doc-section-num">S12</span>
          <div>
            <h2>Ethical Analysis</h2>
            <p>The pathologist key-compromise dilemma, analysed with duty-based ethics and the ACM Code.</p>
          </div>
        </div>
        <h3>The dilemma</h3>
        <p>
          A pathologist’s signing key may have been copied immediately before Eid. Revoking it can delay emergency reports for
          days; leaving it active can let an attacker forge reports. The two duties in conflict are <strong>continuity of essential
          medical services</strong> versus <strong>protection of patients, employers and doctors from fraudulent clinical evidence</strong>.
        </p>
        <h3>Framework and professional code</h3>
        <p>
          Duty-based (deontological) ethics with the <strong>ACM Code of Ethics</strong>: principle <strong>1.2</strong> (avoid harm),
          <strong>1.6</strong> (respect privacy) and <strong>1.7</strong> (honour confidentiality). The professional duty is not merely to keep the
          service online, but also to avoid knowingly allowing reports that may be forged to be relied upon.
        </p>
        <h3>Affected groups and competing duties</h3>
        <?php echo docTable(
            ['Group', 'Risk if revoked immediately', 'Risk if revocation is delayed'],
            [
                ['Patients', 'A delayed emergency report may postpone care.', 'A forged result may cause wrong treatment or exposure of health data.'],
                ['Doctors / pathologists', 'Manual re-signing and confirmation create workload.', 'They may rely on a forged result and lose professional trust.'],
                ['Employers / visa reviewers', 'Employment/travel decisions may be delayed.', 'They may act on a false “normal” result.'],
                ['Spordha / IT team', 'Emergency workflow may be strained.', 'The incident may expand; genuine reports become unidentifiable.'],
                ['Regulators / public', 'A controlled outage may need explanation.', 'Loss of evidence and trust undermines the diagnostic service.'],
            ]
        ); ?>
        <h3>Decision and safeguards</h3>
        <?php echo callout('success', 'Decision', 'Where the compromise is credible, suspend and revoke the affected signing certificate immediately, preserve key and audit evidence without further use, and activate a controlled continuity workflow — prioritising avoidance of foreseeable harm from forged medical reports. Emergency reports are re-approved and re-signed by an independent pathologist using a replacement key; recipients in the compromise window are notified to re-verify.'); ?>
        <ul>
          <li><strong>Opposing view:</strong> delaying revocation preserves timely access and prevents outage harm — a legitimate concern during Eid with intermittent links.</li>
          <li><strong>Safeguard 1:</strong> encrypted branch queues and a local emergency-report process keep the service running without accepting the suspect key.</li>
          <li><strong>Safeguard 2:</strong> define a compromise window and re-check every report signed in that interval.</li>
          <li><strong>Safeguard 3:</strong> a second pathologist and two-person approval for emergency reissue; preserve hashes, signatures, timestamps and logs.</li>
          <li><strong>Safeguard 4:</strong> communicate clearly without disclosing unnecessary health details; provide a phone/helpdesk validation route.</li>
        </ul>
      </article>

      <!-- ============ SECTION 13 ============ -->
      <article class="doc-section" id="sec13">
        <div class="doc-section-head">
          <span class="doc-section-num">S13</span>
          <div>
            <h2>Interactions &amp; Innovation</h2>
            <p>Requirement interaction matrix (A6), innovation statement and Form B declaration.</p>
          </div>
        </div>
        <h3 id="a6">Interaction matrix (A6)</h3>
        <?php echo docTable(
            ['Requirement A', 'Requirement B', 'Conflict', 'Resolution'],
            [
                ['Mandatory mTLS', 'Easy branch uploads', 'Certificate expiration blocks uploads.', 'Automated warning, named device owner, clear retry code, fast reissue.'],
                ['Fail-closed revocation', 'Emergency availability', 'A status server may be unreachable during a link outage.', 'Encrypted local spool; validate before upload resumes; report read path stays independent.'],
                ['Unique signing keys', 'Low IT staffing / cost', 'Many keys increase enrolment and rotation burden.', 'One repeatable enrolment process + certificate inventory; prioritise signer keys.'],
                ['Audit logging', 'Patient privacy &amp; retention', 'Logs can reveal patient identifiers.', 'Log certificate/device/report IDs and metadata; minimise patient data; restrict access.'],
            ]
        ); ?>
        <h3>Innovation statement</h3>
        <p>
          The improvement over the scenario’s shared-password and self-signed-certificate state is the combination of a
          <strong>two-tier PKI</strong>, <strong>per-device mutual TLS</strong>, <strong>certificate-purpose separation</strong>,
          <strong>signed reports</strong>, <strong>explicit revocation</strong> and an <strong>independent approval process</strong> for
          sensitive certificate operations. Diagrams D1–D7 were created for this report to show how the controls connect across the
          service, PKI and human workflow.
        </p>
        <?php echo callout('note', 'Generative-AI use declaration (Form B)', 'Generative AI was used to help structure and draft the report, organise screenshot captions, create original explanatory diagrams and identify evidence gaps. No private keys, passphrases or real patient records were supplied. The team must verify every technical/legal statement and complete Form B accurately.'); ?>
      </article>

      <!-- ============ SECTION 14 ============ -->
      <article class="doc-section" id="sec14">
        <div class="doc-section-head">
          <span class="doc-section-num">S14</span>
          <div>
            <h2>Adaptation &amp; Reflection</h2>
            <p>Post-quantum migration, shorter lifetimes, AI agents, legal change and the learning log (R18 / A9).</p>
          </div>
        </div>
        <?php echo docTable(
            ['Trigger', 'Required design change', 'Owner / evidence'],
            [
                ['Certificate compromise or unexplained issuance', 'Revoke, stop issuance, rotate/rebuild CA layer, reissue descendants, notify relying parties.', 'PKI lead + approvers; incident exercise + CA log.'],
                ['Renewal workload / shorter validity practical', 'Move leaves from 90 days toward ≤30 days using ACME with staged renewal, monitoring and rollback.', 'IT officer; automated-renewal demo.'],
                ['Post-quantum migration ready / mandated', 'Inventory RSA/ECDSA; test hybrid key exchange; plan ML-DSA / SLH-DSA signatures.', 'Security lead; algorithm inventory + transition plan.'],
                ['AI impersonation improves / AI agents request certificates', 'Machine-to-machine workload identity, signed request policy, scoped short-lived credentials, dual control.', 'Management + PKI lead; simulated rogue-agent test.'],
                ['Law / regulation / CCA guidance changes', 'Re-map processing purposes, consent, breach reporting, certificate licensing, signature evidential requirements.', 'Legal counsel + DPO; dated compliance review.'],
                ['Old branch PC upgraded / retired', 'Remove temporary legacy endpoint; re-test TLS 1.3-only; rotate shared credentials and certificates.', 'Branch IT; inventory, scan, retirement evidence.'],
            ]
        ); ?>
        <h3 id="a9">Appendix A9 — Learning log</h3>
        <?php echo docTable(
            ['Date / week', 'Member', 'What was new', 'How it was learned', 'Principle applied', 'Evidence'],
            [
                ['[Complete]', '[Member]', 'Root vs. issuing CA / offline root', '[Source or lab exercise]', 'Least privilege / trust hierarchy', '[B-xx / E-xx]'],
                ['[Complete]', '[Member]', 'SAN, KeyUsage and EKU separation', '[Source or lab exercise]', 'Purpose limitation', '[B-xx / TC-xx]'],
                ['[Complete]', '[Member]', 'TLS 1.3 and server-side testing', '[Source or lab exercise]', 'Secure defaults', '[B-xx / TC-xx]'],
                ['[Complete]', '[Member]', 'mTLS and certificate revocation', '[Source or lab exercise]', 'Authenticate before storing', '[B-xx / TC-xx]'],
                ['[Complete]', '[Member]', 'PDF signatures and integrity validation', '[Source or lab exercise]', 'Evidence and accountability', '[B-xx / TC-xx]'],
            ]
        ); ?>
        <?php echo callout('note', 'Reflection', 'The screenshots show practical debugging lessons: resolve host names before TLS testing; verify file paths and permissions before certificate transfer; issue a certificate for the <code>clientAuth</code> role rather than reusing a server certificate; and an echo statement is not an automated monitoring control.'); ?>
      </article>

      <!-- ============ SECTION 15 ============ -->
      <article class="doc-section" id="sec15">
        <div class="doc-section-head">
          <span class="doc-section-num">S15</span>
          <div>
            <h2>Resources, Limits &amp; Conclusion</h2>
            <p>Tools register (A8), unresolved items and the project conclusion.</p>
          </div>
        </div>
        <h3 id="a8">Appendix A8 — Resources register</h3>
        <?php echo docTable(
            ['Resource', 'Use in this project', 'Why chosen / limitation'],
            [
                ['VirtualBox', 'VM environment and host-only lab networking.', 'Evidence shows manager + host-only adapter; verify every adapter and remove NAT/bridged access.'],
                ['Linux VMs (Ubuntu)', 'Host OS for OpenSSL and Nginx.', 'Record exact OS/kernel version and patch date.'],
                ['OpenSSL', 'Keys/CSRs/certificates, chain validation, TLS and signature testing.', 'Root key uses <code>-nodes</code> and RSA-2048; rebuild with encrypted custody and modern policy.'],
                ['Nginx', 'HTTPS portal and upload reverse proxy; mTLS gate.', 'Nginx 1.28.3 appears in output; record exact version and sanitized config.'],
                ['curl 8.18.0', 'TLS request and handshake inspection.', 'A client-side “no protocols available” is not proof the server refuses TLS 1.1.'],
                ['PowerShell / OpenSSH', 'Certificate-file transfer.', 'Shows wrong path and <code>sudo</code> from Windows; use one controlled, documented transfer method.'],
                ['NIST / IETF / OWASP / CCA', 'Design criteria for PKI, TLS and key lifecycle.', 'Guidance rather than binding law in some cases; cited in Section 7.'],
                ['PDF signing verifier', 'Dedicated PDF signature validation.', 'No actual PDF-signing tool is evidenced; select a PAdES-capable tool before claiming R8.'],
            ]
        ); ?>
        <h3>Limits and unresolved items</h3>
        <ul>
          <li>All material uses the single identity <strong>Spordha Diagnostics Ltd.</strong> and <code>.spordha.test</code> — a fictional academic identity with no real organisation represented.</li>
          <li>The root CA is visible as running and the root key uses <code>-nodes</code> — not an acceptable offline/encrypted-root posture.</li>
          <li>No demonstrated successful valid branch upload exists (the E-22 request reuses the server cert/key as a client identity).</li>
          <li>No published CRL/OCSP service, revoked-certificate rejection, expiry notifications, automated renewal, downgrade refusal or weak-cipher scan is evidenced.</li>
          <li>The signature demo covers text rather than PDF and uses the TLS server key.</li>
          <li>The report requires final review by a qualified Bangladeshi legal professional before any real deployment.</li>
        </ul>
        <h3>Conclusion</h3>
        <p>
          The supplied work establishes a useful starting point — certificate generation and validation, a TLS 1.3 portal connection
          with HSTS, a server-side request for client authentication, and a cryptographic tamper-detection demonstration. Important
          controls remain incomplete or use the wrong key purpose. The recommended design provides a clear route to a secure two-tier
          PKI, unique branch identities, revocation-aware mTLS, purpose-separated report signatures and human approval against
          AI-driven impersonation. The team should treat the current lab as a partial prototype until the missing tests and key-custody
          corrections are completed and recorded.
        </p>
      </article>

      <!-- ============ SECTION 16 ============ -->
      <article class="doc-section" id="sec16">
        <div class="doc-section-head">
          <span class="doc-section-num">S16</span>
          <div>
            <h2>References</h2>
            <p>IEEE-style references, checked on 9 November 2026.</p>
          </div>
        </div>
        <ol style="font-size:0.82rem;color:var(--color-charcoal);line-height:1.8;">
          <li>Government of Bangladesh, “Cyber Security Act, 2026 (Act No. 81 of 2026),” Laws of Bangladesh, 10 Apr. 2026. https://bdlaws.minlaw.gov.bd/act-print-1710.html</li>
          <li>Government of Bangladesh, “Cyber Security (Amendment) Act, 2026 (Act No. 99 of 2026),” Laws of Bangladesh, 1 Jul. 2026. https://bdlaws.minlaw.gov.bd/act-print-1728.html</li>
          <li>Government of Bangladesh, “Personal Data Protection Act, 2026 (Act No. 63 of 2026),” Laws of Bangladesh, 10 Apr. 2026. https://bdlaws.minlaw.gov.bd/act-print-1692.html</li>
          <li>Government of Bangladesh, “Information and Communication Technology Act, 2006 (Act No. 39 of 2006),” Laws of Bangladesh, 8 Oct. 2006, as amended. https://bdlaws.minlaw.gov.bd/act-details-950.html?lang=en</li>
          <li>D. Cooper et al., “Internet X.509 Public Key Infrastructure Certificate and Certificate Revocation List (CRL) Profile,” RFC 5280, IETF, May 2008. https://www.rfc-editor.org/info/rfc5280/</li>
          <li>E. Rescorla, “The Transport Layer Security (TLS) Protocol Version 1.3,” RFC 9846, IETF, Jul. 2026 (obsoletes RFC 8446). https://www.rfc-editor.org/info/rfc9846/</li>
          <li>K. McKay and D. Cooper, “Guidelines for the Selection, Configuration, and Use of Transport Layer Security (TLS) Implementations,” NIST SP 800-52 Rev. 2, Aug. 2019 (under review, 7 May 2026). https://csrc.nist.gov/pubs/sp/800/52/r2/final</li>
          <li>E. Barker, “Recommendation for Key Management: Part 1—General,” NIST SP 800-57 Part 1 Rev. 5, May 2020. https://www.nist.gov/publications/recommendation-key-management-part-1-general-1</li>
          <li>OWASP Foundation, “Transport Layer Security Cheat Sheet.” https://cheatsheetseries.owasp.org/cheatsheets/Transport_Layer_Security_Cheat_Sheet.html</li>
          <li>Office of the Controller of Certifying Authorities, “Policies: CA Licensing Guideline 2024; PKI Auditing Guideline 2024; Bangladesh Root CA CP 2023; CPS 2023.” https://cca.gov.bd/pages/policies</li>
          <li>Government of Bangladesh, “Bangladesh Telecommunications (Amendment) Act, 2026 (Act No. 51 of 2026),” 10 Apr. 2026. https://btrc.gov.bd/pages/laws</li>
          <li>Government of Bangladesh, “Copyright Act, 2023 (Act No. 34 of 2023),” Laws of Bangladesh, 18 Sep. 2023. https://bdlaws.minlaw.gov.bd/act-print-1452.html</li>
          <li>Association for Computing Machinery, “ACM Code of Ethics and Professional Conduct,” 2018.</li>
          <li>European Parliament and Council, “Regulation (EU) 2016/679 (GDPR),” Off. J. Eur. Union, 27 Apr. 2016, Arts. 9, 32–34.</li>
          <li>U.S. HHS, “Summary of the HIPAA Security Rule,” 45 CFR Part 160 and Part 164, Subparts A and C.</li>
          <li>East West University, “CSE487 Cyber Security, Ethics and Law — Fall 2026: Mini Project, Track 2 — Project Specification for Students,” 12 pp.</li>
          <li>Project team, “CSE487 Project Server,” supplied implementation evidence document, 28 screenshots (E-01–E-28).</li>
        </ol>
      </article>

      <!-- ============ APPENDIX B: EVIDENCE ============ -->
      <article class="doc-section" id="evidence">
        <div class="doc-section-head">
          <span class="doc-section-num">B</span>
          <div>
            <h2>Appendix B — Implementation Evidence (E-01 … E-28)</h2>
            <p>Organised screenshot evidence with title and interpretation.</p>
          </div>
        </div>
        <?php
        $evidence = [
            ['E-01', 'VirtualBox VM inventory — lab components', 'Lists Root-CA, Issuing-CA, ULab-Server, Branch-Client; Root-CA shown running, so no offline-root proof.'],
            ['E-02', 'Server network adapter — host-only selection', 'Adapter 2 uses a Host-Only Ethernet Adapter; confirm all NICs.'],
            ['E-03', 'Initial portal lookup failure from Root-CA', '<code>openssl s_client</code> cannot resolve <code>portal.spordha.test</code> — DNS failure, not a validation test.'],
            ['E-04', 'Server certificate signing request displayed', 'CSR contains a public key and requested subject, not the private key.'],
            ['E-05', 'Root CA certificate generation command', '10-year RSA-2048 self-signed root using <code>-nodes</code> (unencrypted key).'],
            ['E-06', 'Issuing CA certificate signed by root', 'CA:TRUE + keyCertSign/cRLSign; omits a complete documented CA profile.'],
            ['E-07', 'Certificate chain assembled and verified', '<code>openssl verify</code> returns <code>server.crt: OK</code>; repeat with <code>-purpose sslserver</code> and <code>-verify_hostname</code>.'],
            ['E-08', 'Branch-side certificate transfer — failed attempt', 'SCP to localhost:2221 fails; remote path missing.'],
            ['E-09', 'Repeated SCP and trust-store troubleshooting', 'Missing CA-chain file and incorrect <code>--cacert</code> path.'],
            ['E-10', 'curl CA bundle path error', 'curl cannot open the CA file; malformed option/path.'],
            ['E-11', 'Windows PowerShell transfer attempt', 'Missing SCP path; <code>sudo</code> unavailable in PowerShell.'],
            ['E-12', 'Repeated branch trust validation failure', 'Client certificate path still unavailable — not proof of untrusted-cert rejection.'],
            ['E-13', 'Server certificate expiry inspection', '<code>notAfter=Nov 9 04:51:25 2027 GMT</code> — an expiry date, not an expiry-warning system.'],
            ['E-14', 'Synthetic patient result text file created', 'A synthetic line written to <code>report.txt</code> — a pre-signing artifact only.'],
            ['E-15', 'Urgent key-export email role-play', 'Fake CEO requests the server private key; IT officer replies dual approval required.'],
            ['E-16', 'Urgent key-export email role-play displayed', 'Saved text confirms the denial; use known-number callback + second approver + audit record.'],
            ['E-17', 'Portal TLS 1.3, certificate verification and HSTS', 'curl verifies cert/hostname, completes TLS 1.3, receives HSTS; HTTP 403 is a directory-index response.'],
            ['E-18', 'Legacy TLS attempt — client-side result inconclusive', '<code>--tls-max 1.1</code> returns “no protocols available” — may be client-side; repeat with a server-side test.'],
            ['E-19', 'Upload endpoint connection — certificate error response', 'HTTP 400 “The SSL certificate error” — consistent with an mTLS problem, not a specific check.'],
            ['E-20', 'Upload endpoint rejects missing client certificate', '“No required SSL certificate was sent” — direct evidence of refusal; no stored-data count shown.'],
            ['E-21', 'Nginx error log — portal directory index denied', 'Forbidden directory index at <code>/var/www/html/</code> — explains the 403, not mTLS logging.'],
            ['E-22', 'TLS 1.3 handshake with wrong client identity', '<code>server.crt</code>/<code>server.key</code> reused as client identity — not a valid branch-client test.'],
            ['E-23', 'CRL directive edit and Nginx reload', '<code>ssl_crl</code> enabled and reloaded; no CRL contents/freshness or revoked-cert rejection shown.'],
            ['E-24', 'Upload TLS request remains unsuccessful', 'A subsequent request still returns a certificate error; no successful branch upload.'],
            ['E-25', 'Expiry status message — manual output only', 'An echo statement — not proof of a scheduler, notification delivery or automatic renewal.'],
            ['E-26', 'Detached SHA-256 signature verifies on text file', 'Signed with the server key, verifies <code>Verified OK</code> — wrong key purpose, text not PDF.'],
            ['E-27', 'Edited text file fails signature verification', 'OpenSSL reports bad signature — positive tamper detection for that text file.'],
            ['E-28', 'Simulated incident record — action blocked and logged', 'Fake CEO request blocked under dual approval; mentions HSM although no HSM exists — state the real TPM/token control.'],
        ];
        echo docTable(['ID', 'Title', 'Interpretation'], $evidence);
        ?>
        <p style="font-size:0.85rem;color:var(--color-gray-mid);">
          Browse the full visual evidence gallery at
          <a href="evidence_gallery.php" style="font-weight:700;color:#000;">Evidence Gallery (E-01 – E-28)</a>.
        </p>
      </article>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
