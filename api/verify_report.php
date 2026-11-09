<?php
/**
 * Spordha Diagnostics Ltd. - Live Cryptographic Verification API
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$uid = trim($_GET['uid'] ?? '');
if (empty($uid)) {
    echo json_encode(['success' => false, 'message' => 'Report UID required.']);
    exit;
}

$db = getDBConnection();
$stmt = $db->prepare("SELECT r.*, u.name as patient_name, patho.name as pathologist_name 
    FROM reports r 
    JOIN users u ON r.patient_id = u.id 
    LEFT JOIN users patho ON r.pathologist_id = patho.id 
    WHERE r.report_uid = ? LIMIT 1");
$stmt->execute([$uid]);
$report = $stmt->fetch();

if (!$report) {
    echo json_encode(['success' => false, 'message' => 'Report UID not found in database.']);
    exit;
}

$result = verifyReportIntegrity($report);

echo json_encode([
    'success' => true,
    'uid' => $report['report_uid'],
    'is_valid' => $result[0],
    'status_message' => $result[1],
    'is_tampered' => (bool)$report['is_tampered'],
    'signature_hash' => $report['signature_hash'],
    'computed_hash' => $result[2],
    'stored_hash' => $result[3],
    'patient_name' => $report['patient_name'],
    'test_name' => $report['test_name'],
    'pathologist' => $report['pathologist_name'] ?: 'Dr. Nazmul Huda',
    'signed_at' => $report['signed_at']
]);
