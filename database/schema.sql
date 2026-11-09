-- =============================================================================
-- Spordha Diagnostics Ltd. - Production Database Schema & Seed Data
-- Track 2: PKI and Secure Communication
-- CSE487 Cyber Security, Ethics and Law | Fall 2026 | East West University
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `spordha_diagnostics` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `spordha_diagnostics`;

-- 1. Branches Table
DROP TABLE IF EXISTS `sample_uploads`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `branches`;
DROP TABLE IF EXISTS `certificates`;
DROP TABLE IF EXISTS `incident_logs`;
DROP TABLE IF EXISTS `test_cases`;

CREATE TABLE `branches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `location` VARCHAR(200) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `client_cert_serial` VARCHAR(64) NOT NULL,
    `client_cert_status` VARCHAR(20) DEFAULT 'ACTIVE',
    `ip_address` VARCHAR(50) NOT NULL,
    `last_sync` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` VARCHAR(30) NOT NULL, -- 'patient', 'doctor', 'pathologist', 'technician', 'branch_staff', 'pki_admin'
    `phone` VARCHAR(30),
    `nid` VARCHAR(30),
    `branch_id` INT NULL,
    `designation` VARCHAR(100),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. PKI Certificates Table
CREATE TABLE `certificates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cert_type` VARCHAR(40) NOT NULL,
    `common_name` VARCHAR(150) NOT NULL,
    `san` VARCHAR(200),
    `serial_number` VARCHAR(64) NOT NULL UNIQUE,
    `key_algorithm` VARCHAR(50) DEFAULT 'RSA-2048',
    `key_size` INT DEFAULT 2048,
    `valid_from` DATETIME NOT NULL,
    `valid_to` DATETIME NOT NULL,
    `status` VARCHAR(20) DEFAULT 'VALID',
    `revocation_date` DATETIME NULL,
    `revocation_reason` VARCHAR(255) NULL,
    `fingerprint_sha256` VARCHAR(128) NOT NULL,
    `cert_path` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Medical Reports Table
CREATE TABLE `reports` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `report_uid` VARCHAR(40) NOT NULL UNIQUE,
    `patient_id` INT NOT NULL,
    `doctor_id` INT NULL,
    `pathologist_id` INT NULL,
    `branch_id` INT NULL,
    `test_name` VARCHAR(150) NOT NULL,
    `test_category` VARCHAR(100) NOT NULL,
    `specimen` VARCHAR(100) NOT NULL,
    `result_summary` VARCHAR(100) NOT NULL,
    `result_details` TEXT NOT NULL,
    `reference_range` TEXT,
    `status` VARCHAR(30) DEFAULT 'approved_signed',
    `is_tampered` TINYINT(1) DEFAULT 0,
    `signature_hash` VARCHAR(128) NOT NULL,
    `raw_data_hash` VARCHAR(128) NOT NULL,
    `signed_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`pathologist_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Incident Logs & AI Defense Table
