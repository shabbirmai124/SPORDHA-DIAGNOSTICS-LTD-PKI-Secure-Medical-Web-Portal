// Md Shabbir Mia
function fallbackSha256(ascii) {
  function rightRotate(value, amount) {
    return (value >>> amount) | (value << (32 - amount));
  }
  const mathPow = Math.pow;
  const maxWord = mathPow(2, 32);
  let result = '';
  const words = [];
  const asciiBitLength = ascii.length * 8;
  const hash = [];
  const k = [];
  let primeCounter = 0;
  const isComposite = {};
  for (let candidate = 2; primeCounter < 64; candidate++) {
    if (!isComposite[candidate]) {
      for (let i = 0; i < 313; i += candidate) {
        isComposite[i] = candidate;
      }
      hash[primeCounter] = (mathPow(candidate, 0.5) * maxWord) | 0;
      k[primeCounter++] = (mathPow(candidate, 1 / 3) * maxWord) | 0;
    }
  }
  ascii += '\x80';
  while ((ascii.length % 64) - 56) ascii += '\x00';
  for (let i = 0; i < ascii.length; i++) {
    const j = ascii.charCodeAt(i);
    words[i >> 2] |= j << (((3 - i) % 4) * 8);
  }
  words[words.length] = (asciiBitLength / maxWord) | 0;
  words[words.length] = asciiBitLength;
  for (let j = 0; j < words.length;) {
    const w = words.slice(j, (j += 16));
    const oldHash = hash.slice(0);
    for (let i = 0; i < 64; i++) {
      const w15 = w[i - 15];
      const w2 = w[i - 2];
      const a = hash[0];
      const e = hash[4];
      const temp1 = hash[7] +
        (rightRotate(e, 6) ^ rightRotate(e, 11) ^ rightRotate(e, 25)) +
        ((e & hash[5]) ^ (~e & hash[6])) +
        k[i] +
        (w[i] = i < 16 ? w[i] : (
          w[i - 16] +
          (rightRotate(w15, 7) ^ rightRotate(w15, 18) ^ (w15 >>> 3)) +
          w[i - 7] +
          (rightRotate(w2, 17) ^ rightRotate(w2, 19) ^ (w2 >>> 10))
        ) | 0);
      const temp2 = (rightRotate(a, 2) ^ rightRotate(a, 13) ^ rightRotate(a, 22)) +
        ((a & hash[1]) ^ (a & hash[2]) ^ (hash[1] & hash[2]));
      hash.unshift((temp1 + temp2) | 0);
      hash[4] = (hash[4] + temp1) | 0;
      hash.pop();
    }
    for (let i = 0; i < 8; i++) {
      hash[i] = (hash[i] + oldHash[i]) | 0;
    }
  }
  for (let i = 0; i < 8; i++) {
    for (let j = 3; j + 1; j--) {
      const b = (hash[i] >> (j * 8)) & 255;
      result += (b < 16 ? '0' : '') + b.toString(16);
    }
  }
  return result;
}

async function sha256(message) {
  if (window.crypto && window.crypto.subtle) {
    const msgBuffer = new TextEncoder().encode(message);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
  }
  const utf8 = unescape(encodeURIComponent(message));
  return fallbackSha256(utf8);
}

document.addEventListener('DOMContentLoaded', function () {
  const payloadInput = document.getElementById('interactivePayloadInput');
  const computedHashEl = document.getElementById('liveComputedHash');
  const verificationResultBox = document.getElementById('interactiveVerifyResult');
  const tamperBtn = document.getElementById('simulateTamperBtn');
  const resetBtn = document.getElementById('resetPayloadBtn');

  const GENUINE_BASELINE = "Spordha Diagnostics Ltd - Patient: Test Patient, Test: Dengue NS1, Result: Negative";

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
