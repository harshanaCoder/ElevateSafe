<?php
/**
 * Security and Utility Functions
 */

// Start session if not already started with security parameters
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        // These settings are also in config.php but redundant setting here is safer
        if (!headers_sent()) {
            session_start();
        } else {
            // Fallback if headers already sent (shouldn't happen with proper structure)
            session_start();
        }
    }
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken(): string {
    startSession();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF Token
 */
function validateCSRFToken(?string $token): bool {
    startSession();

    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time'])) {
        return false;
    }

    if (empty($token)) {
        return false;
    }

    // Check if token matches
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }

    // Check if token is expired
    if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_EXPIRY) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }

    return true;
}

/**
 * Regenerate CSRF Token after form submission
 */
function regenerateCSRFToken(): string {
    startSession();

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();

    return $_SESSION['csrf_token'];
}

/**
 * Output CSRF hidden field for forms
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken()) . '">';
}

/**
 * Sanitize input string
 */
function sanitizeInput(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and sanitize string input
 */
function validateString(?string $input, int $maxLength = 255): string|false {
    if ($input === null || $input === '') {
        return false;
    }

    $sanitized = trim($input);

    if (strlen($sanitized) > $maxLength) {
        return false;
    }

    return $sanitized;
}

/**
 * Validate date format (Y-m-d)
 */
function validateDate(?string $date): string|false {
    if ($date === null || $date === '') {
        return false;
    }

    $d = DateTime::createFromFormat('Y-m-d', $date);

    if (!$d || $d->format('Y-m-d') !== $date) {
        return false;
    }

    return $date;
}

/**
 * Validate time format (H:i)
 */
function validateTime(?string $time): string|false {
    if ($time === null || $time === '') {
        return false;
    }

    $t = DateTime::createFromFormat('H:i', $time);

    if (!$t || $t->format('H:i') !== $time) {
        return false;
    }

    return $time;
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['userid']);
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: ../index.php?error=UnauthorizedAccess");
        exit;
    }
}

/**
 * Check if logged-in user is the admin account
 */
function isAdminUser(): bool {
    startSession();

    if (!isset($_SESSION['username'])) {
        return false;
    }

    return strcasecmp((string)$_SESSION['username'], 'admin') === 0;
}

/**
 * Check if admin has passed admin password verification
 */
function isAdminVerified(): bool {
    startSession();
    return !empty($_SESSION['admin_verified']);
}

/**
 * Mark current admin session as verified for protected pages
 */
function markAdminVerified(): void {
    startSession();
    $_SESSION['admin_verified'] = true;
    $_SESSION['admin_verified_at'] = time();
}

/**
 * Record login attempt for rate limiting
 */
function recordLoginAttempt(string $username): void {
    startSession();

    $key = 'login_attempts_' . $username;
    $attempts = $_SESSION[$key] ?? 0;
    $_SESSION[$key] = $attempts + 1;
    $_SESSION[$key . '_time'] = time();
}

/**
 * Check if account is locked out
 */
function isLockedOut(string $username): bool {
    startSession();

    $key = 'login_attempts_' . $username;
    $attempts = $_SESSION[$key] ?? 0;

    if ($attempts >= MAX_LOGIN_ATTEMPTS) {
        $lockoutTime = $_SESSION[$key . '_time'] ?? 0;
        if (time() - $lockoutTime < LOGIN_LOCKOUT_TIME) {
            return true;
        }
        // Lockout expired, reset attempts
        clearLoginAttempts($username);
    }

    return false;
}

/**
 * Clear login attempts
 */
function clearLoginAttempts(string $username): void {
    startSession();

    $key = 'login_attempts_' . $username;
    unset($_SESSION[$key], $_SESSION[$key . '_time']);
}

/**
 * Get remaining lockout time in minutes
 */
function getRemainingLockoutTime(string $username): int {
    startSession();

    $key = 'login_attempts_' . $username;
    $lockoutTime = $_SESSION[$key . '_time'] ?? time();

    $remaining = LOGIN_LOCKOUT_TIME - (time() - $lockoutTime);
    return max(0, ceil($remaining / 60));
}

/**
 * Redirect with error message
 */
function redirectWithError(string $location, string $error): void {
    $_SESSION['error'] = $error;
    header("Location: $location");
    exit;
}

/**
 * Redirect with success message
 */
function redirectWithMessage(string $location, string $message): void {
    header("Location: $location?message=" . urlencode($message));
    exit;
}