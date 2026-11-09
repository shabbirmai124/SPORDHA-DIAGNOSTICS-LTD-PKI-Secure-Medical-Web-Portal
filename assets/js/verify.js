/**
 * Spordha Diagnostics Ltd. - Live Cryptographic Verification & Tamper Detection Lab
 * Real-time SHA-256 and PAdES Verification Simulator (Evidence E-26 vs E-27)
 */

async function sha256(message) {
  const msgBuffer = new TextEncoder().encode(message);
  const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

document.addEventListener('DOMContentLoaded', function () {
  const payloadInput = document.getElementById('interactivePayloadInput');
  const computedHashEl = document.getElementById('liveComputedHash');
  const verificationResultBox = document.getElementById('interactiveVerifyResult');
  const tamperBtn = document.getElementById('simulateTamperBtn');
  const resetBtn = document.getElementById('resetPayloadBtn');

  // Genuine baseline payload from E-14 and E-26
  const GENUINE_BASELINE = "Spordha Diagnostics Ltd - Patient: Test Patient, Test: Dengue NS1, Result: Negative";
  let expectedHash = "";

  async function updateVerification() {
    if (!payloadInput || !computedHashEl) return;
    const currentText = payloadInput.value;
    const currentHash = await sha256(currentText);
    computedHashEl.innerText = currentHash;

    if (currentText.trim() === GENUINE_BASELINE.trim()) {
      verificationResultBox.className = 'tamper-box genuine';
      verificationResultBox.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #166534; font-weight: 800; font-size: 1.05rem;">
          <span>✓</span> VERIFIED OK (Evidence E-26)
        </div>
        <p style="font-size: 0.85rem; color: #15803d; margin-top: 0.5rem;">
          Cryptographic signature valid. The document matches the pathologist's authoritative RSA-2048 signing digest byte-for-byte.
        </p>
        <div class="hash-display-box" style="margin-top: 0.75rem;">
          [openssl dgst -sha256 -verify pubkey -signature report.sig report.txt]
          <br>Status: <strong>Verified OK</strong>
        </div>
      `;
    } else {
      verificationResultBox.className = 'tamper-box tampered';
      verificationResultBox.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem; color: #991b1b; font-weight: 800; font-size: 1.05rem;">
          <span>⚠</span> CRITICAL ALERT: TAMPER DETECTED (Evidence E-27)
        </div>
        <p style="font-size: 0.85rem; color: #b91c1c; margin-top: 0.5rem;">
          Verification failure! The document contents have been altered after sign-off. Mathematical signature check failed!
        </p>
        <div class="hash-display-box tampered-hash" style="margin-top: 0.75rem;">
          Verification failure:
          <br>404712D682720000:error:02000068:rsa routines:ossl_rsa_verify:bad signature
          <br>Provider routines:rsa_verify_directly:RSA lib (Evidence E-27)
        </div>
      `;
    }
  }

  if (payloadInput) {
    payloadInput.addEventListener('input', updateVerification);
    updateVerification();
  }

  if (tamperBtn) {
    tamperBtn.addEventListener('click', function () {
      if (payloadInput) {
        payloadInput.value = payloadInput.value + "\nModified by Attacker: Result Positive";
        updateVerification();
      }
    });
  }

  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      if (payloadInput) {
        payloadInput.value = GENUINE_BASELINE;
        updateVerification();
      }
    });
  }
});
