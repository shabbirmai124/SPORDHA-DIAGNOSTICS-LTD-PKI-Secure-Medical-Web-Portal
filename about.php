<?php
/**
 * Spordha Diagnostics Ltd. - About Us Page
 * Organization, Lab Credentials, Academic Project Context
 */

$pageTitle = "About Spordha Diagnostics Ltd.";
require_once __DIR__ . '/includes/header.php';
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <span class="section-badge">About Our Laboratory</span>
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">
      About Spordha Diagnostics Ltd.
    </h1>
    <p style="color: var(--color-gray-dark); margin-top: 0.25rem; max-width: 800px;">
      A premier clinical diagnostic laboratory in Dhaka delivering accurate, confidential, and cryptographically verifiable health results for patients, physicians, and corporate clients since 2019.
    </p>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">

  <!-- Mission, Vision, Values -->
  <div class="cards-grid-3" style="margin-bottom: 3.5rem;">
    <div class="card-item" style="border-top: 4px solid #000;">
      <div class="card-icon">🎯</div>
      <h3>Our Mission</h3>
      <p>To provide affordable, accurate, and timely pathology diagnostics to the people of Bangladesh, protected by world-class digital security infrastructure that ensures every report is genuine and confidential.</p>
    </div>
    <div class="card-item" style="border-top: 4px solid #000;">
      <div class="card-icon">👁️</div>
      <h3>Our Vision</h3>
      <p>To become the most trusted diagnostic laboratory in Bangladesh — one where patients, doctors, employers, and government agencies can verify the authenticity of any result at any time through cryptographic proof.</p>
    </div>
    <div class="card-item" style="border-top: 4px solid #000;">
      <div class="card-icon">⚖️</div>
      <h3>Our Values</h3>
      <p>Clinical accuracy, patient confidentiality (PDPA 2026 compliant), digital integrity (SHA-256 signed reports), open and transparent PKI security, and ethical laboratory governance under ACM Professional Code.</p>
    </div>
  </div>

  <!-- Key Statistics -->
  <div style="background-color: var(--color-black); color: #fff; padding: 3rem; border-radius: var(--radius-xl); margin-bottom: 3.5rem;">
    <h2 style="text-align: center; font-size: 1.75rem; font-weight: 800; margin-bottom: 2.5rem; color: #fff;">
      Spordha Diagnostics at a Glance
    </h2>
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 2rem; text-align: center;">
      <div>
        <div style="font-size: 2.5rem; font-weight: 800; color: #fff;">40,000+</div>
        <div style="font-size: 0.85rem; color: #a1a1aa;">Registered Patients</div>
      </div>
      <div>
        <div style="font-size: 2.5rem; font-weight: 800; color: #fff;">1,200</div>
        <div style="font-size: 0.85rem; color: #a1a1aa;">Daily Specimens</div>
      </div>
      <div>
        <div style="font-size: 2.5rem; font-weight: 800; color: #fff;">300+</div>
        <div style="font-size: 0.85rem; color: #a1a1aa;">Referring Physicians</div>
      </div>
      <div>
        <div style="font-size: 2.5rem; font-weight: 800; color: #fff;">4</div>
        <div style="font-size: 0.85rem; color: #a1a1aa;">Laboratory Locations</div>
      </div>
      <div>
        <div style="font-size: 2.5rem; font-weight: 800; color: #fff;">100%</div>
        <div style="font-size: 0.85rem; color: #a1a1aa;">Reports Digitally Signed</div>
      </div>
    </div>
  </div>

  <!-- Lab Team / Departments -->
  <div class="section-head">
    <span class="section-badge">Our Team</span>
    <h2 class="section-title">Clinical & Technical Leadership</h2>
    <p class="section-desc">Highly qualified specialists operating under ISO 15189 quality management standards.</p>
  </div>

  <div class="cards-grid-3" style="margin-bottom: 3.5rem;">
    <div class="card-item" style="text-align: center;">
      <div class="profile-avatar" style="margin: 0 auto 1rem; width: 64px; height: 64px; font-size: 1.75rem;">N</div>
      <h3 style="font-size: 1.1rem;">Dr. Nazmul Huda</h3>
      <p style="font-size: 0.8rem; color: var(--color-gray-mid); margin-bottom: 0.5rem;">Head of Clinical Pathology (MD)</p>
      <span class="role-badge role-pathologist">Lead Pathologist & Document Signer</span>
    </div>
    <div class="card-item" style="text-align: center;">
      <div class="profile-avatar" style="margin: 0 auto 1rem; width: 64px; height: 64px; font-size: 1.75rem;">S</div>
      <h3 style="font-size: 1.1rem;">Md. Shabbir Mia</h3>
      <p style="font-size: 0.8rem; color: var(--color-gray-mid); margin-bottom: 0.5rem;">Senior PKI Administrator</p>
      <span class="role-badge role-pki_admin">IT Security & PKI Operations</span>
    </div>
    <div class="card-item" style="text-align: center;">
      <div class="profile-avatar" style="margin: 0 auto 1rem; width: 64px; height: 64px; font-size: 1.75rem;">F</div>
      <h3 style="font-size: 1.1rem;">Dr. Farah Sharmin</h3>
      <p style="font-size: 0.8rem; color: var(--color-gray-mid); margin-bottom: 0.5rem;">Consultant Hematologist (FCPS)</p>
      <span class="role-badge role-pathologist">Hematology & Blood Sciences</span>
    </div>
  </div>

  <!-- Academic Context -->
  <div style="background-color: var(--color-gray-subtle); border: 1px solid var(--color-gray-border); border-radius: var(--radius-xl); padding: 2.5rem;">
    <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1rem;">Academic Project Context</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; font-size: 0.9rem; color: var(--color-charcoal); line-height: 1.7;">
      <div>
        <p><strong>Institution:</strong> East West University, Dhaka</p>
        <p><strong>Department:</strong> Computer Science and Engineering</p>
        <p><strong>Course:</strong> CSE487 — Cyber Security, Ethics and Law</p>
        <p><strong>Semester:</strong> Fall 2026</p>
        <p><strong>Project Track:</strong> Track 2 — PKI and Secure Communication</p>
      </div>
      <div>
        <p><strong>Assessment:</strong> 20 marks of course total (Report 5, Presentation 5, Demo 3, Viva 7)</p>
        <p><strong>Group Size:</strong> 3–5 students</p>
        <p><strong>Scenario:</strong> Fictional diagnostic laboratory based on Dhrubotara Diagnostics, adapted as "Spordha Diagnostics Ltd."</p>
        <p><strong>Namespace:</strong> <code>.spordha.test</code> (IANA-reserved TLD)</p>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
