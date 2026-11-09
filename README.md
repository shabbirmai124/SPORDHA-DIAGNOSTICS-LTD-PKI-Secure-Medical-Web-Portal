# Spordha Diagnostics Ltd. — PKI & Secure Medical Web Portal
### Track 2: PKI and Secure Communication
**Course:** CSE487 Cyber Security, Ethics and Law (Fall 2026)  
**Institution:** East West University, Department of Computer Science & Engineering  
**Case Identity:** Spordha Diagnostics Ltd. (Central Laboratory in Dhaka & 3 Branches: Dhanmondi, Uttara, Mirpur)

---

## 🌟 Executive Overview
This project delivers a complete, professional, full-stack diagnostic laboratory web platform built using **HTML5, CSS3, JavaScript (ES6+), PHP 8.x, and SQL (SQLite & MySQL/MariaDB compatible)**. 

The website is crafted with a high-end **White, Black, Charcoal, and Slate Gray monochromatic design system**, strictly implementing every requirement from the official course specification (`CSE487_Mini_Project_Student_Instructions (1).docx`), the comprehensive academic report (`CSE487_Spordha_Report_Download.docx`), and the 28 server terminal implementation screenshots (`CSE487 Project Server.docx`).

---

## 🛡️ Core PKI & Security Features Implemented

1. **Two-Tier Certificate Authority Hierarchy (Requirement R4)**
   - **Offline Root CA (Tier 1):** Encrypted RSA-4096 / RSA-2048 key, 10-year lifetime (3650 days from Evidence E-05), powered down after signing intermediate CA.
   - **Online Issuing CA (Tier 2):** 5-year lifetime (1825 days from Evidence E-06), `CA:TRUE`, `pathLen=0`, `keyCertSign`, `cRLSign`. Issues strictly scoped leaf certificates.
   - **Interactive Hierarchy Visualizer:** Embedded vector diagram D2 and live certificate directory.

2. **Transport Layer Security & HSTS (Requirement R5)**
   - Enforcing **TLS 1.3** (`TLS_AES_256_GCM_SHA384` / `X25519MLKEM768` hybrid post-quantum key exchange as shown in Evidence E-17 & E-22).
   - Strict `Strict-Transport-Security: max-age=31536000` header emission.
   - Protocol downgrade resistance: legacy TLS 1.0 / 1.1 handshake attempts are terminated with alert `protocol_version (582)` (Evidence E-18, TC-11).

3. **Mutual TLS (mTLS) for Branch Uploads (Requirement R6)**
   - Eliminates plain HTTP and shared branch passwords.
   - Each branch device (Dhanmondi, Uttara, Mirpur) authenticates with an individual X.509 client certificate containing `clientAuth` Extended Key Usage (EKU).
   - Server explicitly rejects missing certificates (`HTTP 400: No required SSL certificate was sent`, Evidence E-20) and discards payloads with zero bytes committed.
   - Interactive mTLS Simulator (`branch_upload.php`) to test valid, missing, revoked, and wrong-EKU client certificates.

4. **Automated Expiry Warnings & CRL Revocation (Requirement R7)**
   - Multi-threshold alert system: **30-day (Notice)**, **14-day (Warning)**, and **7-day (Critical Outage Risk)** warnings (Evidence E-25).
   - Live Certificate Revocation List (CRL) management: instant toggle to revoke or reinstate certificates with immediate CRL distribution simulation (Evidence E-23, TC-09).

5. **Clinical PDF Report Signing & Real-Time Tamper Detection Lab (Requirement R8)**
   - Pathologists digitally sign approved clinical reports using dedicated document-signing keys.
   - Detached SHA-256 digital signature verification (`verify.php`) mimicking Evidence E-26 (`Verified OK`).
   - Interactive Live Tamper Playground: editing report text triggers instant SHA-256 recomputation and replicates OpenSSL error `404712D682720000:error:02000068:rsa routines:ossl_rsa_verify:bad signature` (Evidence E-27, TC-14).

6. **AI-Driven Threat Defense & Dual Approval (Requirement R13 / Section 8)**
   - Mitigates AI voice-cloning, deepfakes, and executive phishing demanding emergency private key exports.
   - Enforces **Rule SOP-SEC-04**: server private keys are non-exportable; key export requires offline multi-party quorum and CISO incident logging (Evidence E-15, E-16, E-28).

