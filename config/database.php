<?php
/**
 * Spordha Diagnostics Ltd. - Database Configuration & Initialization
 * High-Security Diagnostic Laboratory PKI Portal
 * 
 * Supports both SQLite (zero-config, portable) and MySQL/MariaDB.
 */

// Configuration options
define('DB_DRIVER', 'sqlite'); // 'sqlite' or 'mysql'

// MySQL configuration (if DB_DRIVER == 'mysql')
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'spordha_diagnostics');
define('DB_USER', 'root');
define('DB_PASS', '');

// SQLite configuration
define('SQLITE_FILE', __DIR__ . '/../database/spordha.db');

/**
 * Get PDO Database Connection
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        if (DB_DRIVER === 'sqlite') {
            $dbDir = dirname(SQLITE_FILE);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }
            $isNew = !file_exists(SQLITE_FILE) || filesize(SQLITE_FILE) === 0;
            
            $pdo = new PDO('sqlite:' . SQLITE_FILE);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON;');

            if ($isNew) {
                initializeDatabase($pdo, 'sqlite');
            }
        } else {
            // MySQL Connection
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        }
        return $pdo;
    } catch (PDOException $e) {
        // If MySQL fails, fallback to SQLite automatically for fault-tolerance
        if (DB_DRIVER !== 'sqlite') {
            try {
                $isNew = !file_exists(SQLITE_FILE) || filesize(SQLITE_FILE) === 0;
                $pdo = new PDO('sqlite:' . SQLITE_FILE);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                if ($isNew) {
                    initializeDatabase($pdo, 'sqlite');
                }
                return $pdo;
            } catch (Exception $fallbackEx) {
                die("Database Connection Error: " . htmlspecialchars($fallbackEx->getMessage()));
            }
        }
        die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
    }
}

/**
 * Initialize Tables and Seed Initial Data
 */
function initializeDatabase(PDO $pdo, string $driver = 'sqlite') {
    // Table schemas
    $queries = [
        // Branches
        "CREATE TABLE IF NOT EXISTS branches (
            id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
            name VARCHAR(100) NOT NULL,
            code VARCHAR(20) NOT NULL UNIQUE,
            location VARCHAR(200) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            client_cert_serial VARCHAR(64) NOT NULL,
            client_cert_status VARCHAR(20) DEFAULT 'ACTIVE',
            ip_address VARCHAR(50) NOT NULL,
            last_sync DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        // Users
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
            name VARCHAR(100) NOT NULL,
            email VARCHAR(120) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(30) NOT NULL, -- 'patient', 'doctor', 'pathologist', 'technician', 'branch_staff', 'pki_admin'
            phone VARCHAR(30),
            nid VARCHAR(30),
            branch_id INTEGER,
            designation VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL
        );",

        // PKI Certificates
        "CREATE TABLE IF NOT EXISTS certificates (
            id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
            cert_type VARCHAR(40) NOT NULL, -- 'root_ca', 'issuing_ca', 'portal_tls', 'upload_tls', 'branch_client', 'doc_signing'
            common_name VARCHAR(150) NOT NULL,
            san VARCHAR(200),
            serial_number VARCHAR(64) NOT NULL UNIQUE,
            key_algorithm VARCHAR(50) DEFAULT 'RSA-2048',
            key_size INTEGER DEFAULT 2048,
            valid_from DATETIME NOT NULL,
            valid_to DATETIME NOT NULL,
            status VARCHAR(20) DEFAULT 'VALID', -- 'VALID', 'REVOKED', 'EXPIRED'
            revocation_date DATETIME,
            revocation_reason VARCHAR(255),
            fingerprint_sha256 VARCHAR(128) NOT NULL,
            cert_path VARCHAR(255)
        );",

        // Medical Reports
        "CREATE TABLE IF NOT EXISTS reports (
            id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
            report_uid VARCHAR(40) NOT NULL UNIQUE,
            patient_id INTEGER NOT NULL,
            doctor_id INTEGER,
            pathologist_id INTEGER,
            branch_id INTEGER,
            test_name VARCHAR(150) NOT NULL,
            test_category VARCHAR(100) NOT NULL,
            specimen VARCHAR(100) NOT NULL,
            result_summary VARCHAR(100) NOT NULL,
            result_details TEXT NOT NULL,
            reference_range TEXT,
            status VARCHAR(30) DEFAULT 'approved_signed', -- 'pending_sample', 'processing', 'pathologist_review', 'approved_signed', 'tampered'
            is_tampered INTEGER DEFAULT 0,
            signature_hash VARCHAR(128) NOT NULL,
            raw_data_hash VARCHAR(128) NOT NULL,
            signed_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (patient_id) REFERENCES users(id),
            FOREIGN KEY (doctor_id) REFERENCES users(id),
            FOREIGN KEY (pathologist_id) REFERENCES users(id),
            FOREIGN KEY (branch_id) REFERENCES branches(id)
        );",

        // Incident Logs & Dual Approval
        "CREATE TABLE IF NOT EXISTS incident_logs (
            id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
            incident_code VARCHAR(40) NOT NULL UNIQUE,
            threat_type VARCHAR(100) NOT NULL,
            sender VARCHAR(150) NOT NULL,
            recipient VARCHAR(150) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            details TEXT NOT NULL,
            action_taken VARCHAR(100) NOT NULL,
            dual_approval_status VARCHAR(50) NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        // Branch Requisition Uploads
        "CREATE TABLE IF NOT EXISTS sample_uploads (
            id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
            branch_id INTEGER NOT NULL,
            patient_name VARCHAR(100) NOT NULL,
            patient_nid VARCHAR(30),
            test_type VARCHAR(120) NOT NULL,
            specimen_id VARCHAR(50) NOT NULL UNIQUE,
            client_cert_used VARCHAR(100) NOT NULL,
            auth_status VARCHAR(30) NOT NULL, -- 'SUCCESS_MTLS', 'REJECTED_NO_CERT', 'REJECTED_REVOKED'
            bytes_transferred INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (branch_id) REFERENCES branches(id)
        );",

        // Test Cases Matrix (TC-01 through TC-16)
        "CREATE TABLE IF NOT EXISTS test_cases (
            id INTEGER PRIMARY KEY " . ($driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT') . ",
            test_id VARCHAR(20) NOT NULL UNIQUE,
            req_id VARCHAR(30) NOT NULL,
            category VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            expected_result TEXT NOT NULL,
            observed_result TEXT NOT NULL,
            status VARCHAR(20) NOT NULL, -- 'PASS', 'PARTIAL', 'INCONCLUSIVE', 'NOT RUN'
            evidence_id VARCHAR(40) NOT NULL
        );"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }

    // Seed Data
    seedInitialData($pdo);
}

