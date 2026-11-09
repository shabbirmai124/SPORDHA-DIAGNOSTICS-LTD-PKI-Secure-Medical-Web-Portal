<?php
/**
 * Spordha Diagnostics Ltd. - User Registration Page
 * New Patient / Staff Account Registration
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $error = "CSRF Token Validation Failed.";
    } else {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $nid      = trim($_POST['nid'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $role     = 'patient'; // Default: public registration is patient only

        if (empty($name) || empty($email) || empty($password)) {
            $error = "Name, email, and password are required fields.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match. Please re-enter.";
        } else {
            $db = getDBConnection();
            // Check duplicate email
            $check = $db->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = "An account with this email already exists. Please sign in instead.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("INSERT INTO users (name, email, password_hash, role, phone, nid, designation, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
                $designation = 'Patient (ID: P-' . rand(10000, 99999) . ')';
                if ($stmt->execute([$name, $email, $hash, $role, $phone, $nid, $designation])) {
                    $success = "Account created successfully! You can now sign in with your credentials.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}

$pageTitle = "Create New Account";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 4rem 0; min-height: 80vh; display: flex; align-items: center;">
  <div class="container container-narrow">
    <div style="background-color: var(--color-white); border: 1px solid var(--color-gray-border); border-radius: var(--radius-xl); padding: 2.5rem; box-shadow: var(--shadow-xl); max-width: 580px; margin: 0 auto;">

      <div style="text-align: center; margin-bottom: 2rem;">
        <div class="brand-icon-box" style="margin: 0 auto 1rem; width: 48px; height: 48px;">S</div>
        <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px;">Create Patient Account</h2>
        <p style="font-size: 0.85rem; color: var(--color-gray-mid); margin-top: 0.25rem;">
          Register to access your medical test results securely
        </p>
      </div>

      <?php if ($success): ?>
        <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; color: #166534; padding: 0.85rem 1rem; font-size: 0.85rem; margin-bottom: 1.5rem; border-radius: 4px;">
          <?php echo htmlspecialchars($success); ?> <a href="login.php" style="font-weight: 700; color: #166534;">Sign In Now &rarr;</a>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 0.85rem 1rem; font-size: 0.85rem; margin-bottom: 1.5rem; border-radius: 4px;">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form action="register.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">

        <div style="margin-bottom: 1.15rem;">
          <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Full Name <span style="color: #b91c1c;">*</span></label>
          <input type="text" name="name" class="form-control" placeholder="e.g. Md. Tanvir Ahmed" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.15rem;">
          <div>
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Email Address <span style="color: #b91c1c;">*</span></label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
          </div>
          <div>
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Phone Number</label>
            <input type="text" name="phone" class="form-control" placeholder="+880 17XX XXX XXX" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
          </div>
        </div>

        <div style="margin-bottom: 1.15rem;">
          <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">National ID (NID) Number</label>
          <input type="text" name="nid" class="form-control" placeholder="e.g. 19941234567890" value="<?php echo htmlspecialchars($_POST['nid'] ?? ''); ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
          <div>
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Password <span style="color: #b91c1c;">*</span></label>
            <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
          </div>
          <div>
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Confirm Password <span style="color: #b91c1c;">*</span></label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 0.95rem;">
          Create My Account
        </button>
      </form>

      <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: var(--color-gray-mid);">
        Already have an account? <a href="login.php" style="color: #000; font-weight: 700;">Sign In</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
