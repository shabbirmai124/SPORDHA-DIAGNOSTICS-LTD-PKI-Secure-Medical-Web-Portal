<?php
/**
 * Spordha Diagnostics Ltd. - Session Logout Handler
 */

require_once __DIR__ . '/includes/auth.php';
logoutUser();
header("Location: login.php?msg=" . urlencode("You have been signed out securely. Session cryptographic tokens invalidated."));
exit;
