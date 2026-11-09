<?php
/**
 * Spordha Diagnostics Ltd. - Dual-Approval & Security Incident API
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['threat_type'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid incident payload.']);
    exit;
}

$success = logSecurityIncident(
    $data['threat_type'],
    $data['sender'] ?? 'Unknown Caller / Email',
    $data['recipient'] ?? 'PKI Administrator',
    $data['subject'] ?? 'Urgent Certificate Operation Request',
    $data['details'] ?? 'Attempted out-of-band request without dual-quorum verification.',
    $data['action_taken'] ?? 'BLOCKED: Dual approval required under SOP-SEC-04.',
    $data['status'] ?? 'REJECTED_BLOCKED'
);

echo json_encode([
    'success' => $success,
    'message' => 'Incident successfully logged into immutable audit database.'
]);
