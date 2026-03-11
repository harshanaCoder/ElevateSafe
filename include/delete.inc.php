<?php
/**
 * Delete Record Handler
 * Handles deletion of breakdown records via POST request
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../db/db.php';

startSession();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: ../index.php?error=UnauthorizedAccess");
    exit;
}

if (!isAdminVerified()) {
    header("Location: ../page/requestAdminPassword.php?next=dataHistory");
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../page/dataHistory.php?error=InvalidMethod");
    exit;
}

// Validate CSRF Token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    header("Location: ../page/dataHistory.php?error=InvalidRequest");
    exit;
}

// Validate delete_id
$deleteId = $_POST['delete_id'] ?? null;
if ($deleteId === null || !is_numeric($deleteId)) {
    header("Location: ../page/dataHistory.php?error=InvalidId");
    exit;
}

$deleteId = (int) $deleteId;

// Get filter parameters for redirect
$start = $_POST['start'] ?? null;
$end = $_POST['end'] ?? null;
$page = $_POST['page'] ?? 1;

try {
    $stmt = $conn->prepare("DELETE FROM breakdowns WHERE id = ?");
    $stmt->bind_param("i", $deleteId);

    if ($stmt->execute()) {
        $stmt->close();
        regenerateCSRFToken();

        // Build redirect URL with filters
        $redirectUrl = "../page/dataHistory.php?message=DeleteSuccessful";
        if ($start && $end) {
            $redirectUrl .= "&start=" . urlencode($start) . "&end=" . urlencode($end);
        }
        $redirectUrl .= "&page=" . urlencode($page);

        header("Location: " . $redirectUrl);
        exit;
    } else {
        throw new Exception("Delete failed");
    }
} catch (Exception $e) {
    error_log("Delete error: " . $e->getMessage());
    header("Location: ../page/dataHistory.php?error=DeleteFailed");
    exit;
}