7. **Full Statutory & Ethical Analysis (Sections 11 & 12)**
   - **Cyber Security Act 2026 (Act 81):** Sections 18, 19, 21 (forgery), 22 (fraud).
   - **Personal Data Protection Act 2026 (Act 63):** Sensitive health data processing, 5-year retention, breach notification rules.
   - **ICT Act 2006:** Electronic signature validity and CCA licensing framework.
   - **Ethical Dilemma:** Compromise of pathologist signing key on the eve of Eid analyzed using duty-based ethics and ACM Code of Ethics principles 1.2, 1.6, and 1.7.

---

## 👥 Stakeholder Accounts & Credentials (1-Click Login)

The system features working authentication (`password_hash` & `password_verify`), session handling, CSRF defense, and **1-Click Quick Role Switchers** on the login page:

| Persona / Role | Name / Designation | Email / ID | Password | Portal Capabilities |
| :--- | :--- | :--- | :--- | :--- |
| **Patient** | Tanvir Ahmed | `patient@spordha.test` | `Patient@123` | View health records, download signed PDF reports, track requisitions. |
| **Patient** | Fahim Ahmed | `fahim.ahmed@spordha.test` | `Fahim@123` | Corporate employee (Patient ID: P-10027) — portal access and signed reports. |
| **Referring Doctor** | Dr. Rezaul Karim | `doctor.karim@spordha.test` | `Doctor@123` | Review referred patients, verify forwarded PDF reports, clinical summaries. |
| **Pathologist** | Dr. Nazmul Huda | `pathologist@spordha.test` | `Patho@123` | Review lab findings, digitally sign reports with RSA key, simulate tampering. |
| **Lab Technologist** | Sabbir Hasan | `tech@spordha.test` | `Tech@123` | Enter specimen results, manage sample registration queue. |
| **Branch Staff** | Tahmina Chowdhury | `branch.uttara@spordha.test` | `Branch@123` | Transmit sample requisitions using branch mTLS client certificate. |
| **PKI Administrator** | Md Shabbir Mia | `it-admin@spordha.test` | `12345` | Full SOC console: CA status, revoke certificates on CRL, AI incident audit. |

*(All role passwords also accept `Spordha@2026` or `password123` for evaluation convenience; the PKI Administrator also accepts `12345`.)*

---

## 📂 Project Directory Structure

```
SPORDHA DIAGNOSTICS LTD Website/
├── config/
│   └── database.php             # Unified PDO connection (SQLite zero-config + MySQL toggle, auto-seeder)
├── database/
│   ├── schema.sql               # Production MySQL / MariaDB schema and complete seed data
│   └── spordha.db               # SQLite database generated automatically on first visit
├── includes/
│   ├── auth.php                 # Session management, RBAC, password authentication, CSRF tokens
│   ├── functions.php            # SHA-256 digest, digital signing, tamper verification, expiry alerts
│   ├── header.php               # Sticky navigation, security telemetry bar, role indicator
│   └── footer.php               # Statutory compliance notes, branch locations, academic credits
├── assets/
│   ├── css/
│   │   ├── style.css            # Monochromatic design system (white, black, charcoal & slate gray)
│   │   └── dashboard.css        # Dashboard metrics, simulated terminal windows, printable report CSS
│   └── js/
│       ├── main.js              # Tab switcher, search filters, modals, clipboard, CRL AJAX
│       └── verify.js            # Live SHA-256 calculation & real-time tamper detection playground
├── assets1/                     # 34 files — architecture diagrams + first half of evidence:
│   ├── D1_target_architecture.png           (Figure 1 - Target Architecture)
│   ├── D2_two_tier_ca_hierarchy.png         (Figure 5 - Two-Tier CA Hierarchy)
│   ├── D3_virtual_lab_topology.png          (Figure 2 - Virtual Lab Topology)
│   ├── D4_mtls_branch_upload_flow.png       (Figure 6 - mTLS Flow)
│   ├── D5_clinical_report_signing_workflow.png (Figure 7 - PAdES Report Signing)
│   ├── D6_ai_impersonation_attack_tree.png  (Figure 4 - AI Threat Attack Tree)
│   ├── D7_trust_boundaries_stride.png       (Figure 3 - Trust Boundaries & STRIDE)
│   ├── E01_screenshot.png to E14_screenshot.png
│   └── favicon.svg
├── assets2/                     # 33 files — second half of evidence + source captures:
│   ├── E15_screenshot.png to E28_screenshot.png
│   └── server_image*.png / extra_image*.png (original captures)
├── api/
│   ├── verify_report.php        # JSON API for automated report authenticity queries
│   ├── revoke_cert.php          # JSON API for live CRL certificate revocation / reinstatement
│   └── incident_action.php      # JSON API for security incident logging & dual approval
├── index.php                    # High-impact landing page, quick verifier, role switcher, stats
├── login.php                    # Secure authentication page with 1-click persona buttons
├── logout.php                   # Session termination and token invalidation
├── dashboard.php                # Unified role-aware dashboard for all 6 stakeholders
├── patient_portal.php           # Patient medical history & cryptographic certificate download
├── doctor_portal.php            # Referring doctor consultation console & batch verifier
├── pathologist_studio.php       # Pathologist digital signing studio & live tamper simulation
├── branch_upload.php            # Branch mTLS upload simulator with client cert state toggles
├── pki_soc.php                  # PKI Security Operations Center: CA tree, CRL, Expiry, AI Log, Test Matrix
├── verify.php                   # Public report authenticity verifier & live cryptographic tamper lab
├── services.php                 # Diagnostic test catalog, fees, turnaround times, specimens
├── branches.php                 # Branch directory (HQ Dhaka, Dhanmondi, Uttara, Mirpur) & mTLS IPs
├── documentation.php            # Full 16-section academic project report & specification viewer
├── evidence_gallery.php         # Filterable gallery of all 28 screenshot evidence items (E-01 – E-28)
├── generate_report.php          # Authentic printable/PDF clinical pathology report with digital seal
└── start_server.bat             # 1-click batch launcher for Windows / XAMPP PHP
```

