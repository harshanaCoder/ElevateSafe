<?php
// Get error code
$errorCode = isset($_GET['code']) ? $_GET['code'] : 'general';

// Define error messages
$errorMessages = [
    'db_connection' => 'We are experiencing database connection issues. Our team has been notified and is working on it.',
    'general' => 'An unexpected error occurred. Please try again later.'
];

$errorMessage = $errorMessages[$errorCode] ?? $errorMessages['general'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Error | ElevateSafe</title>
    
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
            --danger: #ef4444;
            --background: #f8fafc;
            --glass: rgba(255, 255, 255, 0.9);
        }
        
        body {
            background-color: var(--background);
            background-image: radial-gradient(at 0% 0%, rgba(239, 68, 68, 0.05) 0, transparent 50%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
        }
        
        .premium-error-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            margin: auto;
        }

        .error-header {
            background: linear-gradient(135deg, var(--danger), #f43f5e);
            padding: 2.5rem 2rem;
            color: white;
            text-align: center;
        }
        
        .btn-premium-home {
            background: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            color: white;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .btn-premium-home:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .error-msg-box {
            background: rgba(239, 68, 68, 0.05);
            border-left: 4px solid var(--danger);
            padding: 1.25rem;
            border-radius: 0 12px 12px 0;
            margin-bottom: 2rem;
            font-weight: 500;
            color: #1e293b;
        }
    </style>
</head>
<body>
    <div class="container py-5 d-flex justify-content-center">
        <div class="premium-error-card animate__animated animate__zoomIn">
            <div class="error-header">
                <i class="fas fa-exclamation-triangle fa-3x mb-3 animate__animated animate__headShake animate__infinite animate__slower"></i>
                <h3 class="fw-bold mb-0">System Error</h3>
            </div>
            
            <div class="p-4 p-md-5 bg-white text-center">
                <div class="error-msg-box text-start animate__animated animate__fadeInUp">
                    <?= htmlspecialchars($errorMessage); ?>
                </div>
                
                <p class="text-secondary mb-4 small animate__animated animate__fadeInUp">
                    We've logged this incident. Please try again later or contact your administrator if this persists.
                </p>
                
                <div class="d-grid gap-3 animate__animated animate__fadeInUp">
                    <a href="../index.php" class="btn btn-premium-home">
                        <i class="fas fa-home me-2"></i> Return to Main Portal
                    </a>
                </div>
                
                <div class="mt-4 pt-4 border-top text-muted small">
                    Error Code: <span class="fw-bold"><?= htmlspecialchars($errorCode); ?></span> | <?= date('H:i:s') ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
