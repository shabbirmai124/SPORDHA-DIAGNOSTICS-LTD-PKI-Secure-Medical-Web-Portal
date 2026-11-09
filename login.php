<?php
/**
 * Spordha Diagnostics Ltd. - Secure Authentication Portal
 * High-Security Login with Role-Based Access Control
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';
$notice = $_GET['msg'] ?? '';

// Handle One-Click Quick Role Sign-In if requested
if (isset($_GET['quick_role'])) {
    $role = $_GET['quick_role'];
    $roleEmailMap = [
        'patient'      => 'patient@spordha.test',
        'doctor'       => 'doctor.karim@spordha.test',
        'pathologist'  => 'pathologist@spordha.test',
        'technician'   => 'tech@spordha.test',
        'branch_staff' => 'branch.uttara@spordha.test',
        'pki_admin'    => 'it-admin@spordha.test'
    ];
    $rolePassMap = [
        'patient'      => 'Patient@123',
        'doctor'       => 'Doctor@123',
        'pathologist'  => 'Patho@123',
        'technician'   => 'Tech@123',
        'branch_staff' => 'Branch@123',
        'pki_admin'    => 'Admin@123'
    ];

    if (isset($roleEmailMap[$role])) {
        [$success, $msg, $u] = authenticateUser($roleEmailMap[$role], $rolePassMap[$role]);
        if ($success) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = $msg;
        }
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $csrf       = $_POST['csrf_token'] ?? '';

    if (!verifyCsrfToken($csrf)) {
        $error = "CSRF Token Validation Failed: Potential cross-site request forgery intercepted.";
    } elseif (empty($identifier) || empty($password)) {
        $error = "Please enter both identity credential and password.";
    } else {
        [$success, $msg, $u] = authenticateUser($identifier, $password);
        if ($success) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = $msg;
        }
    }
}

$pageTitle = "Secure Sign In";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 4rem 0; min-height: 80vh; display: flex; align-items: center;">
  <div class="container container-narrow">
    
    <div style="background-color: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-xl); padding: 2.5rem; box-shadow: var(--shadow-xl); max-width: 540px; margin: 0 auto;">
      
      <div style="text-align: center; margin-bottom: 2rem;">
        <div class="brand-icon-box" style="margin: 0 auto 1rem; width: 48px; height: 48px;">S</div>
        <h2 style="font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px;">Sign In to Spordha Identity</h2>
        <p style="font-size: 0.85rem; color: var(--color-gray-mid); margin-top: 0.25rem;">
          Enterprise PKI-Backed Medical Laboratory Authentication
        </p>
      </div>

      <?php if (!empty($notice)): ?>
        <div style="background-color: var(--color-gray-bg); border-left: 4px solid var(--color-black); padding: 0.85rem 1rem; font-size: 0.85rem; margin-bottom: 1.5rem; border-radius: 4px;">
          <?php echo htmlspecialchars($notice); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 0.85rem 1rem; font-size: 0.85rem; margin-bottom: 1.5rem; border-radius: 4px;">
          <strong>Authentication Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.4rem;">
            Email, Phone, or Patient ID:
          </label>
          <input type="text" name="identifier" class="form-control" placeholder="e.g. patient@spordha.test or P-10024" required value="<?php echo htmlspecialchars($_POST['identifier'] ?? 'patient@spordha.test'); ?>">
        </div>

        <div style="margin-bottom: 1.5rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label style="font-size: 0.8125rem; font-weight: 600;">Account Password:</label>
            <span style="font-size: 0.75rem; color: var(--color-gray-mid);">Enforced 12-char policy</span>
          </div>
          <input type="password" name="password" class="form-control" placeholder="••••••••••••" required value="Patient@123">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 0.95rem;">
          Sign In with Authenticated Credentials
        </button>

        <div style="text-align: center; margin-top: 1rem; font-size: 0.85rem; color: var(--color-gray-mid);">
          New patient? <a href="register.php" style="color: #000; font-weight: 700;">Create a secure account</a>
        </div>
      </form>

      <!-- One-Click Instant Role Switcher -->
      <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--color-gray-border);">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-black); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
          Quick-Switch Demo Accounts (1-Click Login):
        </div>
        <p style="font-size: 0.75rem; color: var(--color-gray-mid); margin-bottom: 0.75rem;">
          Click any persona below to authenticate with pre-configured cryptographic roles:
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
          <a href="login.php?quick_role=patient" class="btn btn-secondary btn-sm" style="text-align: left; justify-content: flex-start;">
            <span><strong>Patient</strong> (Tanvir)</span>
          </a>
          <a href="login.php?quick_role=doctor" class="btn btn-secondary btn-sm" style="text-align: left; justify-content: flex-start;">
            <span><strong>Doctor</strong> (Dr. Karim)</span>
          </a>
          <a href="login.php?quick_role=pathologist" class="btn btn-secondary btn-sm" style="text-align: left; justify-content: flex-start;">
            <span><strong>Pathologist</strong> (Dr. Huda)</span>
          </a>
          <a href="login.php?quick_role=technician" class="btn btn-secondary btn-sm" style="text-align: left; justify-content: flex-start;">
            <span><strong>Technician</strong> (Sabbir)</span>
          </a>
          <a href="login.php?quick_role=branch_staff" class="btn btn-secondary btn-sm" style="text-align: left; justify-content: flex-start;">
            <span><strong>Branch Staff</strong> (Uttara)</span>
          </a>
          <a href="login.php?quick_role=pki_admin" class="btn btn-primary btn-sm" style="text-align: left; justify-content: flex-start;">
            <span><strong>PKI Administrator</strong></span>
          </a>
        </div>
      </div>

      <!-- Security Notice -->
      <div style="margin-top: 1.5rem; font-size: 0.75rem; color: var(--color-gray-mid); text-align: center; line-height: 1.5;">
        Session cookies protected by <code>SameSite=Strict</code>, <code>HttpOnly</code>, and TLS 1.3 encryption.
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
