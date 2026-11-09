<?php
/**
 * Spordha Diagnostics Ltd. - Tests & Clinical Services Directory
 * Monochromatic Professional Interface
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Diagnostic Tests & Clinical Services";
require_once __DIR__ . '/includes/header.php';

$testCatalog = [
    [
        'category' => 'Serology & Infectious Diseases',
        'name' => 'Dengue NS1 Antigen & Platelet Count',
        'code' => 'TEST-DEN-01',
        'specimen' => 'Serum Blood (3 ml)',
        'tat' => '3 Hours (Emergency Rapid)',
        'fee' => 'BDT 1,200',
        'desc' => 'High-affinity ELISA detection of Dengue virus NS1 antigen paired with automated platelet count, critical for acute febrile monitoring.',
        'sample_uid' => 'SPD-2026-90412'
    ],
    [
        'category' => 'Clinical Biochemistry',
        'name' => 'Comprehensive Lipid Profile & Fasting Blood Sugar',
        'code' => 'TEST-LIP-02',
        'specimen' => 'Fasting Serum & Fluoride Blood (5 ml)',
        'tat' => '6 Hours',
        'fee' => 'BDT 1,600',
        'desc' => 'Enzymatic spectrophotometric assay evaluating Total Cholesterol, Triglycerides, HDL, LDL, VLDL, and Fasting Plasma Glucose.',
        'sample_uid' => 'SPD-2026-90413'
    ],
    [
        'category' => 'Hematology',
        'name' => 'Complete Blood Count (CBC) with ESR & PBF',
        'code' => 'TEST-CBC-03',
        'specimen' => 'Whole Blood in K2-EDTA (3 ml)',
        'tat' => '4 Hours',
        'fee' => 'BDT 800',
        'desc' => '5-part differential automated hematology analyzer profiling Hemoglobin, RBC, Hematocrit, Platelets, TLC, DLC, and Westergren ESR.',
        'sample_uid' => 'SPD-2026-90414'
    ],
    [
        'category' => 'Corporate & Visa Health',
        'name' => 'Corporate Visa Health Screening Panel',
        'code' => 'TEST-CORP-04',
        'specimen' => 'Serum Blood & Urine',
        'tat' => '12 Hours',
        'fee' => 'BDT 3,500',
        'desc' => 'Mandatory overseas and employment health certification including HBsAg, Anti-HCV, VDRL, Chest X-Ray review, and Urine Routine.',
        'sample_uid' => 'SPD-2026-90499'
    ],
    [
        'category' => 'Molecular Diagnostics',
        'name' => 'COVID-19 RT-PCR Screening',
        'code' => 'TEST-PCR-05',
        'specimen' => 'Nasopharyngeal / Oropharyngeal Swab',
        'tat' => '8 Hours',
        'fee' => 'BDT 2,000',
        'desc' => 'Real-time reverse transcription polymerase chain reaction targeting RdRp and E genes with international QR verification.',
        'sample_uid' => 'SPD-2026-90412'
    ],
    [
        'category' => 'Clinical Biochemistry',
        'name' => 'Renal Function Test (Creatinine, Urea, Electrolytes)',
        'code' => 'TEST-RFT-06',
        'specimen' => 'Serum Blood (3 ml)',
        'tat' => '5 Hours',
        'fee' => 'BDT 1,400',
        'desc' => 'Kinetic Jaffe assay for Serum Creatinine, Blood Urea Nitrogen (BUN), and ion-selective electrode electrolytes (Na+, K+, Cl-).',
        'sample_uid' => 'SPD-2026-90413'
    ]
];
?>

<div style="background-color: var(--color-gray-subtle); padding: 3rem 0; border-bottom: 1px solid var(--color-gray-border);">
  <div class="container">
    <span class="section-badge">Diagnostic Directory</span>
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.5px; margin-top: 0.5rem;">
      Clinical Pathology & Diagnostic Tests
    </h1>
    <p style="color: var(--color-gray-dark); margin-top: 0.25rem;">
      Comprehensive diagnostic test catalog processed at our central laboratory and three branch collection centers.
    </p>
  </div>
</div>

<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">
  
  <div class="cards-grid-3">
    <?php foreach ($testCatalog as $t): ?>
      <div class="card-item">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
          <span class="badge badge-warning-notice"><?php echo htmlspecialchars($t['category']); ?></span>
          <strong style="font-size: 1.1rem; color: #000;"><?php echo htmlspecialchars($t['fee']); ?></strong>
        </div>

        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.5rem;">
          <?php echo htmlspecialchars($t['name']); ?>
        </h3>

        <p style="font-size: 0.85rem; color: var(--color-gray-dark); margin-bottom: 1rem; line-height: 1.6;">
          <?php echo htmlspecialchars($t['desc']); ?>
        </p>

        <div style="background: var(--color-gray-subtle); padding: 0.85rem; border-radius: var(--radius-md); font-size: 0.8rem; margin-bottom: 1.25rem; border: 1px solid var(--color-gray-border);">
          <div><strong>Specimen:</strong> <?php echo htmlspecialchars($t['specimen']); ?></div>
          <div style="margin-top: 0.25rem;"><strong>Turnaround Time:</strong> <?php echo htmlspecialchars($t['tat']); ?></div>
          <div style="margin-top: 0.25rem;"><strong>Test Code:</strong> <code><?php echo htmlspecialchars($t['code']); ?></code></div>
        </div>

        <div style="display: flex; gap: 0.5rem; margin-top: auto;">
          <a href="verify.php?uid=<?php echo urlencode($t['sample_uid']); ?>" class="btn btn-secondary btn-sm" style="flex: 1;">
            Inspect Sample Report
          </a>
          <a href="branch_upload.php" class="btn btn-outline-dark btn-sm">
            Book Requisition
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