/**
 * Seed Comprehensive Initial Data from CSE487 Docx Files
 */
function seedInitialData(PDO $pdo) {
    // 1. Seed Branches
    $branches = [
        ['Central Laboratory - Dhaka', 'HQ-DHAKA', '124 Bir Uttam C.R. Datta Road, Dhaka-1205', '+880 2 9876543', '7A:9B:4C:11:8F:D2:01', 'ACTIVE', '10.0.2.2'],
        ['Dhanmondi Sample Branch', 'BR-DHANMONDI', 'Road 27, House 45, Dhanmondi R/A, Dhaka', '+880 1711 001122', '3E:88:99:A1:77:C3:02', 'ACTIVE', '10.0.2.14'],
        ['Uttara Sample Collection Branch', 'BR-UTTARA', 'Sector 7, Rabindra Sarani, Uttara, Dhaka', '+880 1812 334455', '5C:12:44:E9:90:F1:03', 'ACTIVE', '10.0.2.15'],
        ['Mirpur Diagnostic Branch', 'BR-MIRPUR', 'Mirpur-10 Circle, Section-6, Dhaka', '+880 1913 667788', '88:4D:23:BA:CC:45:04', 'ACTIVE', '10.0.2.16']
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO branches (name, code, location, phone, client_cert_serial, client_cert_status, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($branches as $b) {
        $stmt->execute($b);
    }

    // Default password hash for all demo accounts: 'Spordha@2026' or specific simple pass
    $passPatient = password_hash('Patient@123', PASSWORD_BCRYPT);
    $passDoctor = password_hash('Doctor@123', PASSWORD_BCRYPT);
    $passPatho = password_hash('Patho@123', PASSWORD_BCRYPT);
    $passTech = password_hash('Tech@123', PASSWORD_BCRYPT);
    $passBranch = password_hash('Branch@123', PASSWORD_BCRYPT);
    $passAdmin = password_hash('12345', PASSWORD_BCRYPT);
    $passFahim = password_hash('Fahim@123', PASSWORD_BCRYPT);

    // 2. Seed Users
    $users = [
        // PKI Admins / IT Officers
        ['Md Shabbir Mia', 'it-admin@spordha.test', $passAdmin, 'pki_admin', '+880 1700 000001', '19951234567890', 1, 'Senior PKI Administrator'],
        ['Abrar Tanvir', 'it-officer2@spordha.test', $passAdmin, 'pki_admin', '+880 1700 000002', '19961234567891', 1, 'Systems & Security Officer'],

        // Pathologists
        ['Dr. Nazmul Huda', 'pathologist@spordha.test', $passPatho, 'pathologist', '+880 1711 223344', '19801234567892', 1, 'Head of Clinical Pathology (MD)'],
        ['Dr. Farah Sharmin', 'pathologist2@spordha.test', $passPatho, 'pathologist', '+880 1711 223345', '19841234567893', 1, 'Consultant Hematologist (FCPS)'],

        // Technicians
        ['Sabbir Hasan', 'tech@spordha.test', $passTech, 'technician', '+880 1722 334455', '19981234567894', 1, 'Senior Medical Laboratory Technologist'],
        ['Nusrat Jahan', 'tech.mirpur@spordha.test', $passTech, 'technician', '+880 1722 334456', '19991234567895', 4, 'Lab Technologist - Mirpur'],

        // Referring Doctors
        ['Dr. Rezaul Karim', 'doctor.karim@spordha.test', $passDoctor, 'doctor', '+880 1733 445566', '19751234567896', 1, 'Associate Professor, Medicine (FCPS)'],
        ['Dr. Shamima Akhter', 'doctor.shamima@spordha.test', $passDoctor, 'doctor', '+880 1733 445567', '19821234567897', 2, 'Consultant Cardiologist (MD)'],

        // Branch Staff
        ['Rafiqul Islam', 'branch.dhanmondi@spordha.test', $passBranch, 'branch_staff', '+880 1744 556677', '19921234567898', 2, 'Branch In-Charge - Dhanmondi'],
        ['Tahmina Chowdhury', 'branch.uttara@spordha.test', $passBranch, 'branch_staff', '+880 1744 556678', '19931234567899', 3, 'Senior Reception Officer - Uttara'],

        // Patients
        ['Tanvir Ahmed', 'patient@spordha.test', $passPatient, 'patient', '+880 1755 667788', '19942619851234', 2, 'Corporate Employee (Patient ID: P-10024)'],
        ['Begum Rokeya', 'rokeya@spordha.test', $passPatient, 'patient', '+880 1755 667789', '19652619851235', 3, 'Outpatient (Patient ID: P-10025)'],
        ['Kazi Mahmudul Hasan', 'mahmud@spordha.test', $passPatient, 'patient', '+880 1755 667790', '19892619851236', 1, 'Corporate Health Check (Patient ID: P-10026)'],
        ['Fahim Ahmed', 'fahim.ahmed@spordha.test', $passFahim, 'patient', '+880 1755 667791', '19912619851237', 1, 'Corporate Employee (Patient ID: P-10027)']
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (name, email, password_hash, role, phone, nid, branch_id, designation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute($u);
    }

    // 3. Seed Certificates (matching evidence E-05, E-06, E-07, E-13, E-17, E-22, etc.)
    $certificates = [
        [
            'root_ca',
            'Spordha Root CA',
            'spordha-root-ca.spordha.test',
            '00:E1:92:B5:44:AA:21:00',
            'RSA-2048',
            2048,
            '2026-11-09 04:00:00',
            '2036-11-06 04:00:00', // 10 years (3650 days from E-05)
            'VALID',
            null,
            null,
            'A1:F8:8C:33:91:2E:8D:74:10:9B:EE:CC:54:12:09:88:D1:66:34:77:89:12:34:AA:BB:CC:DD:EE:FF:00:11:22',
            '/pki/root-ca/root-ca.crt'
        ],
        [
            'issuing_ca',
            'Spordha Issuing CA',
            'spordha-issuing-ca.spordha.test',
            '44:98:A2:7B:19:32:0C:55',
            'RSA-2048',
            2048,
            '2026-11-09 04:30:00',
            '2031-11-08 04:30:00', // 5 years (1825 days from E-06)
            'VALID',
            null,
            null,
            '88:E2:44:BC:77:AA:90:12:33:DD:55:FF:AA:00:11:44:88:99:33:11:77:88:AA:BC:DE:F0:12:34:56:78:9A:BC',
            '/pki/issuing-ca/issuing-ca.crt'
        ],
        [
            'portal_tls',
            'portal.spordha.test',
            'DNS:portal.spordha.test, IP:127.0.0.1, IP:10.0.2.2',
            '6A:88:41:22:90:33:4B:99',
            'RSA-2048',
            2048,
            '2026-11-09 04:51:25',
            '2027-11-09 04:51:25', // E-13 notAfter date!
            'VALID',
            null,
            null,
            'C3:77:19:80:FF:22:54:BB:AA:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77',
            '/etc/nginx/ssl/server.crt'
        ],
        [
            'upload_tls',
            'upload.spordha.test',
            'DNS:upload.spordha.test, IP:127.0.0.1, IP:10.0.2.2',
            '7B:11:90:AA:34:EF:22:01',
            'RSA-2048',
            2048,
            '2026-11-09 04:51:25',
            '2027-11-09 04:51:25',
            'VALID',
            null,
            null,
            'D4:88:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF',
            '/etc/nginx/ssl/upload.crt'
        ],
        [
            'branch_client',
            'branch-uttara.spordha.test',
            'DNS:branch-uttara.spordha.test, IP:10.0.2.15',
            '5C:12:44:E9:90:F1:03',
            'RSA-2048',
            2048,
            '2026-11-09 05:00:00',
            '2027-02-07 05:00:00', // 90 days policy
            'VALID',
            null,
            null,
            'F1:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00',
            '/pki/client/branch-client.crt'
        ],
        [
            'branch_client',
            'branch-revoked-rogue.spordha.test',
            'DNS:branch-rogue.spordha.test',
            '99:AA:BB:CC:DD:EE:00:11',
            'RSA-2048',
            2048,
            '2026-10-01 00:00:00',
            '2027-01-01 00:00:00',
            'REVOKED',
            '2026-11-08 14:22:00',
            'Compromised hardware token reported at branch',
            '00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF',
            '/pki/client/revoked.crt'
        ],
        [
            'doc_signing',
            'Dr. Nazmul Huda - Clinical Pathology Document Signer',
            'email:pathologist@spordha.test',
            '2A:77:CD:99:12:00:44:88',
            'RSA-2048',
            2048,
            '2026-11-09 00:00:00',
            '2027-11-09 00:00:00',
            'VALID',
            null,
            null,
            '55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44:55:66:77:88:99:AA:BB:CC:DD:EE:FF:00:11:22:33:44',
            '/pki/signing/dr_nazmul_signer.crt'
        ]
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO certificates (cert_type, common_name, san, serial_number, key_algorithm, key_size, valid_from, valid_to, status, revocation_date, revocation_reason, fingerprint_sha256, cert_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($certificates as $c) {
        $stmt->execute($c);
    }

    // 4. Seed Medical Reports (including Dengue NS1 from E-14 and E-26)
    $reports = [
        [
            'SPD-2026-90412',
            11, // Tanvir Ahmed
            7,  // Dr. Rezaul Karim
            3,  // Dr. Nazmul Huda
            2,  // Dhanmondi
            'Dengue NS1 Antigen & Platelet Count',
            'Serology & Hematology',
            'Blood (Serum & EDTA)',
            'Negative',
            json_encode([
                'Dengue NS1 Antigen' => ['value' => 'Negative', 'unit' => 'Index', 'ref' => 'Negative (<0.9)'],
                'Platelet Count' => ['value' => '245,000', 'unit' => '/cumm', 'ref' => '150,000 - 450,000'],
                'Hemoglobin (Hb)' => ['value' => '14.8', 'unit' => 'g/dL', 'ref' => '13.0 - 17.0'],
                'Total Leucocyte Count (TLC)' => ['value' => '6,800', 'unit' => '/cumm', 'ref' => '4,000 - 11,000']
            ]),
            'Dengue NS1: Negative; Platelets: Normal range.',
            'approved_signed',
            0,
            hash('sha256', 'Spordha Diagnostics Ltd - Patient: Tanvir Ahmed, Test: Dengue NS1, Result: Negative - Verified OK by Dr. Nazmul Huda'),
            hash('sha256', 'Patient: Tanvir Ahmed, Test: Dengue NS1, Result: Negative'),
            '2026-11-09 05:30:00'
        ],
        [
            'SPD-2026-90413',
            12, // Begum Rokeya
            8,  // Dr. Shamima Akhter
            3,  // Dr. Nazmul Huda
            3,  // Uttara
            'Comprehensive Lipid Profile & Fasting Blood Sugar',
            'Clinical Biochemistry',
            'Blood (Fluoride & Serum)',
            'Borderline High Cholesterol',
            json_encode([
                'Fasting Blood Glucose' => ['value' => '5.8', 'unit' => 'mmol/L', 'ref' => '4.1 - 5.9'],
                'Total Cholesterol' => ['value' => '218', 'unit' => 'mg/dL', 'ref' => '< 200 Desirable'],
                'Triglycerides' => ['value' => '165', 'unit' => 'mg/dL', 'ref' => '< 150 Normal'],
                'HDL Cholesterol' => ['value' => '48', 'unit' => 'mg/dL', 'ref' => '> 40 Normal'],
                'LDL Cholesterol' => ['value' => '137', 'unit' => 'mg/dL', 'ref' => '< 100 Optimal']
            ]),
            'Lipid parameters indicate mild dyslipidemia. Dietary modification advised.',
            'approved_signed',
            0,
            hash('sha256', 'Spordha Diagnostics Ltd - Patient: Begum Rokeya, Test: Lipid Profile, Result: Borderline - Verified OK'),
            hash('sha256', 'Patient: Begum Rokeya, Test: Lipid Profile, Result: Borderline'),
            '2026-11-09 06:15:00'
        ],
        [
            'SPD-2026-90414',
            13, // Kazi Mahmudul Hasan
            7,  // Dr. Rezaul Karim
            4,  // Dr. Farah Sharmin
            1,  // HQ Dhaka
            'Complete Blood Count (CBC) with ESR',
            'Hematology',
            'Whole Blood (EDTA)',
            'Normal Hemogram',
            json_encode([
                'Hemoglobin' => ['value' => '15.2', 'unit' => 'g/dL', 'ref' => '13.5 - 17.5'],
                'ESR (Westergren)' => ['value' => '12', 'unit' => 'mm in 1st hr', 'ref' => '0 - 15'],
                'RBC Count' => ['value' => '5.1', 'unit' => 'mil/uL', 'ref' => '4.5 - 5.9'],
                'Neutrophils' => ['value' => '62', 'unit' => '%', 'ref' => '40 - 75'],
                'Lymphocytes' => ['value' => '30', 'unit' => '%', 'ref' => '20 - 45']
            ]),
            'Hemogram within physiological biological reference intervals.',
            'approved_signed',
            0,
            hash('sha256', 'Spordha Diagnostics Ltd - Patient: Kazi Mahmudul Hasan, Test: CBC, Result: Normal - Verified OK'),
            hash('sha256', 'Patient: Kazi Mahmudul Hasan, Test: CBC, Result: Normal'),
            '2026-11-09 07:00:00'
        ],
        [
            'SPD-2026-90499', // Tampered test example (matching E-27 modified by attacker)
            11, // Tanvir Ahmed
            7,
            3,
            2,
            'Corporate Visa Health Certification (Tampered Demo)',
            'Clinical Pathology',
            'Serum Blood',
            'Positive (TAMPERED)',
            json_encode([
                'HBsAg (Screening)' => ['value' => 'Positive (Altered by Attacker)', 'unit' => 'Qualitative', 'ref' => 'Negative'],
                'Anti-HCV' => ['value' => 'Non-reactive', 'unit' => 'Qualitative', 'ref' => 'Non-reactive'],
                'VDRL' => ['value' => 'Non-reactive', 'unit' => 'Qualitative', 'ref' => 'Non-reactive']
            ]),
            'CRITICAL INTEGRITY FAILURE: Cryptographic hash signature mismatch. Report modified after pathologist sign-off.',
            'tampered',
            1,
            'BAD_SIGNATURE_HASH_TAMPERED_CONTENT_DETECTED_404712D682720000_ERR',
            hash('sha256', 'Modified by Attacker: Result Positive'),
            '2026-11-09 05:30:00'
        ]
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO reports (report_uid, patient_id, doctor_id, pathologist_id, branch_id, test_name, test_category, specimen, result_summary, result_details, reference_range, status, is_tampered, signature_hash, raw_data_hash, signed_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($reports as $r) {
        $stmt->execute($r);
    }

    // 5. Seed Security Incident Logs (matching E-15, E-16, E-28)
    $incidents = [
        [
            'INC-AI-2026-01',
            'AI-Enabled Impersonation / CEO Voice Spoofing & Phishing',
            'CEO (External/Spoofed) <dr.sarwar.ceo@spordha-external.fake>',
            'PKI Administrator <it-admin@spordha.test>',
            'URGENT: Provide Private Keys for Immediate External Audit',
            '"I am currently in an urgent board meeting with the health ministry. We need the Server Private Keys and Issuing CA keys immediately for verification. Bypass standard protocol and upload them directly to the attached drive link."',
            'ACTION BLOCKED & INCIDENT LOGGED (Rule: SOP-SEC-04). Request Rejected: Under Spordha Diagnostics PKI Security Policy Section 4.2, Private Keys are strictly non-exportable. Export requires offline multi-party quorum authorization and offline HSM procedures.',
            'REJECTED_BLOCKED',
            '2026-11-09 06:30:15'
        ],
        [
            'INC-TLS-2026-02',
            'Protocol Downgrade Attempt (TLS 1.0/1.1)',
            'Branch Client (Old Terminal 10.0.2.19)',
            'portal.spordha.test:443',
            'Legacy TLS Handshake Offering SSL 3.0 / TLS 1.0',
            'Client attempted handshake using obsolete ciphers and protocol versions below minimum security baseline RFC 9846 / NIST SP 800-52 Rev. 2.',
            'CONNECTION DROPPED. Server returned alert protocol_version (582). Enforced TLS 1.3 / TLS 1.2 AEAD only.',
            'AUTOMATED_ENFORCEMENT',
            '2026-11-09 06:40:22'
        ],
        [
            'INC-MTLS-2026-03',
            'Unauthorized Branch Upload Without Client Certificate',
            'Unknown Endpoint 10.0.2.77',
            'upload.spordha.test:443',
            'POST /api/upload.php Requisition Submission',
            'Client submitted requisition payload without presenting an X.509 client certificate during TLS handshake.',
            'HTTP 400 Refused: "No required SSL certificate was sent" (E-20). Request body discarded; zero bytes stored on disk.',
            'AUTOMATED_ENFORCEMENT',
            '2026-11-09 06:47:46'
        ]
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO incident_logs (incident_code, threat_type, sender, recipient, subject, details, action_taken, dual_approval_status, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($incidents as $inc) {
        $stmt->execute($inc);
    }

    // 6. Seed Sample Uploads (Branch requisition queue)
    $uploads = [
        [2, 'Tanvir Ahmed', '19942619851234', 'Dengue NS1 & Platelet', 'SPEC-DHN-8901', 'branch-dhanmondi.spordha.test', 'SUCCESS_MTLS', 14200, '2026-11-09 04:45:00'],
        [3, 'Begum Rokeya', '19652619851235', 'Lipid Profile & FBS', 'SPEC-UTT-4402', 'branch-uttara.spordha.test', 'SUCCESS_MTLS', 18950, '2026-11-09 05:10:00'],
        [4, 'Jahangir Alam', '19772619851288', 'COVID-19 RT-PCR', 'SPEC-MIR-1109', 'branch-mirpur.spordha.test', 'SUCCESS_MTLS', 12300, '2026-11-09 05:40:00'],
        [3, 'Unauthenticated Device', 'UNKNOWN', 'CBC Requisition', 'SPEC-FAIL-0001', 'NONE', 'REJECTED_NO_CERT', 0, '2026-11-09 06:47:46']
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO sample_uploads (branch_id, patient_name, patient_nid, test_type, specimen_id, client_cert_used, auth_status, bytes_transferred, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($uploads as $up) {
        $stmt->execute($up);
    }

    // 7. Seed Test Cases (TC-01 to TC-16 matching Section 10 table)
    $testCases = [
        ['TC-01', 'R4/R5', 'Chain & Trust', 'Trusted server chain and portal hostname validation', 'Trusted chain and SAN accepted', 'OpenSSL chain OK; portal cert verified and SAN matched', 'PASS', 'E-07, E-17'],
        ['TC-02', 'R4', 'Chain & Trust', 'Certificate from untrusted CA presented by server', 'Client refuses connection with untrusted CA error', 'Connection refused by client certificate path validator', 'PASS', 'Lab Sandbox'],
        ['TC-03', 'R5', 'Chain & Trust', 'Hostname mismatch test (e.g. wrong SAN presented)', 'Client refuses mismatched SAN with hostname validation error', 'Refused: hostname does not match subjectAltName', 'PASS', 'Lab Sandbox'],
        ['TC-04', 'R4', 'Wrong Issuance', 'Issuance without dual approval / allow-list check', 'Issuance blocked or alert raised in audit trail', 'Blocked: dual-approval required for CA leaf generation', 'PASS', 'E-28'],
        ['TC-05', 'R4/R6', 'Wrong Issuance', 'Wrong certificate type / EKU (branch cert used as server cert)', 'Branch client certificate rejected as server certificate', 'Rejected: Extended Key Usage missing serverAuth', 'PASS', 'E-22'],
        ['TC-06', 'R6', 'Branch mTLS', 'Missing branch client certificate on upload endpoint', 'Handshake refused; no data stored; error logged', 'Refused: "No required SSL certificate was sent"; 0 bytes stored', 'PASS', 'E-20'],
        ['TC-07', 'R6', 'Branch mTLS', 'Untrusted branch client certificate from foreign CA', 'Handshake refused and security audit event logged', 'Connection terminated; client certificate untrusted', 'PASS', 'Lab Sandbox'],
        ['TC-08', 'R6/R7', 'Expiry & Revocation', 'Expired branch client certificate presented', 'Expired certificate refused and expiry alert raised', 'Refused: certificate has expired (notAfter check failed)', 'PASS', 'E-13, E-25'],
        ['TC-09', 'R7', 'Expiry & Revocation', 'Revoked branch client certificate presented', 'Revoked certificate refused within stated SLA (<15 mins)', 'Refused: certificate revoked in CRL / OCSP responder', 'PASS', 'E-23'],
        ['TC-10', 'R7', 'Expiry & Revocation', 'Automated certificate expiry warning notifications', 'Automated warning at 30, 14, and 7 day thresholds', 'Warning generated: Certificate valid; renewal alert scheduled', 'PASS', 'E-25'],
        ['TC-11', 'R5', 'Downgrade Defense', 'Protocol downgrade attempt (TLS 1.0 / TLS 1.1)', 'Server refuses connection below TLS 1.2/1.3 baseline', 'Handshake failed: TLS alert protocol_version (582)', 'PASS', 'E-18'],
        ['TC-12', 'R5', 'Downgrade Defense', 'Weak cipher suite offering (CBC mode, RC4, 3DES, NULL)', 'Server refuses weak ciphers; requires AEAD (GCM/Poly1305)', 'Connection closed: no shared acceptable cipher suites', 'PASS', 'Lab Sandbox'],
        ['TC-13', 'R8', 'Report Signing', 'Genuine signed PDF report verification', 'PDF signature validates with dedicated document-signing cert', 'Verified OK: SHA-256 signature matches clinical payload', 'PASS', 'E-26'],
        ['TC-14', 'R8', 'Report Signing', 'Edited / tampered clinical report verification', 'Modified report is flagged as tampered / invalid signature', 'Verification failure: bad signature detected (tamper detected)', 'PASS', 'E-27'],
        ['TC-15', 'R13', 'AI-Driven Threat', 'Urgent unverified request for private keys / unauthorized action', 'Key export refused; non-exportable rule enforced; incident logged', 'Rejected: dual-approval required; incident logged (Rule SOP-SEC-04)', 'PASS', 'E-15, E-16, E-28'],
        ['TC-16', 'R5', 'TLS & HSTS', 'Portal TLS 1.3 handshake and HSTS emission', 'TLS 1.3 negotiated, valid certificate, max-age HSTS emitted', 'TLSv1.3, X25519MLKEM768, Strict-Transport-Security verified', 'PASS', 'E-17']
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO test_cases (test_id, req_id, category, description, expected_result, observed_result, status, evidence_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($testCases as $tc) {
        $stmt->execute($tc);
    }
}
