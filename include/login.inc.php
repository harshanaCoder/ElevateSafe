<?php
/**
 * Login Handler
 * Handles user authentication with security measures
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../db/db.php';

startSession();

// Redirect if already logged in
if (isset($_SESSION['userid'])) {
    header("Location: ../page/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF Token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrfToken)) {
        redirectWithError('../index.php', "Invalid request. Please try again.");
    }

    // Get and validate username
    $username = trim($_POST['username'] ?? '');

    if (empty($username)) {
        redirectWithError('../index.php', "Please enter your credentials.");
    }

    // Check for lockout
    if (isLockedOut($username)) {
        $remainingTime = getRemainingLockoutTime($username);
        redirectWithError('../index.php', "Too many failed attempts. Please try again in {$remainingTime} minute(s).");
    }

    // Get and validate password
    $password = $_POST['password'] ?? '';

    if (empty($password)) {
        recordLoginAttempt($username);
        redirectWithError('../index.php', "Please enter your credentials.");
    }

    // Query database
    $sql = "SELECT id, username, password FROM users WHERE LOWER(username) = LOWER(?) LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Login prepare failed: " . $conn->error);
        redirectWithError('../index.php', "An error occurred. Please try again later.");
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $dbUsername, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            // Clear failed login attempts
            clearLoginAttempts($username);

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['userid'] = $id;
            $_SESSION['username'] = $dbUsername;
            $_SESSION['login_time'] = time();
            $_SESSION['admin_verified'] = false;

            // Only the admin account can access analytics/history workflow.
            if (strcasecmp($dbUsername, 'admin') === 0) {
                $_SESSION['is_admin'] = true;
            } else {
                $_SESSION['is_admin'] = false;
            }

            // Regenerate CSRF token after successful login
            regenerateCSRFToken();

            $stmt->close();
            header("Location: ../page/dashboard.php");
            exit;
        }
    }

    // Generic error message (don't reveal if username exists)
    $stmt->close();
    recordLoginAttempt($username);
    redirectWithError('../index.php', "Invalid username or password.");
} else {
    header("Location: ../index.php?error=RestrictAccess");
    exit;
}