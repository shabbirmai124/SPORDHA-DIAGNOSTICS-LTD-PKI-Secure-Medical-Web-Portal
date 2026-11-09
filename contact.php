<?php
/**
 * Spordha Diagnostics Ltd. - Contact Us Page
 * Laboratory Contact Info & Inquiry Form
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDBConnection();
$branches = $db->query("SELECT * FROM branches ORDER BY id ASC")->fetchAll();
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real system we'd email or store the inquiry
    $submitted = true;
}

$pageTitle = "Contact Us";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <span class="section-badge">Get in Touch</span>
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">Contact Us</h1>
    <p style="color: var(--color-gray-dark); margin-top: 0.25rem;">
      Reach out for appointments, sample collection queries, report inquiries, or partnership proposals.
    </p>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <div class="cards-grid-2" style="gap: 3rem;">
    <!-- Contact Form -->
    <div>
      <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.25rem;">Send Us a Message</h3>

      <?php if ($submitted): ?>
        <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; color: #166534; padding: 1.25rem; border-radius: 6px; font-size: 0.9rem;">
          <strong>Thank you!</strong> Your message has been received. Our customer service team will respond within 24 hours during business days.
        </div>
      <?php else: ?>
        <form action="contact.php" method="POST">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.15rem;">
            <div>
              <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Your Name <span style="color: #b91c1c;">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Full name" required>
            </div>
            <div>
              <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Phone Number <span style="color: #b91c1c;">*</span></label>
              <input type="text" name="phone" class="form-control" placeholder="+880 17XX XXX XXX" required>
            </div>
          </div>

          <div style="margin-bottom: 1.15rem;">
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com">
          </div>

          <div style="margin-bottom: 1.15rem;">
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Subject <span style="color: #b91c1c;">*</span></label>
            <select name="subject" class="form-control" required>
              <option value="">Select a topic...</option>
              <option value="Report Inquiry">Report / Test Result Inquiry</option>
              <option value="Home Collection">Home Sample Collection Request</option>
              <option value="Appointment">Appointment Scheduling</option>
              <option value="Corporate Health">Corporate Health Check Package</option>
              <option value="Feedback">Service Feedback</option>
              <option value="Other">Other / General Inquiry</option>
            </select>
          </div>

          <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Your Message <span style="color: #b91c1c;">*</span></label>
            <textarea name="message" class="form-control" rows="5" placeholder="Describe your inquiry..." required></textarea>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 0.95rem;">
            Send Message
          </button>
        </form>
      <?php endif; ?>
    </div>

    <!-- Contact Details -->
    <div>
      <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1.25rem;">Our Laboratory Locations</h3>

      <?php foreach ($branches as $b): ?>
        <div class="card-item" style="margin-bottom: 1.25rem; border-left: 4px solid #000;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <h4 style="font-size: 1rem; font-weight: 800;"><?php echo htmlspecialchars($b['name']); ?></h4>
            <span class="badge badge-warning-notice"><?php echo htmlspecialchars($b['code']); ?></span>
          </div>
          <div style="font-size: 0.85rem; color: var(--color-gray-dark); line-height: 1.6;">
            <div><strong>Address:</strong> <?php echo htmlspecialchars($b['location']); ?></div>
            <div><strong>Phone:</strong> <?php echo htmlspecialchars($b['phone']); ?></div>
            <div><strong>Hours:</strong> Saturday – Thursday: 7:30 AM – 10:30 PM | Friday: 9:00 AM – 8:00 PM</div>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- Emergency Info Box -->
      <div style="background-color: var(--color-black); color: #fff; padding: 1.5rem; border-radius: var(--radius-lg); margin-top: 1.5rem;">
        <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 0.5rem; color: #fff;">
          24/7 Emergency Rapid Diagnostics
        </h4>
        <p style="font-size: 0.85rem; color: #a1a1aa; line-height: 1.6;">
          For urgent Dengue NS1, Cardiac Troponin, or Blood Sugar emergencies, call our 24-hour hotline:
        </p>
        <div style="font-size: 1.5rem; font-weight: 800; margin-top: 0.75rem; color: #fff;">
          +880 2 9876543
        </div>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
