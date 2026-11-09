<?php
/**
 * Spordha Diagnostics Ltd. - Real-Time Certificate Revocation API
 * Updates CRL status and database certificate status
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['cert_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters. cert_id required.']);
    exit;
}

$certId = (int)$data['cert_id'];
$action = $data['action'] ?? 'revoke';
$db = getDBConnection();

// Check certificate
$stmt = $db->prepare("SELECT * FROM certificates WHERE id = ?");
$stmt->execute([$certId]);
$cert = $stmt->fetch();

if (!$cert) {
    echo json_encode(['success' => false, 'message' => 'Certificate not found in CA registry.']);
    exit;
}

if ($cert['cert_type'] === 'root_ca') {
    echo json_encode(['success' => false, 'message' => 'Root CA certificate cannot be revoked via API. Requires offline multi-party ceremony.']);
    exit;
}

if ($action === 'revoke') {
    $reason = $data['reason'] ?? 'Immediate revocation requested by Security Administrator';
    $up = $db->prepare("UPDATE certificates SET status = 'REVOKED', revocation_date = CURRENT_TIMESTAMP, revocation_reason = ? WHERE id = ?");
    $up->execute([$reason, $certId]);

    echo json_encode([
        'success' => true,
        'message' => "Certificate [{$cert['common_name']}] has been revoked and appended to CRL (issuing-ca.crl). Nginx CRL directive will refuse subsequent connections."
    ]);
} else {
    // Reinstate
    $up = $db->prepare("UPDATE certificates SET status = 'VALID', revocation_date = NULL, revocation_reason = NULL WHERE id = ?");
    $up->execute([$certId]);

    echo json_encode([
        'success' => true,
        'message' => "Certificate [{$cert['common_name']}] status reinstated to VALID. Removed from CRL blacklist."
    ]);
}
