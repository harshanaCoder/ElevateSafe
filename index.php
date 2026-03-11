<?php
require_once __DIR__ . '/include/functions.php';
startSession();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ElevateSafe Premium</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --background: #f8fafc;
            --glass: rgba(255, 255, 255, 0.9);
        }

        body {
            background-color: var(--background);
            background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, rgba(168, 85, 247, 0.15) 0, transparent 50%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .premium-login-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
            margin: auto;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            padding: 2.5rem 2rem;
            color: white;
            text-align: center;
        }

        .brand-logo i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .premium-input {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 12px 16px;
            padding-left: 45px;
            transition: all 0.2s ease;
        }

        .premium-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.2s ease;
        }

        .input-icon-wrapper input:focus + i {
            color: var(--primary);
        }

        .btn-premium-login {
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: 14px;
            color: white;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-premium-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container py-5 d-flex justify-content-center">
        <div class="premium-login-card animate__animated animate__zoomIn">
            <div class="login-header">
                <div class="brand-logo">
                    <i class="fas fa-elevator animate__animated animate__bounceIn"></i>
                </div>
                <h3 class="fw-bold mb-1">ElevateSafe</h3>
                <p class="mb-0 opacity-75">Maintenance Management Portal</p>
            </div>
            
            <div class="p-4 p-md-5 bg-white">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger border-0 p-3 mb-4 animate__animated animate__shakeX" style="border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span class="small fw-bold"><?= htmlspecialchars($error) ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <form method="post" action="include/login.inc.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Username</label>
                        <div class="input-icon-wrapper">
                            <input type="text" name="username" class="form-control premium-input" placeholder="Enter your username" required autocomplete="username">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Password</label>
                        <div class="input-icon-wrapper">
                            <input type="password" name="password" class="form-control premium-input" placeholder="••••••••" required autocomplete="current-password">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-premium-login w-100 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Sign In
                    </button>
                    
                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="text-muted small mb-0">Authorized Personnel Only</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>