---

## 🚀 How to Run the Website

### Method 1: One-Click Batch Launcher (Recommended)
1. Double-click `start_server.bat` in this folder.
2. The script will automatically detect PHP in `C:\xampp\php\php.exe` (or your system PATH), launch the server at `http://127.0.0.1:8080`, and open your default web browser.

### Method 2: Command Line (PowerShell / Terminal)
```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8080
```
Then visit `http://127.0.0.1:8080` in your web browser.

### Method 3: XAMPP Apache
1. Copy or symlink this folder to `C:\xampp\htdocs\spordha`.
2. Start Apache from the XAMPP Control Panel.
3. Open `http://localhost/spordha` in your browser.

*(Note: The database works immediately out-of-the-box using the auto-seeded SQLite database `database/spordha.db`. If you prefer MySQL, simply import `database/schema.sql` into phpMyAdmin and set `define('DB_DRIVER', 'mysql');` in `config/database.php`)*

---

## 🧪 Key Demonstration Scenarios to Try

1. **Verify Genuine Report (Evidence E-26):**
   - Go to `http://127.0.0.1:8080/verify.php?uid=SPD-2026-90412`
   - Observe green **VERIFIED OK** badge, matching SHA-256 hash, and pathologist RSA signature details.
2. **Inspect Tampered Report (Evidence E-27):**
   - Go to `http://127.0.0.1:8080/verify.php?uid=SPD-2026-90499`
   - Observe red **TAMPER DETECTED** alert, error `0x02000068: bad signature`, and tampered payload explanation.
3. **Live Tamper Testing Playground:**
   - Scroll down on `verify.php` to the interactive editor.
   - Click **"Simulate Attacker Tampering"** to see the SHA-256 hash recalculate in real-time and fail validation.
   - Click **"Reset to Genuine Baseline"** to see it restore to **Verified OK**.
4. **Branch mTLS Upload Simulation (Requirement R6):**
   - Go to `http://127.0.0.1:8080/branch_upload.php`.
   - Select **Valid Client Certificate** → returns **HTTP 200 OK** (matching `image38.png`).
   - Select **Missing Client Certificate** → returns **HTTP 400: No required SSL certificate was sent** (matching E-20).
   - Select **Revoked Certificate** → returns **HTTP 400: The SSL certificate error (CRL)** (matching E-23).
   - Select **Wrong EKU** → returns **HTTP 400: EKU missing clientAuth** (matching E-22).
5. **Simulate Executive Impersonation Block (Evidence E-28):**
   - Go to `http://127.0.0.1:8080/pki_soc.php` and click the **"AI Threat & Dual Approval"** tab.
   - Inspect the blocked incident logs and Rule SOP-SEC-04 dual-quorum mandate.
6. **Printable Pathology Certificate:**
   - Click **"Printable PDF Certificate"** on any report to view or print an authentic medical report with a PAdES digital signature seal block.

---
*Created for East West University CSE487 Mini Project — Fall 2026*
