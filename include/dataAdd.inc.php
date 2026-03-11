<?php
/**
 * Add Breakdown Record Handler
 * Processes form submissions for new breakdown records
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/ai_service.php';

startSession();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: ../index.php?error=UnauthorizedAccess");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate CSRF Token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrfToken)) {
        header("Location: ../page/dashboard.php?message=InvalidRequest");
        exit;
    }

    // Validate required fields
    $requiredFields = [
        'unit_no', 'inform_date', 'inform_time',
        'attended_date', 'attended_time',
        'nature_of_breakdown', 'work_description', 'team'
    ];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            header("Location: ../page/dashboard.php?message=InvalidInput");
            exit;
        }
    }

    // Validate and sanitize inputs
    $unitNo = validateString($_POST['unit_no'], 50);
    $informDate = validateDate($_POST['inform_date']);
    $informTime = validateTime($_POST['inform_time']);
    $attendedDate = validateDate($_POST['attended_date']);
    $attendedTime = validateTime($_POST['attended_time']);
    $nature = validateString($_POST['nature_of_breakdown'], 255);
    $work = validateString($_POST['work_description'], 1000);
    $team = validateString($_POST['team'], 100);

    // Validate all inputs
    if ($unitNo === false || $informDate === false || $informTime === false ||
        $attendedDate === false || $attendedTime === false ||
        $nature === false || $work === false || $team === false) {
        header("Location: ../page/dashboard.php?message=InvalidInput");
        exit;
    }

    // AI Categorization
    $category = AIService::categorizeBreakdown($nature, $work);

    try {
        $sql = "INSERT INTO breakdowns (unit_no, category, nature_of_breakdown, work_description, inform_date, inform_time, attendent_date, attended_time, team, submit_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Database prepare statement failed");
        }

        $stmt->bind_param("sssssssss", $unitNo, $category, $nature, $work, $informDate, $informTime, $attendedDate, $attendedTime, $team);

        if ($stmt->execute()) {
            $stmt->close();
            // Regenerate CSRF token after successful submission
            regenerateCSRFToken();
            header("Location: ../page/dashboard.php?message=DataAddSuccessful");
        } else {
            throw new Exception("Database execution failed");
        }
    } catch (Throwable $e) {
        if (isset($stmt)) {
            $stmt->close();
        }
        error_log("Error in dataAdd.inc.php: " . $e->getMessage());
        header("Location: ../page/dashboard.php?message=DataAddFailed");
    }
    exit;
} else {
    header("Location: ../page/dashboard.php?message=InvalidRequest");
    exit;
}