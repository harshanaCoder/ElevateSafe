<?php
/**
 * Dashboard Page
 * Form for adding new breakdown records
 */

require_once __DIR__ . '/../include/functions.php';
startSession();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: ../index.php?error=UnauthorizedAccess");
    exit;
}

include_once "component/header.php";
include_once "../db/db.php";

// Safe Message Handling
function getAlertMessage(?string $msgCode): ?array {
    $messages = [
        'DataAddSuccessful' => ['success', 'Breakdown record added successfully!'],
        'DataAddFailed' => ['danger', 'Failed to add breakdown record. Please try again.'],
        'DatabaseError' => ['danger', 'Database error occurred. Please contact administrator.'],
        'InvalidInput' => ['warning', 'Please fill all required fields with valid data.'],
        'InvalidRequest' => ['danger', 'Invalid request. Please try again.'],
    ];

    return $messages[$msgCode] ?? null;
}

if (isset($_GET['message'])) {
    $alert = getAlertMessage($_GET['message']);
    if ($alert) {
        echo '<div class="container-fluid mt-3"><div class="alert alert-' . $alert[0] . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($alert[1]) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div></div>';
    }
}
?>

</main>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Stats overview or Welcome -->
            <div class="mb-4 animate__animated animate__fadeIn">
                <h2 class="fw-bold mb-1">Breakdown Submission</h2>
                <p class="text-muted">Enter the details of the incident below to log a new maintenance record.</p>
            </div>

            <!-- Safe Message Handling -->
            <?php
            function getDashboardMessage(?string $msgCode): ?array {
                $messages = [
                    'DataAddSuccessful' => ['success', 'fa-check-circle', 'Submission Successful', 'The breakdown record has been logged in the system.'],
                    'DataAddFailed' => ['danger', 'fa-times-circle', 'Submission Failed', 'There was an error saving the record. Please try again.'],
                    'InvalidInput' => ['warning', 'fa-exclamation-triangle', 'Invalid Entry', 'Please ensure all required fields are filled correctly.'],
                    'AdminOnly' => ['warning', 'fa-lock', 'Admin Access Required', 'Only the admin account can access analytics and history.'],
                ];
                return $messages[$msgCode] ?? null;
            }

            if (isset($_GET['message'])): 
                $alert = getDashboardMessage($_GET['message']);
                if ($alert):
            ?>
            <div class="alert alert-<?= $alert[0] ?> premium-card border-0 p-4 mb-4 animate__animated animate__fadeInDown" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas <?= $alert[1] ?> fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1 fw-bold"><?= $alert[2] ?></h5>
                        <p class="mb-0 opacity-75"><?= htmlspecialchars($alert[3]) ?></p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <?php endif; endif; ?>

            <!-- Main Form Card -->
            <div class="premium-card animate__animated animate__fadeInUp">
                <div class="card-gradient-header">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2"></i> Breakdown Details</h5>
                </div>
                <div class="p-4 p-md-5">
                    <form method="post" action="../include/dataAdd.inc.php" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        
                        <div class="row g-4">
                            <!-- Time Group -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Inform Details</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="date" name="inform_date" id="inform_date" class="form-control premium-input" required>
                                            <label for="inform_date">Date</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="time" name="inform_time" id="inform_time" class="form-control premium-input" required>
                                            <label for="inform_time">Time</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Attendance Details</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="date" name="attended_date" id="attended_date" class="form-control premium-input" required>
                                            <label for="attended_date">Date</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="time" name="attended_time" id="attended_time" class="form-control premium-input" required>
                                            <label for="attended_time">Time</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Unit Details -->
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" name="unit_no" id="unit_no" class="form-control premium-input" placeholder="e.g. ELEV-01" required>
                                    <label for="unit_no">Unit Number</label>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" name="nature_of_breakdown" id="nature_of_breakdown" class="form-control premium-input" placeholder="Description of breakdown" required>
                                    <label for="nature_of_breakdown">Nature of Breakdown</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea name="work_description" id="work_description" class="form-control premium-input" style="height: 120px" placeholder="Details of work done" required></textarea>
                                    <label for="work_description">Work Description / Rectification Details</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="team" id="team" class="form-control premium-input" placeholder="Technicians names" required>
                                    <label for="team">Assigned Maintenance Team</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 d-flex flex-column flex-md-row gap-3">
                            <button type="submit" name="submit" class="btn btn-premium-submit flex-grow-1 py-3 px-5">
                                <i class="fas fa-paper-plane me-2"></i> Submit Maintenance Report
                            </button>
                            <a href="dataHistory.php" class="btn btn-light border-0 py-3 px-4 fw-bold text-secondary" style="border-radius: 12px">
                                View History
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Quick Navigation Shortcuts -->
            <div class="mt-5 row g-4 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="col-md-6">
                    <a href="dataHistory.php" class="text-decoration-none">
                        <div class="premium-card p-4 h-100 d-flex align-items-center hover-lift">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="fas fa-history text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">View Maintenance History</h6>
                                <p class="small text-muted mb-0">Browse and filter previous records</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="analytics.php" class="text-decoration-none">
                        <div class="premium-card p-4 h-100 d-flex align-items-center hover-lift">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="fas fa-chart-bar text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Report Summary</h6>
                                <p class="small text-muted mb-0">Advanced analytics and exports</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-lift { transition: all 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1); }
</style>

<?php include_once "component/footer.php"; ?>