<?php
/**
 * Admin Password Verification Page
 * Provides access to report summary after password verification
 */

require_once __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/../db/db.php';
startSession();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: ../index.php?error=UnauthorizedAccess");
    exit;
}

$allowedNextPages = ['analytics.php', 'dataHistory.php'];
$requestedNext = $_GET['next'] ?? ($_POST['next'] ?? 'analytics');
$nextPage = $requestedNext . '.php';
if (in_array($requestedNext, $allowedNextPages, true)) {
    $nextPage = $requestedNext;
} elseif (!in_array($nextPage, $allowedNextPages, true)) {
    $nextPage = 'analytics.php';
}

if (isAdminVerified()) {
    header("Location: ../page/" . $nextPage);
    exit;
}

$error = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF Token
    $submittedCsrfToken = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($submittedCsrfToken)) {
        $error = "Invalid request. Please try again.";
    } else {
        $adminPassword = $_POST['admin_password'] ?? '';

        if (empty($adminPassword)) {
            $error = "Please enter the admin password.";
        } else {
            $username = "admin";
            $hashedPassword = null;

            // Prefer dedicated admin table, fallback to users for compatibility.
            $lookups = [
                ["SELECT password FROM admin WHERE LOWER(username) = LOWER(?) LIMIT 1", true],
                ["SELECT password FROM admin LIMIT 1", false],
                ["SELECT password FROM users WHERE LOWER(username) = LOWER(?) LIMIT 1", true],
            ];

            foreach ($lookups as [$sql, $needsUsername]) {
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    continue;
                }

                if ($needsUsername) {
                    $stmt->bind_param("s", $username);
                }

                if (!$stmt->execute()) {
                    $stmt->close();
                    continue;
                }

                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($candidatePassword);
                    $stmt->fetch();
                    if (!empty($candidatePassword)) {
                        $hashedPassword = $candidatePassword;
                        $stmt->close();
                        break;
                    }
                }

                $stmt->close();
            }

            if ($hashedPassword === null) {
                $error = "Admin configuration error. Contact administrator.";
            } elseif (password_verify($adminPassword, $hashedPassword)) {
                markAdminVerified();
                regenerateCSRFToken();
                header("Location: ../page/" . $nextPage . "?message=adminLoginSuccess");
                exit;
            } else {
                $error = "Incorrect admin password. Access denied.";
            }
        }
    }
}

$csrfToken = generateCSRFToken();
include_once "component/header.php";
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-lock me-2"></i> Admin Verification Required
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Please enter the admin password to access the report summary.</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="next" value="<?= htmlspecialchars(str_replace('.php', '', $nextPage)) ?>">
                <div class="mb-3">
                    <label for="admin_password" class="form-label">Admin Password</label>
                    <input type="password" class="form-control" id="admin_password" name="admin_password"
                           required autocomplete="current-password">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i> Verify
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once "component/footer.php"; ?>