CREATE TABLE `incident_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `incident_code` VARCHAR(40) NOT NULL UNIQUE,
    `threat_type` VARCHAR(100) NOT NULL,
    `sender` VARCHAR(150) NOT NULL,
    `recipient` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(200) NOT NULL,
    `details` TEXT NOT NULL,
    `action_taken` VARCHAR(100) NOT NULL,
    `dual_approval_status` VARCHAR(50) NOT NULL,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Sample Uploads Table (Branch Requisition Queue)
CREATE TABLE `sample_uploads` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL,
    `patient_name` VARCHAR(100) NOT NULL,
    `patient_nid` VARCHAR(30),
    `test_type` VARCHAR(120) NOT NULL,
    `specimen_id` VARCHAR(50) NOT NULL UNIQUE,
    `client_cert_used` VARCHAR(100) NOT NULL,
    `auth_status` VARCHAR(30) NOT NULL,
    `bytes_transferred` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Test Cases Matrix Table
CREATE TABLE `test_cases` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `test_id` VARCHAR(20) NOT NULL UNIQUE,
    `req_id` VARCHAR(30) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `expected_result` TEXT NOT NULL,
    `observed_result` TEXT NOT NULL,
    `status` VARCHAR(20) NOT NULL,
    `evidence_id` VARCHAR(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- SEED DATA
-- =============================================================================

INSERT INTO `branches` (`name`, `code`, `location`, `phone`, `client_cert_serial`, `client_cert_status`, `ip_address`) VALUES
('Central Laboratory - Dhaka', 'HQ-DHAKA', '124 Bir Uttam C.R. Datta Road, Dhaka-1205', '+880 2 9876543', '7A:9B:4C:11:8F:D2:01', 'ACTIVE', '10.0.2.2'),
('Dhanmondi Sample Branch', 'BR-DHANMONDI', 'Road 27, House 45, Dhanmondi R/A, Dhaka', '+880 1711 001122', '3E:88:99:A1:77:C3:02', 'ACTIVE', '10.0.2.14'),
('Uttara Sample Collection Branch', 'BR-UTTARA', 'Sector 7, Rabindra Sarani, Uttara, Dhaka', '+880 1812 334455', '5C:12:44:E9:90:F1:03', 'ACTIVE', '10.0.2.15'),
('Mirpur Diagnostic Branch', 'BR-MIRPUR', 'Mirpur-10 Circle, Section-6, Dhaka', '+880 1913 667788', '88:4D:23:BA:CC:45:04', 'ACTIVE', '10.0.2.16');

-- Password for all is their respective role demo password (e.g. Admin@123, Patient@123)
-- Admin@123: $2y$10$wO0oD5yQcYxSGp+jbAYDoBYCFCSi0OuAvCtd968hi8yXmYy9cyiLL (bcrypt)
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `phone`, `nid`, `branch_id`, `designation`) VALUES
('Md. Shabbir Mia', 'it-admin@spordha.test', '$2y$10$vN0o9L9tQdCvyTzH2R.8uOMtT4pW4E3G/L1c77m9Z3m3Q0oD5yQcY', 'pki_admin', '+880 1700 000001', '19951234567890', 1, 'Senior PKI Administrator'),
('Abrar Tanvir', 'it-officer2@spordha.test', '$2y$10$vN0o9L9tQdCvyTzH2R.8uOMtT4pW4E3G/L1c77m9Z3m3Q0oD5yQcY', 'pki_admin', '+880 1700 000002', '19961234567891', 1, 'Systems & Security Officer'),
('Dr. Nazmul Huda', 'pathologist@spordha.test', '$2y$10$E5zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'pathologist', '+880 1711 223344', '19801234567892', 1, 'Head of Clinical Pathology (MD)'),
('Dr. Farah Sharmin', 'pathologist2@spordha.test', '$2y$10$E5zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'pathologist', '+880 1711 223345', '19841234567893', 1, 'Consultant Hematologist (FCPS)'),
('Sabbir Hasan', 'tech@spordha.test', '$2y$10$F6zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'technician', '+880 1722 334455', '19981234567894', 1, 'Senior Medical Laboratory Technologist'),
('Nusrat Jahan', 'tech.mirpur@spordha.test', '$2y$10$F6zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'technician', '+880 1722 334456', '19991234567895', 4, 'Lab Technologist - Mirpur'),
('Dr. Rezaul Karim', 'doctor.karim@spordha.test', '$2y$10$G7zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'doctor', '+880 1733 445566', '19751234567896', 1, 'Associate Professor, Medicine (FCPS)'),
('Dr. Shamima Akhter', 'doctor.shamima@spordha.test', '$2y$10$G7zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'doctor', '+880 1733 445567', '19821234567897', 2, 'Consultant Cardiologist (MD)'),
('Rafiqul Islam', 'branch.dhanmondi@spordha.test', '$2y$10$H8zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'branch_staff', '+880 1744 556677', '19921234567898', 2, 'Branch In-Charge - Dhanmondi'),
('Tahmina Chowdhury', 'branch.uttara@spordha.test', '$2y$10$H8zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'branch_staff', '+880 1744 556678', '19931234567899', 3, 'Senior Reception Officer - Uttara'),
('Tanvir Ahmed', 'patient@spordha.test', '$2y$10$J9zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'patient', '+880 1755 667788', '19942619851234', 2, 'Corporate Employee (Patient ID: P-10024)'),
('Begum Rokeya', 'rokeya@spordha.test', '$2y$10$J9zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'patient', '+880 1755 667789', '19652619851235', 3, 'Outpatient (Patient ID: P-10025)'),
('Kazi Mahmudul Hasan', 'mahmud@spordha.test', '$2y$10$J9zXjP7Y6B5a9T0aQ8oYeO3Q3m8L7m9Z3m3Q0oD5yQcYxSGp+jbAY', 'patient', '+880 1755 667790', '19892619851236', 1, 'Corporate Health Check (Patient ID: P-10026)');

INSERT INTO `certificates` (`cert_type`, `common_name`, `san`, `serial_number`, `key_algorithm`, `key_size`, `valid_from`, `valid_to`, `status`, `revocation_date`, `revocation_reason`, `fingerprint_sha256`, `cert_path`) VALUES
('root_ca', 'Spordha Root CA', 'spordha-root-ca.spordha.test', '00:E1:92:B5:44:AA:21:00', 'RSA-2048', 2048, '2026-11-09 04:00:00', '2036-11-06 04:00:00', 'VALID', NULL, NULL, 'A1:F8:8C:33:91:2E:8D:74:10:9B:EE:CC:54:12:09:88:D1:66:34:77:89:12:34:AA:BB:CC:DD:EE:FF:00:11:22', '/pki/root-ca/root-ca.crt'),
('issuing_ca', 'Spordha Issuing CA', 'spordha-issuing-ca.spordha.test', '44:98:A2:7B:19:32:0C:55', 'RSA-2048', 2048, '2026-11-09 04:30:00', '2031-11-08 04:30:00', 'VALID', NULL, NULL, '88:E2:44:BC:77:AA:90:12:33:DD:55:FF:AA:00:11:44:88:99:33:11:77:88:AA:BC:DE:F0:12:34:56:78:9A:BC', '/pki/issuing-ca/issuing-ca.crt'),
('portal_tls', 'portal.spordha.test', 'DNS:portal.spordha.test, IP:127.0.0.1, IP:10.0.2.2', '6A:88:41:22:90:33:4B:99', 'RSA-2048', 2048, '2026-11-09 04:51:25', '2027-11-09 04:51:25', 'VALID', NULL, NULL, 'C3:77:19:80:FF:22:54:BB:AA:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77', '/etc/nginx/ssl/server.crt'),
('upload_tls', 'upload.spordha.test', 'DNS:upload.spordha.test, IP:127.0.0.1, IP:10.0.2.2', '7B:11:90:AA:34:EF:22:01', 'RSA-2048', 2048, '2026-11-09 04:51:25', '2027-11-09 04:51:25', 'VALID', NULL, NULL, 'D4:88:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF', '/etc/nginx/ssl/upload.crt'),
('branch_client', 'branch-uttara.spordha.test', 'DNS:branch-uttara.spordha.test, IP:10.0.2.15', '5C:12:44:E9:90:F1:03', 'RSA-2048', 2048, '2026-11-09 05:00:00', '2027-02-07 05:00:00', 'VALID', NULL, NULL, 'F1:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00', '/pki/client/branch-client.crt'),
('branch_client', 'branch-revoked-rogue.spordha.test', 'DNS:branch-rogue.spordha.test', '99:AA:BB:CC:DD:EE:00:11', 'RSA-2048', 2048, '2026-10-01 00:00:00', '2027-01-01 00:00:00', 'REVOKED', '2026-11-08 14:22:00', 'Compromised hardware token reported at branch', '00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF', '/pki/client/revoked.crt'),
('doc_signing', 'Dr. Nazmul Huda - Clinical Pathology Document Signer', 'email:pathologist@spordha.test', '2A:77:CD:99:12:00:44:88', 'RSA-2048', 2048, '2026-11-09 00:00:00', '2027-11-09 00:00:00', 'VALID', NULL, NULL, '55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44', '/pki/signing/dr_nazmul_signer.crt');

INSERT INTO `reports` (`report_uid`, `patient_id`, `doctor_id`, `pathologist_id`, `branch_id`, `test_name`, `test_category`, `specimen`, `result_summary`, `result_details`, `reference_range`, `status`, `is_tampered`, `signature_hash`, `raw_data_hash`, `signed_at`) VALUES
('SPD-2026-90412', 11, 7, 3, 2, 'Dengue NS1 Antigen & Platelet Count', 'Serology & Hematology', 'Blood (Serum & EDTA)', 'Negative', '{\"Dengue NS1 Antigen\":{\"value\":\"Negative\",\"unit\":\"Index\",\"ref\":\"Negative (<0.9)\"},\"Platelet Count\":{\"value\":\"245,000\",\"unit\":\"/cumm\",\"ref\":\"150,000 - 450,000\"},\"Hemoglobin (Hb)\":{\"value\":\"14.8\",\"unit\":\"g/dL\",\"ref\":\"13.0 - 17.0\"},\"Total Leucocyte Count (TLC)\":{\"value\":\"6,800\",\"unit\":\"/cumm\",\"ref\":\"4,000 - 11,000\"}}', 'Dengue NS1: Negative; Platelets: Normal range.', 'approved_signed', 0, 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', 'f4b304c4f9bc298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b785', '2026-11-09 05:30:00'),
('SPD-2026-90413', 12, 8, 3, 3, 'Comprehensive Lipid Profile & Fasting Blood Sugar', 'Clinical Biochemistry', 'Blood (Fluoride & Serum)', 'Borderline High Cholesterol', '{\"Fasting Blood Glucose\":{\"value\":\"5.8\",\"unit\":\"mmol/L\",\"ref\":\"4.1 - 5.9\"},\"Total Cholesterol\":{\"value\":\"218\",\"unit\":\"mg/dL\",\"ref\":\"< 200 Desirable\"},\"Triglycerides\":{\"value\":\"165\",\"unit\":\"mg/dL\",\"ref\":\"< 150 Normal\"},\"HDL Cholesterol\":{\"value\":\"48\",\"unit\":\"mg/dL\",\"ref\":\"> 40 Normal\"},\"LDL Cholesterol\":{\"value\":\"137\",\"unit\":\"mg/dL\",\"ref\":\"< 100 Optimal\"}}', 'Lipid parameters indicate mild dyslipidemia. Dietary modification advised.', 'approved_signed', 0, 'a7c1b44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b866', 'b8d204c4f9bc298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b799', '2026-11-09 06:15:00'),
('SPD-2026-90414', 13, 7, 4, 1, 'Complete Blood Count (CBC) with ESR', 'Hematology', 'Whole Blood (EDTA)', 'Normal Hemogram', '{\"Hemoglobin\":{\"value\":\"15.2\",\"unit\":\"g/dL\",\"ref\":\"13.5 - 17.5\"},\"ESR (Westergren)\":{\"value\":\"12\",\"unit\":\"mm in 1st hr\",\"ref\":\"0 - 15\"},\"RBC Count\":{\"value\":\"5.1\",\"unit\":\"mil/uL\",\"ref\":\"4.5 - 5.9\"},\"Neutrophils\":{\"value\":\"62\",\"unit\":\"%\",\"ref\":\"40 - 75\"},\"Lymphocytes\":{\"value\":\"30\",\"unit\":\"%\",\"ref\":\"20 - 45\"}}', 'Hemogram within physiological biological reference intervals.', 'approved_signed', 0, 'c9e3b44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b877', 'd0f404c4f9bc298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b700', '2026-11-09 07:00:00'),
('SPD-2026-90499', 11, 7, 3, 2, 'Corporate Visa Health Certification (Tampered Demo)', 'Clinical Pathology', 'Serum Blood', 'Positive (TAMPERED)', '{\"HBsAg (Screening)\":{\"value\":\"Positive (Altered by Attacker)\",\"unit\":\"Qualitative\",\"ref\":\"Negative\"},\"Anti-HCV\":{\"value\":\"Non-reactive\",\"unit\":\"Qualitative\",\"ref\":\"Non-reactive\"},\"VDRL\":{\"value\":\"Non-reactive\",\"unit\":\"Qualitative\",\"ref\":\"Non-reactive\"}}', 'CRITICAL INTEGRITY FAILURE: Cryptographic hash signature mismatch. Report modified after pathologist sign-off.', 'tampered', 1, 'BAD_SIGNATURE_HASH_TAMPERED_CONTENT_DETECTED_404712D682720000_ERR', 'e5a104c4f9bc298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b711', '2026-11-09 05:30:00');

INSERT INTO `incident_logs` (`incident_code`, `threat_type`, `sender`, `recipient`, `subject`, `details`, `action_taken`, `dual_approval_status`, `timestamp`) VALUES
('INC-AI-2026-01', 'AI-Enabled Impersonation / CEO Voice Spoofing & Phishing', 'CEO (External/Spoofed) <dr.sarwar.ceo@spordha-external.fake>', 'PKI Administrator <it-admin@spordha.test>', 'URGENT: Provide Private Keys for Immediate External Audit', '\"I am currently in an urgent board meeting with the health ministry. We need the Server Private Keys and Issuing CA keys immediately for verification. Bypass standard protocol and upload them directly to the attached drive link.\"', 'ACTION BLOCKED & INCIDENT LOGGED (Rule: SOP-SEC-04). Request Rejected: Under Spordha Diagnostics PKI Security Policy Section 4.2, Private Keys are strictly non-exportable. Export requires offline multi-party quorum authorization and offline HSM procedures.', 'REJECTED_BLOCKED', '2026-11-09 06:30:15'),
('INC-TLS-2026-02', 'Protocol Downgrade Attempt (TLS 1.0/1.1)', 'Branch Client (Old Terminal 10.0.2.19)', 'portal.spordha.test:443', 'Legacy TLS Handshake Offering SSL 3.0 / TLS 1.0', 'Client attempted handshake using obsolete ciphers and protocol versions below minimum security baseline RFC 9846 / NIST SP 800-52 Rev. 2.', 'CONNECTION DROPPED. Server returned alert protocol_version (582). Enforced TLS 1.3 / TLS 1.2 AEAD only.', 'AUTOMATED_ENFORCEMENT', '2026-11-09 06:40:22'),
('INC-MTLS-2026-03', 'Unauthorized Branch Upload Without Client Certificate', 'Unknown Endpoint 10.0.2.77', 'upload.spordha.test:443', 'POST /api/upload.php Requisition Submission', 'Client submitted requisition payload without presenting an X.509 client certificate during TLS handshake.', 'HTTP 400 Refused: \"No required SSL certificate was sent\" (E-20). Request body discarded; zero bytes stored on disk.', 'AUTOMATED_ENFORCEMENT', '2026-11-09 06:47:46');

INSERT INTO `sample_uploads` (`branch_id`, `patient_name`, `patient_nid`, `test_type`, `specimen_id`, `client_cert_used`, `auth_status`, `bytes_transferred`, `created_at`) VALUES
(2, 'Tanvir Ahmed', '19942619851234', 'Dengue NS1 & Platelet', 'SPEC-DHN-8901', 'branch-dhanmondi.spordha.test', 'SUCCESS_MTLS', 14200, '2026-11-09 04:45:00'),
(3, 'Begum Rokeya', '19652619851235', 'Lipid Profile & FBS', 'SPEC-UTT-4402', 'branch-uttara.spordha.test', 'SUCCESS_MTLS', 18950, '2026-11-09 05:10:00'),
(4, 'Jahangir Alam', '19772619851288', 'COVID-19 RT-PCR', 'SPEC-MIR-1109', 'branch-mirpur.spordha.test', 'SUCCESS_MTLS', 12300, '2026-11-09 05:40:00'),
(3, 'Unauthenticated Device', 'UNKNOWN', 'CBC Requisition', 'SPEC-FAIL-0001', 'NONE', 'REJECTED_NO_CERT', 0, '2026-11-09 06:47:46');

INSERT INTO `test_cases` (`test_id`, `req_id`, `category`, `description`, `expected_result`, `observed_result`, `status`, `evidence_id`) VALUES
('TC-01', 'R4/R5', 'Chain & Trust', 'Trusted server chain and portal hostname validation', 'Trusted chain and SAN accepted', 'OpenSSL chain OK; portal cert verified and SAN matched', 'PASS', 'E-07, E-17'),
('TC-02', 'R4', 'Chain & Trust', 'Certificate from untrusted CA presented by server', 'Client refuses connection with untrusted CA error', 'Connection refused by client certificate path validator', 'PASS', 'Lab Sandbox'),
('TC-03', 'R5', 'Chain & Trust', 'Hostname mismatch test (e.g. wrong SAN presented)', 'Client refuses mismatched SAN with hostname validation error', 'Refused: hostname does not match subjectAltName', 'PASS', 'Lab Sandbox'),
('TC-04', 'R4', 'Wrong Issuance', 'Issuance without dual approval / allow-list check', 'Issuance blocked or alert raised in audit trail', 'Blocked: dual-approval required for CA leaf generation', 'PASS', 'E-28'),
('TC-05', 'R4/R6', 'Wrong Issuance', 'Wrong certificate type / EKU (branch cert used as server cert)', 'Branch client certificate rejected as server certificate', 'Rejected: Extended Key Usage missing serverAuth', 'PASS', 'E-22'),
('TC-06', 'R6', 'Branch mTLS', 'Missing branch client certificate on upload endpoint', 'Handshake refused; no data stored; error logged', 'Refused: \"No required SSL certificate was sent\"; 0 bytes stored', 'PASS', 'E-20'),
('TC-07', 'R6', 'Branch mTLS', 'Untrusted branch client certificate from foreign CA', 'Handshake refused and security audit event logged', 'Connection terminated; client certificate untrusted', 'PASS', 'Lab Sandbox'),
('TC-08', 'R6/R7', 'Expiry & Revocation', 'Expired branch client certificate presented', 'Expired certificate refused and expiry alert raised', 'Refused: certificate has expired (notAfter check failed)', 'PASS', 'E-13, E-25'),
('TC-09', 'R7', 'Expiry & Revocation', 'Revoked branch client certificate presented', 'Revoked certificate refused within stated SLA (<15 mins)', 'Refused: certificate revoked in CRL / OCSP responder', 'PASS', 'E-23'),
('TC-10', 'R7', 'Expiry & Revocation', 'Automated certificate expiry warning notifications', 'Automated warning at 30, 14, and 7 day thresholds', 'Warning generated: Certificate valid; renewal alert scheduled', 'PASS', 'E-25'),
('TC-11', 'R5', 'Downgrade Defense', 'Protocol downgrade attempt (TLS 1.0 / TLS 1.1)', 'Server refuses connection below TLS 1.2/1.3 baseline', 'Handshake failed: TLS alert protocol_version (582)', 'PASS', 'E-18'),
('TC-12', 'R5', 'Downgrade Defense', 'Weak cipher suite offering (CBC mode, RC4, 3DES, NULL)', 'Server refuses weak ciphers; requires AEAD (GCM/Poly1305)', 'Connection closed: no shared acceptable cipher suites', 'PASS', 'Lab Sandbox'),
('TC-13', 'R8', 'Report Signing', 'Genuine signed PDF report verification', 'PDF signature validates with dedicated document-signing cert', 'Verified OK: SHA-256 signature matches clinical payload', 'PASS', 'E-26'),
('TC-14', 'R8', 'Report Signing', 'Edited / tampered clinical report verification', 'Modified report is flagged as tampered / invalid signature', 'Verification failure: bad signature detected (tamper detected)', 'PASS', 'E-27'),
('TC-15', 'R13', 'AI-Driven Threat', 'Urgent unverified request for private keys / unauthorized action', 'Key export refused; non-exportable rule enforced; incident logged', 'Rejected: dual-approval required; incident logged (Rule SOP-SEC-04)', 'PASS', 'E-15, E-16, E-28'),
('TC-16', 'R5', 'TLS & HSTS', 'Portal TLS 1.3 handshake and HSTS emission', 'TLS 1.3 negotiated, valid certificate, max-age HSTS emitted', 'TLSv1.3, X25519MLKEM768, Strict-Transport-Security verified', 'PASS', 'E-17');
