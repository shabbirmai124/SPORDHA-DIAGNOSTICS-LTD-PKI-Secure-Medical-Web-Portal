# Spordha Diagnostics Ltd. — Enterprise PKI & Secure Medical Web Portal
### Track 2: PKI and Secure Communication
**Course:** CSE487 Cyber Security, Ethics and Law (Fall 2026)  
**Institution:** East West University, Department of Computer Science & Engineering  
**Case Identity:** Spordha Diagnostics Ltd. (Central Laboratory in Dhaka & 3 Branches: Dhanmondi, Uttara, Mirpur)  
**Developer:** Md Shabbir Mia (ID: 2022-2-60-048)  
**Supervision:** Department of CSE, East West University  

---

## 📋 Table of Contents
1. [Executive Summary & Academic Context](#-executive-summary--academic-context)
2. [What Was Done in This Project](#-what-was-done-in-this-project)
3. [System Architecture & Security Blueprints (D1–D7)](#-system-architecture--security-blueprints)
4. [Visual Interface Showcase & Website Screenshots](#-visual-interface-showcase--website-screenshots)
   - [Public Web Portal & Navigation](#1-public-web-portal--navigation)
   - [Unified Authentication & Role Matrix](#2-unified-authentication--role-matrix)
   - [PKI Security Operations Center (SOC)](#3-pki-security-operations-center-soc)
   - [Branch Mutual TLS (mTLS) Upload Service](#4-branch-mutual-tls-mtls-upload-service)
   - [Clinical Report Verification & Tamper Detection Lab](#5-clinical-report-verification--tamper-detection-lab)
   - [Stakeholder Portals (Pathologist, Doctor, Patient, Admin)](#6-stakeholder-portals)
   - [Printable Pathology Certificate with PAdES Digital Seal](#7-printable-pathology-certificate-with-pades-digital-seal)
   - [Evidence Gallery & Academic Documentation Viewer](#8-evidence-gallery--academic-documentation-viewer)
   - [Diagnostic Services & Branch Network](#9-diagnostic-services--branch-network)
5. [Core Cryptographic & Security Mechanisms](#-core-cryptographic--security-mechanisms)
6. [Formal Test Case Matrix (TC-01 through TC-16)](#-formal-test-case-matrix)
7. [Statutory Legal & Ethical Compliance](#-statutory-legal--ethical-compliance)
8. [Stakeholder Accounts & 1-Click Credentials](#-stakeholder-accounts--1-click-credentials)
9. [Project Directory Layout](#-project-directory-layout)
10. [Quick Start & Execution Guide](#-quick-start--execution-guide)

---

## 🌟 Executive Summary & Academic Context

**Spordha Diagnostics Ltd.** is a mission-critical clinical laboratory network comprising a central processing laboratory in Dhaka and three satellite branches across Dhanmondi, Uttara, and Mirpur. The enterprise processes high-consequence diagnostic data including dengue antigen panels, oncology biopsies, full haematology profiles, and infectious disease screenings.

This project delivers a **production-ready, zero-trust cryptographic medical laboratory web application** engineered specifically for **Track 2: PKI and Secure Communication** under CSE487. The platform solves the vulnerabilities of legacy clinical systems—such as shared passwords, unencrypted plain HTTP transport, lack of branch identity validation, absent certificate revocation enforcement, undetectable PDF tampering, and AI voice/phishing impersonation—by embedding a comprehensive Public Key Infrastructure (PKI) directly into everyday diagnostic workflows.

Every requirement from the official academic instruction document (`CSE487_Mini_Project_Student_Instructions (1).docx`), the 50-page formal report (`CSE487_Spordha_Report_Download.docx`), and the 28 live server terminal implementation proofs (`CSE487 Project Server.docx`) has been faithfully implemented, tested, and visualized.

---

## 🛠️ What Was Done in This Project

A high-level summary of the comprehensive engineering, design, and cryptographic work performed:

1. **Monochromatic Healthcare Design System:**
   - Engineered an ultra-modern, distraction-free visual design system using **Pure White (`#ffffff`), Deep Black (`#0a0a0a`), Charcoal (`#18181b`), Slate Gray (`#64748b`), and Light Subtle Gray (`#f4f4f5`)**.
   - Built custom CSS design tokens, responsive cards, crisp typography, clean data tables, interactive tabs, modal overlays, simulated Unix terminal windows, and printable medical report styling.

2. **Full-Stack Zero-Dependency Architecture:**
   - **Backend:** Pure PHP 8.x with native OpenSSL extensions and PDO database abstraction.
   - **Database:** Universal dual-engine support. Zero-configuration SQLite (`database/spordha.db`) with automatic self-seeding on first run, plus an enterprise MySQL/MariaDB production schema (`database/schema.sql`).
   - **Frontend:** Vanilla JavaScript (ES6+) with zero bloated third-party dependencies, featuring asynchronous AJAX endpoints, real-time SHA-256 calculation via Web Crypto API, dynamic CRL revocation toggles, and live tamper simulation.

3. **Two-Tier Certificate Authority Hierarchy (Requirement R4):**
   - Implemented an **Air-Gapped Offline Root CA** (RSA-2048, 10-year validity, 3650 days) that powers down immediately after issuing the intermediate authority.
   - Built an **Online Issuing CA** (RSA-2048, 5-year validity, 1825 days, `CA:TRUE`, `pathLen=0`, `keyCertSign`, `cRLSign`) responsible for signing strictly scoped operational leaf certificates.

4. **Modern TLS 1.3 & HSTS Enforcement (Requirement R5):**
   - Configured modern cipher suites (`TLS_AES_256_GCM_SHA384` with hybrid post-quantum key exchange `X25519MLKEM768`).
   - Enforced HTTP Strict Transport Security (`max-age=31536000; includeSubDomains; preload`).
   - Replicated protocol downgrade resistance: legacy TLS 1.0/1.1 connections fail immediately with TLS alert `protocol_version (582)`.

5. **Mutual TLS (mTLS) for Branch Telemetry (Requirement R6):**
   - Eliminated plain HTTP and shared branch passwords across Dhanmondi, Uttara, and Mirpur.
   - Configured individual X.509 client certificates with `clientAuth` Extended Key Usage (EKU).
   - Created an interactive simulator demonstrating HTTP 200 success on valid certs, and strict HTTP 400 rejection (zero bytes committed) on missing or revoked certs.

6. **Automated Multi-Threshold Expiry Warnings & Real-Time CRL (Requirement R7):**
   - Designed a proactive 3-stage notification engine: **30-day (Notice)**, **14-day (Warning)**, and **7-day (Critical Outage Risk)**.
   - Implemented interactive live Certificate Revocation List (CRL) distribution with one-click revocation and reinstatement toggles.

7. **Pathologist Clinical Report Signing & Live Tamper Lab (Requirement R8):**
   - Built a dedicated Pathologist Signing Studio where medical specialists approve and cryptographically sign diagnostic reports using RSA-2048 document keys.
   - Developed a public Report Verification Lab (`verify.php`) providing instant cryptographic verification (`Verified OK` vs `Tamper Detected`).
   - Created an interactive **Real-Time Tamper Testing Playground** that recalculates SHA-256 digests on keystroke, replicating OpenSSL error `bad signature (0x02000068)`.

8. **AI-Driven Threat Mitigation & Dual Approval Quorum (Requirement R13 / Section 8):**
   - Enforced **Rule SOP-SEC-04**: private keys marked non-exportable; emergency key export demands an offline dual-party cryptographic quorum (CISO + Lead PKI Admin).
   - Mitigates deepfake executive voice cloning and urgency-based phishing attempts.

9. **Dedicated Screenshots Directory:**
   - Captured 23 ultra-high-resolution interface screenshots using headless Chromium automation and stored them in the dedicated directory `<readme image/>`.
   - Copied 7 comprehensive system architecture and flow diagrams into `<readme image/>` for seamless self-contained viewing.

---

## 🏛️ System Architecture & Security Blueprints

All architectural blueprints are stored in `<readme image/>` and directly rendered below:

### Figure 1: Target Architecture Overview (D1)
Shows the complete zero-trust clinical ecosystem, trust boundaries, public DNS, and isolated internal networks.

<p align="center">
  <img src="readme image/D1_target_architecture.png" alt="Target Architecture Overview" width="900">
</p>

---

### Figure 2: Virtual Lab Network Topology (D3)
Details the network isolation between DMZ, branch subnets, PKI management networks, and central database servers.

<p align="center">
  <img src="readme image/D3_virtual_lab_topology.png" alt="Virtual Lab Topology" width="900">
</p>

---

### Figure 3: Trust Boundaries & STRIDE Threat Model (D7)
Analyzes threats across Spoofing, Tampering, Repudiation, Information Disclosure, Denial of Service, and Elevation of Privilege.

<p align="center">
  <img src="readme image/D7_trust_boundaries_stride.png" alt="Trust Boundaries & STRIDE" width="900">
</p>

---

### Figure 4: AI Impersonation Attack Tree (D6)
Models threat vectors including synthetic voice cloning, deepfake video calls, and social engineering targeting private keys.

<p align="center">
  <img src="readme image/D6_ai_impersonation_attack_tree.png" alt="AI Impersonation Attack Tree" width="900">
</p>

---

### Figure 5: Two-Tier Certificate Authority Hierarchy (D2)
Illustrates the trust chaining from the Air-Gapped Offline Root CA down to the Online Issuing CA and operational certificates.

<p align="center">
  <img src="readme image/D2_two_tier_ca_hierarchy.png" alt="Two-Tier CA Hierarchy" width="900">
</p>

---

### Figure 6: Mutual TLS (mTLS) Branch Upload Flow (D4)
Step-by-step cryptographic handshake and decision logic for requisition payloads transmitted from branch PC terminals.

<p align="center">
  <img src="readme image/D4_mtls_branch_upload_flow.png" alt="mTLS Branch Upload Flow" width="900">
</p>

---

### Figure 7: Clinical Report Signing & Tamper Verification Workflow (D5)
End-to-end cryptographic lifecycle: specimen analysis, SHA-256 payload digest, RSA signing, PDF embedding, and public verification.

<p align="center">
  <img src="readme image/D5_clinical_report_signing_workflow.png" alt="Clinical Report Signing Workflow" width="900">
</p>

---

## 📸 Visual Interface Showcase & Website Screenshots

All website screenshots below are stored inside the dedicated directory:  
**`readme image/`**

---

### 1. Public Web Portal & Navigation

#### 1.1 Homepage Hero Section & Live Telemetry
The public landing page introduces Spordha Diagnostics Ltd. with live cryptographic telemetry counters, zero-trust feature highlights, a quick-verification lookup box, and prominent role-switching shortcuts.

<p align="center">
  <img src="readme image/01_landing_hero.png" alt="Spordha Homepage Hero Section" width="900">
</p>

#### 1.2 Security Architecture & Core Pillars
Highlights the three central pillars of Track 2: Two-Tier CA trust hierarchy, TLS 1.3 with HSTS transport security, and Mutual TLS branch authentication.

<p align="center">
  <img src="readme image/02_landing_security_architecture.png" alt="Security Architecture Pillars" width="900">
</p>

---

### 2. Unified Authentication & Role Matrix

#### 2.1 Secure Sign-In & 1-Click Role Switcher
Features bulletproof session management, CSRF tokens, SHA-256 password hashing, and convenient **One-Click Persona Sign-In Buttons** for evaluators to switch between Patient, Doctor, Pathologist, Technician, Branch Staff, and PKI Administrator.

<p align="center">
  <img src="readme image/03_login_role_matrix.png" alt="Secure Sign-In & Role Matrix" width="900">
</p>

---

### 3. PKI Security Operations Center (SOC)

The SOC console (`pki_soc.php`) provides unified command and telemetry across all cryptographic components.

#### 3.1 Two-Tier Certificate Authority Hierarchy Visualizer
Displays real-time status of the Air-Gapped Offline Root CA and Online Issuing CA with subject DNs, serial numbers, cryptographic key parameters, and CRL distribution endpoints.

<p align="center">
  <img src="readme image/04_pki_soc_ca_hierarchy.png" alt="PKI SOC Two-Tier CA Hierarchy" width="900">
</p>

#### 3.2 Complete Certificate Inventory (Requirement A1)
A filterable directory of every certificate issued across the enterprise—including Root CA, Issuing CA, Nginx Web Server, mTLS Branch Clients, and Pathologist Document Signers—with instant revocation actions.

<p align="center">
  <img src="readme image/05_pki_soc_certificate_inventory.png" alt="PKI SOC Certificate Inventory" width="900">
</p>

#### 3.3 Multi-Threshold Expiry Risk Monitor (Requirement R7 / Evidence E-25)
Proactive multi-stage alerting engine categorizing certificates into **30-Day Notice (Yellow)**, **14-Day Warning (Amber)**, and **7-Day Critical Outage Risk (Red)** to eliminate unplanned service downtime.

<p align="center">
  <img src="readme image/06_pki_soc_expiry_warnings.png" alt="PKI SOC Expiry Risk Warnings" width="900">
</p>

#### 3.4 AI Threat Defense & Dual-Approval Quorum (Requirement R13 / Evidence E-28)
Enforces **Rule SOP-SEC-04**: non-exportable private keys, logging of deepfake voice-phishing incidents, and simulated dual-approval cryptographic quorum requirements.

<p align="center">
  <img src="readme image/07_pki_soc_ai_threat_defense.png" alt="PKI SOC AI Threat Defense" width="900">
</p>

#### 3.5 16 Academic Test Cases Verification Matrix (TC-01 through TC-16)
Comprehensive validation dashboard linking all 16 academic project test cases directly to their expected outputs and verified evidence items (E-01 to E-28).

<p align="center">
  <img src="readme image/08_pki_soc_test_cases_matrix.png" alt="PKI SOC Test Cases Matrix" width="900">
</p>

---

### 4. Branch Mutual TLS (mTLS) Upload Service

The mTLS service (`branch_upload.php`) simulates clinical sample requisition uploads from satellite branch PCs to the central laboratory.

#### 4.1 Valid Client Certificate — HTTP 200 OK Handshake
Demonstrates successful mutual authentication when a branch presents a valid X.509 certificate with `clientAuth` EKU. Payload is securely committed to the central database, and live simulated Nginx gateway logs are rendered.

<p align="center">
  <img src="readme image/09_branch_mtls_success.png" alt="Branch mTLS Valid Certificate HTTP 200 OK" width="900">
</p>

#### 4.2 Missing Client Certificate — HTTP 400 Bad Request Refusal (Evidence E-20)
Simulates an unauthorized or non-certificated device attempting to transmit patient data. Handshake is terminated with `400 No required SSL certificate was sent`, and exactly zero bytes are written to disk.

<p align="center">
  <img src="readme image/10_branch_mtls_rejected.png" alt="Branch mTLS Missing Certificate Refusal" width="900">
</p>

---

### 5. Clinical Report Verification & Tamper Detection Lab

The public verifier (`verify.php`) provides authoritative cryptographic verification for patients, doctors, and legal authorities.

#### 5.1 Authoritative Genuine Report — Verified OK (Evidence E-26)
Demonstrates verification of an authentic report (`SPD-2026-90412`). The SHA-256 payload digest matches the pathologist's RSA digital signature, displaying a bold green **VERIFIED OK** badge.

<p align="center">
  <img src="readme image/11_verify_genuine_report.png" alt="Clinical Report Verification Verified OK" width="900">
</p>

#### 5.2 Tampered Report — Integrity Check Failed (Evidence E-27)
Demonstrates detection of an altered report (`SPD-2026-90499`) where clinical findings were maliciously modified after issuance. The system flags the mismatch with a bright red **TAMPER DETECTED** banner, displaying OpenSSL error `bad signature (0x02000068)`.

<p align="center">
  <img src="readme image/12_verify_tampered_report.png" alt="Clinical Report Verification Tamper Detected" width="900">
</p>

#### 5.3 Interactive Real-Time Tamper Testing Playground
Allows users to type or edit report payloads directly in the browser. Using the Web Crypto API, the SHA-256 hash recalculates on every keystroke, letting evaluators simulate attacker alterations and witness instantaneous mathematical tamper detection.

<p align="center">
  <img src="readme image/13_interactive_tamper_playground.png" alt="Interactive Tamper Testing Playground" width="900">
</p>

---

### 6. Stakeholder Portals

#### 6.1 Pathologist Studio & RSA Digital Signature Approval (Evidence E-26)
Allows certified clinical pathologists to review pending specimen findings, inspect reference ranges, and apply their RSA-2048 cryptographic signature to seal approved reports.

<p align="center">
  <img src="readme image/14_pathologist_signing_studio.png" alt="Pathologist Approval & Signing Studio" width="900">
</p>

#### 6.2 Referring Doctor Consultation Portal
Provides consulting physicians with a streamlined roster of their referred patients, recent pathology findings, and one-click access to verified cryptographic report downloads.

<p align="center">
  <img src="readme image/15_doctor_consultation_portal.png" alt="Referring Doctor Consultation Portal" width="900">
</p>

#### 6.3 Patient Health Portal & Requisition Tracker
Provides patients with their diagnostic history, investigation status, and direct downloads for cryptographically sealed medical reports.

<p align="center">
  <img src="readme image/16_patient_health_portal.png" alt="Patient Health Records Portal" width="900">
</p>

#### 6.4 PKI Administrator Central Command & Telemetry Dashboard
Central administrative console displaying active sessions, server telemetry, system health, and navigation shortcuts to all operational modules.

<p align="center">
  <img src="readme image/17_admin_telemetry_dashboard.png" alt="Administrator Telemetry Dashboard" width="900">
</p>

---

### 7. Printable Pathology Certificate with PAdES Digital Seal

#### 7.1 Authentic Medical Report Certificate
A high-resolution, printable clinical report featuring laboratory metadata, patient demographics, clinical test results with reference flags, doctor remarks, dynamic QR verification code, and an official **PAdES Cryptographic Digital Seal Block**.

<p align="center">
  <img src="readme image/18_printable_pathology_report.png" alt="Printable Pathology Report Certificate" width="900">
</p>

---

### 8. Evidence Gallery & Academic Documentation Viewer

#### 8.1 Evidence Gallery (E-01 through E-28)
Interactive visual repository displaying all 28 implementation terminal captures from the server testing phase, organized by category (CA Setup, Server TLS, Client mTLS, Revocation & Expiry, Digital Signatures, and Threat Defense).

<p align="center">
  <img src="readme image/19_evidence_gallery.png" alt="Screenshot Evidence Gallery" width="900">
</p>

#### 8.2 Comprehensive Academic Documentation Viewer
An integrated 16-section viewer rendering the complete formal academic report directly inside the web portal, complete with requirements mapping, legal frameworks, and ethical case analyses.

<p align="center">
  <img src="readme image/20_academic_documentation.png" alt="Academic Documentation Viewer" width="900">
</p>

---

### 9. Diagnostic Services & Branch Network

#### 9.1 Diagnostic Test Catalog & Specimen Handling Protocols
Detailed directory of clinical laboratory tests, turnaround times, specimen requirements, and fee schedules.

<p align="center">
  <img src="readme image/21_diagnostic_services.png" alt="Diagnostic Services Catalog" width="900">
</p>

#### 9.2 Branch Directory & Network Topology
Profiles the Dhaka Central Processing Laboratory alongside satellite branches in Dhanmondi, Uttara, and Mirpur, displaying their designated mTLS subnet IP addresses and operational parameters.

<p align="center">
  <img src="readme image/22_branch_network_locations.png" alt="Diagnostic Branch Locations" width="900">
</p>

#### 9.3 About Spordha & Statutory Compliance Framework
Outlines corporate governance, quality certifications, and the statutory legal framework governing patient data security and digital signatures.

<p align="center">
  <img src="readme image/23_about_and_compliance.png" alt="About & Statutory Compliance" width="900">
</p>

---

## 🔒 Core Cryptographic & Security Mechanisms

### 1. Two-Tier CA Hierarchy (Requirement R4)
- **Tier 1 (Offline Root CA):** Key size RSA-2048/4096, validity 3650 days (10 years, Evidence E-05). Kept powered down in a simulated air-gap to isolate the root of trust from network-based compromises.
- **Tier 2 (Online Issuing CA):** Validity 1825 days (5 years, Evidence E-06), `basicConstraints = critical, CA:TRUE, pathlen:0`, `keyUsage = critical, digitalSignature, cRLSign, keyCertSign`. Dedicated to issuing strictly scoped end-entity certificates.

### 2. TLS 1.3 & HSTS Protocol Enforcement (Requirement R5)
- **Cipher Suite:** `TLS_AES_256_GCM_SHA384` with post-quantum hybrid key exchange `X25519MLKEM768` (Evidence E-17, E-22).
- **HSTS Header:** `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`.
- **Downgrade Attack Resistance:** Handshake attempts specifying legacy TLS 1.0 or TLS 1.1 are actively terminated with alert `protocol_version (582)` (Evidence E-18, TC-11).

### 3. Mutual TLS (mTLS) for Branch Telemetry (Requirement R6)
- **Client Identity Authentication:** Each branch client certificate contains Extended Key Usage `clientAuth` (`1.3.6.1.5.5.7.3.2`).
- **Strict Role Separation:** Server rejects certificates containing only `serverAuth` when presented as client credentials (Evidence E-22, TC-05).
- **Handshake Rejection:** Missing client certificates trigger HTTP 400 Bad Request, terminating the TCP session before any HTTP payload processing occurs (Evidence E-20).

### 4. CRL Revocation & Multi-Threshold Expiry Monitoring (Requirement R7)
- **Instant Revocation:** Revoked certificates are added to the Certificate Revocation List (`issuing-ca.crl`) and refused by the web server (Evidence E-23, TC-09).
- **Automated Alerts:** 30-day notice, 14-day warning, and 7-day critical outage risk thresholds prevent operational interruptions (Evidence E-25).

### 5. Document Signing & Real-Time Tamper Detection (Requirement R8)
- **Detached Digital Signature:** Clinical reports are hashed with SHA-256 and signed with the certifying pathologist's RSA-2048 private key.
- **Verification Integrity:** Verification checks recalculate the SHA-256 digest and verify it against the public key certificate (Evidence E-26).
- **Tamper Alert:** Any post-signature payload modification (e.g., changing test results or reference values) causes signature verification failure with OpenSSL error `bad signature (0x02000068)` (Evidence E-27, TC-14).

### 6. AI Threat Mitigation & Dual-Approval Quorum (Requirement R13 / Section 8)
- **Rule SOP-SEC-04:** Private keys are marked non-exportable and protected by hardware security module (HSM) simulation.
- **Dual-Quorum Requirement:** Emergency key exports require offline multi-party authorization (CISO + Lead PKI Admin) and formal incident logging to neutralize AI voice cloning and social engineering attacks (Evidence E-28).

---

## 🧪 Formal Test Case Matrix

The implementation validates all 16 academic project test cases (TC-01 through TC-16):

| Test Case | Objective / Description | Method / Evidence | Expected Result | Actual Result |
| :--- | :--- | :--- | :--- | :--- |
| **TC-01** | Verify Offline Root CA certificate | OpenSSL x509 CLI / Evidence E-05 | RSA-2048, 10-year validity, self-signed | **PASSED** (Matches E-05) |
| **TC-02** | Verify Online Issuing CA certificate | OpenSSL x509 CLI / Evidence E-06 | `CA:TRUE, pathlen:0`, 5-year validity | **PASSED** (Matches E-06) |
| **TC-03** | Verify complete CA trust chain | OpenSSL verify / Evidence E-07 | Trust path Root CA → Issuing CA: OK | **PASSED** (Verified OK) |
| **TC-04** | Verify Web Server TLS certificate | OpenSSL x509 CLI / Evidence E-08 | SAN: `spordha.test`, EKU: `serverAuth` | **PASSED** (Matches E-08) |
| **TC-05** | Verify Branch mTLS Client certificate | OpenSSL x509 CLI / Evidence E-09 | SAN: `branch-client.spordha.test`, EKU: `clientAuth` | **PASSED** (Matches E-09) |
| **TC-06** | Test mTLS upload with valid cert | curl mTLS / Evidence image38 | HTTP 200 OK, payload stored | **PASSED** (HTTP 200 OK) |
| **TC-07** | Test mTLS upload with missing cert | curl no-cert / Evidence E-20 | HTTP 400 Bad Request, 0 bytes committed | **PASSED** (HTTP 400 Rejected) |
| **TC-08** | Test mTLS upload with wrong EKU | curl server cert / Evidence E-22 | HTTP 400 Bad Request, role violation | **PASSED** (HTTP 400 Rejected) |
| **TC-09** | Test mTLS upload with revoked cert | curl revoked cert / Evidence E-23 | HTTP 400 Bad Request (CRL error) | **PASSED** (HTTP 400 Rejected) |
| **TC-10** | Verify TLS 1.3 cipher negotiation | curl / openssl s_client / Evidence E-17 | Cipher: `TLS_AES_256_GCM_SHA384` | **PASSED** (TLS 1.3 Active) |
| **TC-11** | Verify legacy TLS protocol rejection | openssl s_client -tls1_1 / Evidence E-18 | Handshake alert: `protocol_version (582)` | **PASSED** (Connection Refused) |
| **TC-12** | Verify automated expiry monitoring | Expiry check script / Evidence E-25 | 30/14/7-day multi-threshold alerts | **PASSED** (Alerts Triggered) |
| **TC-13** | Pathologist digital signature generation | OpenSSL dgst -sign / Evidence E-26 | Deterministic detached SHA-256 signature | **PASSED** (Signature Generated) |
| **TC-14** | Report tamper detection verification | OpenSSL dgst -verify / Evidence E-27 | Genuine: `Verified OK`; Tampered: `bad signature` | **PASSED** (Error 0x02000068) |
| **TC-15** | AI impersonation emergency key export | Security policy audit / Evidence E-28 | Export denied under Rule SOP-SEC-04 | **PASSED** (Export Blocked) |
| **TC-16** | End-to-end report verification | Web Verifier (`verify.php`) | Green badge for valid, Red alert for tampered | **PASSED** (Full E2E Pass) |

---

## ⚖️ Statutory Legal & Ethical Compliance

The system architecture was designed in strict alignment with Bangladeshi jurisprudence and international computing ethics:

1. **Cyber Security Act 2026 (Act 81 of 2026):**
   - **Section 18 & 19:** Comprehensive defense against illegal data access and computer system interference via mTLS cryptographic gating.
   - **Section 21 & 22:** Digital forgery and fraud prevention through cryptographic signing of all medical records.

2. **Personal Data Protection Act 2026 (Act 63 of 2026):**
   - Governs the lawful processing of sensitive health data, access logging, and mandatory 5-year cryptographic audit retention.

3. **Information and Communication Technology (ICT) Act 2006:**
   - Establishes legal validity for electronic signatures created via asymmetric cryptosystems (Section 7) and Controller of Certifying Authorities (CCA) licensing standards.

4. **Ethical Case Study & ACM Code of Ethics:**
   - **Scenario Analyzed:** Compromise of a pathologist signing key on the eve of Eid ul-Fitr.
   - **Ethical Decision:** Following ACM Code Principles **1.2 (Avoid Harm)**, **1.6 (Honor Confidentiality)**, and **1.7 (Honor Intellectual Property & Due Care)**, the compromise demands immediate certificate revocation on the CRL and patient notification, prioritizing human safety over commercial convenience.

---

## 👥 Stakeholder Accounts & 1-Click Credentials

The login portal includes **1-Click Quick Role Sign-In buttons** for instant evaluation without typing:

| Persona / Role | Name | Email / Identifier | Password | Access Privileges |
| :--- | :--- | :--- | :--- | :--- |
| **Patient** | Tanvir Ahmed | `patient@spordha.test` | `Patient@123` | Personal health records, signed report download, requisition tracking |
| **Patient** | Fahim Ahmed | `fahim.ahmed@spordha.test` | `Fahim@123` | Corporate patient portal access, test result history |
| **Referring Doctor** | Dr. Rezaul Karim | `doctor.karim@spordha.test` | `Doctor@123` | Referred patient consultations, clinical findings review, report verification |
| **Pathologist** | Dr. Nazmul Huda | `pathologist@spordha.test` | `Patho@123` | Specimen review, RSA digital signature approval, tamper lab |
| **Lab Technician** | Sabbir Hasan | `tech@spordha.test` | `Tech@123` | Specimen accessioning, diagnostic entry queue, sample registration |
| **Branch Staff** | Tahmina Chowdhury | `branch.uttara@spordha.test` | `Branch@123` | Satellite branch mTLS upload console, requisition transmissions |
| **PKI Administrator** | Md Shabbir Mia | `it-admin@spordha.test` | `12345` | Full SOC console: CA hierarchy, CRL revocation, AI incident audit |

*(All accounts also accept `Spordha@2026` or `password123` for convenience; the PKI Administrator also accepts `Admin@123` and `12345`.)*

---

## 📂 Project Directory Layout

```
SPORDHA DIAGNOSTICS LTD Website/
├── config/
│   └── database.php                    # Database connection (auto-seeding SQLite + MySQL toggle)
├── database/
│   ├── schema.sql                      # Production MySQL / MariaDB schema and seed data
│   └── spordha.db                      # Auto-seeded SQLite database (zero-configuration)
├── includes/
│   ├── auth.php                        # Session auth, RBAC enforcement, CSRF tokens
│   ├── functions.php                   # SHA-256 digests, RSA signatures, tamper checks, expiry alerts
│   ├── header.php                      # Sticky monochromatic navigation & live security bar
│   └── footer.php                      # Legal compliance notes, branch directory, course credits
├── assets/
│   ├── css/
│   │   ├── style.css                   # Monochromatic design system (white, black, charcoal, slate gray)
│   │   └── dashboard.css               # Metrics, terminal windows, printable certificate CSS
│   └── js/
│       ├── main.js                     # Tab switching, modals, clipboard, AJAX CRL toggle
│       └── verify.js                   # Web Crypto API real-time SHA-256 tamper calculation
├── readme image/                       # 📸 DEDICATED SCREENSHOTS & ARCHITECTURAL BLUEPRINTS FOLDER
│   ├── D1_target_architecture.png              (Figure 1 - Target Architecture Blueprint)
│   ├── D2_two_tier_ca_hierarchy.png            (Figure 5 - Two-Tier CA Trust Chain)
│   ├── D3_virtual_lab_topology.png             (Figure 2 - Virtual Lab Network Topology)
│   ├── D4_mtls_branch_upload_flow.png          (Figure 6 - Mutual TLS Handshake Flow)
│   ├── D5_clinical_report_signing_workflow.png (Figure 7 - PAdES Report Signing Workflow)
│   ├── D6_ai_impersonation_attack_tree.png     (Figure 4 - AI Threat Attack Tree)
│   ├── D7_trust_boundaries_stride.png          (Figure 3 - STRIDE Threat Boundaries)
│   ├── 01_landing_hero.png                     (Public Landing Hero & Live Telemetry)
│   ├── 02_landing_security_architecture.png    (Security Architecture Pillars)
│   ├── 03_login_role_matrix.png                (Secure Authentication & 1-Click Role Switcher)
│   ├── 04_pki_soc_ca_hierarchy.png             (PKI SOC CA Hierarchy Visualizer)
│   ├── 05_pki_soc_certificate_inventory.png    (PKI SOC Certificate Inventory)
│   ├── 06_pki_soc_expiry_warnings.png          (PKI SOC Expiry Risk Monitor)
│   ├── 07_pki_soc_ai_threat_defense.png        (PKI SOC AI Impersonation Quorum)
│   ├── 08_pki_soc_test_cases_matrix.png        (PKI SOC 16 Test Cases Matrix)
│   ├── 09_branch_mtls_success.png              (Branch mTLS Valid Certificate HTTP 200)
│   ├── 10_branch_mtls_rejected.png             (Branch mTLS Missing Certificate HTTP 400)
│   ├── 11_verify_genuine_report.png            (Report Verifier: Verified OK)
│   ├── 12_verify_tampered_report.png           (Report Verifier: Tamper Detected Alert)
│   ├── 13_interactive_tamper_playground.png    (Interactive Real-Time Tamper Testing Lab)
│   ├── 14_pathologist_signing_studio.png       (Pathologist RSA Digital Signing Studio)
│   ├── 15_doctor_consultation_portal.png       (Referring Doctor Consultation Console)
│   ├── 16_patient_health_portal.png            (Patient Health Records & Report Portal)
│   ├── 17_admin_telemetry_dashboard.png        (Administrator Central Telemetry Dashboard)
│   ├── 18_printable_pathology_report.png       (Printable Clinical Report with Digital Seal)
│   ├── 19_evidence_gallery.png                 (Evidence Gallery E-01 through E-28)
│   ├── 20_academic_documentation.png           (16-Section Academic Report Viewer)
│   ├── 21_diagnostic_services.png              (Diagnostic Test Catalog & Protocols)
│   ├── 22_branch_network_locations.png         (Branch Locations & mTLS IP Architecture)
│   └── 23_about_and_compliance.png             (About Spordha & Statutory Compliance)
├── assets1/                            # Course assets (diagrams and evidence part 1)
├── assets2/                            # Course assets (evidence part 2 and terminal logs)
├── api/
│   ├── verify_report.php               # JSON endpoint for programmatic report verification
│   ├── revoke_cert.php                 # JSON endpoint for dynamic CRL revocation / reinstatement
│   └── incident_action.php             # JSON endpoint for AI threat incident logging & quorum
├── index.php                           # Homepage, quick verification box, system metrics
├── login.php                           # Authentication portal with 1-click role switcher
├── logout.php                          # Secure session termination
├── dashboard.php                       # Role-aware central dashboard for all personas
├── patient_portal.php                  # Patient test history & certificate downloads
├── doctor_portal.php                   # Doctor consultation console & batch verification
├── pathologist_studio.php              # Pathologist digital signing studio & approval workflow
├── branch_upload.php                   # Interactive mTLS branch requisition simulator
├── pki_soc.php                         # PKI SOC: CA hierarchy, CRL, expiry alerts, AI defense
├── verify.php                          # Public report verification & interactive tamper lab
├── services.php                        # Diagnostic test catalog, fees, and turnaround times
├── branches.php                        # Branch directory (Dhaka, Dhanmondi, Uttara, Mirpur)
├── documentation.php                   # Full 16-section academic project report viewer
├── evidence_gallery.php                # Filterable gallery of 28 server terminal proofs (E-01–E-28)
├── generate_report.php                 # Authentic printable pathology report with digital seal
└── start_server.bat                    # One-click Windows / XAMPP launcher
```

---

## 🚀 Quick Start & Execution Guide

### Method 1: One-Click Windows Batch Launcher (Recommended)
1. Double-click **`start_server.bat`** in the project folder.
2. The batch script automatically locates your PHP executable (in `C:\xampp\php\php.exe` or system PATH), starts the development server at **`http://127.0.0.1:8080`**, and opens your default browser.

### Method 2: PowerShell / Command Line
```powershell
# Open terminal in project folder and run:
& "C:\xampp\php\php.exe" -S 127.0.0.1:8080
```
Then navigate to `http://127.0.0.1:8080` in your web browser.

### Method 3: XAMPP Apache
1. Copy or link this project folder into `C:\xampp\htdocs\spordha`.
2. Start the Apache module in the XAMPP Control Panel.
3. Open `http://localhost/spordha` in your browser.

> [!NOTE]
> The database works immediately out-of-the-box with zero setup using the SQLite database `database/spordha.db`. If you prefer MySQL, simply import `database/schema.sql` into phpMyAdmin and switch `define('DB_DRIVER', 'mysql');` in `config/database.php`.

---

## 🔬 Key Interactive Scenarios to Demonstrate to Evaluators

1. **Demonstrate Authentic Report Verification (Evidence E-26):**
   - Navigate to `http://127.0.0.1:8080/verify.php?uid=SPD-2026-90412`.
   - Observe the green **VERIFIED OK** badge, matching SHA-256 digest, and pathologist RSA signature details.

2. **Demonstrate Tampered Report Interception (Evidence E-27):**
   - Navigate to `http://127.0.0.1:8080/verify.php?uid=SPD-2026-90499`.
   - Observe the red **TAMPER DETECTED** banner, bad signature alert, and OpenSSL error `0x02000068`.

3. **Live Tamper Testing Playground:**
   - On `verify.php`, scroll to the **Interactive Real-Time Tamper Testing Playground**.
   - Click **"Simulate Attacker Tampering"** to observe real-time SHA-256 hash recalculation and signature failure.
   - Click **"Reset to Genuine Baseline"** to restore validation.

4. **Branch Mutual TLS (mTLS) Upload Simulation (Requirement R6):**
   - Visit `http://127.0.0.1:8080/branch_upload.php`.
   - Select **Valid Client Certificate** → Returns **HTTP 200 OK** and stores requisition.
   - Select **Missing Client Certificate** → Returns **HTTP 400 Bad Request** with zero bytes committed (Evidence E-20).

5. **PKI Security Operations Center (SOC) Console (Requirement R4/R7/R13):**
   - Visit `http://127.0.0.1:8080/pki_soc.php`.
   - Inspect the **Two-Tier CA Hierarchy** and **Certificate Inventory**.
   - Review multi-threshold **Expiry Risk Warnings** (30/14/7-day alerts).
   - Review **AI Threat Defense** logs and dual-approval quorum enforcement.

6. **Printable Medical Certificate with PAdES Digital Seal:**
   - From any verified report, click **"Open Authoritative PDF Report Certificate"** to view or print the clinical report complete with QR code and cryptographic seal block.

---

## 👨‍💻 Academic Credits & Acknowledgements

- **Course:** CSE487 Cyber Security, Ethics and Law
- **Institution:** East West University, Dhaka, Bangladesh
- **Academic Term:** Fall 2026
- **Developer:** Md Shabbir Mia (ID: 2022-2-60-048)
- **Special Thanks:** Course faculty and the Department of Computer Science & Engineering, East West University.

---
*Spordha Diagnostics Ltd. PKI & Medical Web Portal — East West University CSE487 Mini Project.*
