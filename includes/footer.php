  </main>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <!-- Brand & Academic Overview -->
        <div class="footer-col">
          <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem;">
            <div class="brand-icon-box" style="width: 32px; height: 32px; font-size: 1rem;">S</div>
            <h4 style="margin: 0; color: #fff;">Spordha Diagnostics Ltd.</h4>
          </div>
          <p style="font-size: 0.85rem; line-height: 1.6; margin-bottom: 1rem; color: #a1a1aa;">
            A premier clinical diagnostic laboratory serving Dhaka with 1,200 daily specimens, 300 referring physicians, and 40,000+ patients. Protected by an enterprise two-tier PKI, mutual TLS 1.3, and cryptographic report signing.
          </p>
          <div style="font-size: 0.75rem; color: #71717a; line-height: 1.5;">
            <strong>East West University</strong><br>
            Department of Computer Science &amp; Engineering<br>
            CSE487: Cyber Security, Ethics and Law — Fall 2026<br>
            Mini Project Track 2: PKI and Secure Communication<br>
            <span style="color: #71717a;">Project lead &amp; PKI Administrator: Md Shabbir Mia</span>
          </div>
        </div>

        <!-- Portals & Services -->
        <div class="footer-col">
          <h4>Secure Portals</h4>
          <ul class="footer-links">
            <li><a href="patient_portal.php">Patient Result Portal</a></li>
            <li><a href="doctor_portal.php">Referring Doctor Portal</a></li>
            <li><a href="pathologist_studio.php">Pathologist Approval Studio</a></li>
            <li><a href="branch_upload.php">Branch mTLS Upload API</a></li>
            <li><a href="pki_soc.php">PKI Operations Center (SOC)</a></li>
            <li><a href="verify.php">Live Report Tamper Verification</a></li>
            <li><a href="about.php">About Spordha</a></li>
            <li><a href="contact.php">Contact & Support</a></li>
          </ul>
        </div>

        <!-- Statutory & Standards Compliance -->
        <div class="footer-col">
          <h4>Legal & Standards</h4>
          <ul class="footer-links">
            <li><a href="documentation.php#legal">Cyber Security Act 2026 (Act 81)</a></li>
            <li><a href="documentation.php#legal">Personal Data Protection Act 2026 (Act 63)</a></li>
            <li><a href="documentation.php#legal">ICT Act 2006 (Electronic Signatures)</a></li>
            <li><a href="documentation.php#standards">IETF RFC 9846 / RFC 5280 PKI</a></li>
            <li><a href="documentation.php#standards">NIST SP 800-52 & SP 800-57</a></li>
            <li><a href="documentation.php#ethics">ACM Code of Ethics (1.2, 1.6, 1.7)</a></li>
          </ul>
        </div>

        <!-- Diagnostic Branches -->
        <div class="footer-col">
          <h4>Lab Locations</h4>
          <ul class="footer-links">
            <li>
              <strong style="color: #fff; font-size: 0.8rem;">Central Laboratory (HQ)</strong><br>
              <span style="font-size: 0.75rem;">124 Bir Uttam C.R. Datta Rd, Dhaka</span>
            </li>
            <li style="margin-top: 0.5rem;">
              <strong style="color: #fff; font-size: 0.8rem;">Dhanmondi Branch</strong><br>
              <span style="font-size: 0.75rem;">Road 27, House 45, Dhanmondi R/A</span>
            </li>
            <li style="margin-top: 0.5rem;">
              <strong style="color: #fff; font-size: 0.8rem;">Uttara Sample Collection</strong><br>
              <span style="font-size: 0.75rem;">Sector 7, Rabindra Sarani, Uttara</span>
            </li>
            <li style="margin-top: 0.5rem;">
              <strong style="color: #fff; font-size: 0.8rem;">Mirpur Diagnostic Branch</strong><br>
              <span style="font-size: 0.75rem;">Mirpur-10 Circle, Section-6, Dhaka</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="footer-legal-bar">
        <div>
          &copy; <?php echo date('Y'); ?> Spordha Diagnostics Ltd. All rights reserved. Academic implementation under reserved namespace <code>.spordha.test</code>.
        </div>
        <div style="display: flex; gap: 1rem;">
          <a href="documentation.php" style="color: inherit; text-decoration: none;">Security Architecture</a>
          <span>•</span>
          <a href="evidence_gallery.php" style="color: inherit; text-decoration: none;">Evidence Gallery (E-01 – E-28)</a>
          <span>•</span>
          <a href="verify.php" style="color: inherit; text-decoration: none;">Report Integrity Verifier</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Back to top -->
  <button class="back-to-top" aria-label="Back to top">↑</button>

  <!-- Scripts -->
  <script src="assets/js/main.js"></script>
  <?php if (isset($loadVerifyScript) && $loadVerifyScript): ?>
    <script src="assets/js/verify.js"></script>
  <?php endif; ?>
</body>
</html>
