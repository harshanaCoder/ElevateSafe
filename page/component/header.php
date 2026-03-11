<?php
/**
 * Page Header Component
 */

require_once __DIR__ . '/../../include/functions.php';
startSession();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElevateSafe | Premium Maintenance Portal</title>
    
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
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --background: #f8fafc;
            --glass: rgba(255, 255, 255, 0.85);
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        body {
            background-color: var(--background);
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Glassmorphism Navigation */
        .premium-nav {
            background: var(--glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.75rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-btn {
            border-radius: 10px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .nav-btn-primary {
            background: var(--primary);
            color: white !important;
        }

        .nav-btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .nav-btn-outline {
            color: var(--secondary) !important;
            background: rgba(100, 116, 139, 0.05);
        }

        .nav-btn-outline:hover {
            background: rgba(100, 116, 139, 0.1);
            color: var(--primary) !important;
        }

        .nav-btn-danger {
            color: var(--danger) !important;
            background: rgba(239, 68, 68, 0.05);
        }

        .nav-btn-danger:hover {
            background: var(--danger);
            color: white !important;
        }

        /* Global Premium Elements */
        .premium-card {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card-gradient-header {
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            padding: 1.5rem;
            color: white;
        }

        .premium-input {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 12px 16px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .premium-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-premium-submit {
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            color: white;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-premium-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5);
        }

        .bg-purple {
            background-color: #a855f7 !important;
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .animate-slide-down {
            animation: slideDown 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <nav class="premium-nav">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="dashboard.php" class="brand-logo text-decoration-none">
                    <i class="fas fa-elevator animate__animated animate__bounceIn"></i>
                    <span>ElevateSafe</span>
                </a>
                
                <div class="d-flex gap-3 align-items-center">
                    <a href="dashboard.php" class="nav-btn <?= strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'nav-btn-primary' : 'nav-btn-outline' ?>">
                        <i class="fas fa-plus"></i> <span>Report</span>
                    </a>
                    <a href="analytics.php" class="nav-btn <?= strpos($_SERVER['PHP_SELF'], 'analytics.php') !== false ? 'nav-btn-primary' : 'nav-btn-outline' ?>">
                        <i class="fas fa-chart-pie"></i> <span>Analytics</span>
                    </a>
                    <a href="dataHistory.php" class="nav-btn <?= strpos($_SERVER['PHP_SELF'], 'dataHistory.php') !== false ? 'nav-btn-primary' : 'nav-btn-outline' ?>">
                        <i class="fas fa-history"></i> <span>History</span>
                    </a>
                    <div class="vr mx-2 text-secondary opacity-25"></div>
                    <a href="logout.php" class="nav-btn nav-btn-danger">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="py-4 flex-grow-1">