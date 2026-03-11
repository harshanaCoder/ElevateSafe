<?php
require_once __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../include/ai_service.php';

requireLogin();

if (!isAdminVerified()) {
    header("Location: requestAdminPassword.php?next=analytics");
    exit;
}

$pageTitle = "AI Maintenance Analytics";
include 'component/header.php';

// --- Data Fetching for Charts ---

// 1. Breakdowns by Category
$catSql = "SELECT category, COUNT(*) as count FROM breakdowns GROUP BY category";
$catResult = $conn->query($catSql);
$categoryData = [];
while ($row = $catResult->fetch_assoc()) {
    $categoryData[] = $row;
}

// 2. Breakdowns by Month (Last 6 months)
$trendSql = "SELECT DATE_FORMAT(inform_date, '%Y-%m') as month, COUNT(*) as count 
             FROM breakdowns 
             WHERE inform_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY month 
             ORDER BY month ASC";
$trendResult = $conn->query($trendSql);
$trendData = [];
while ($row = $trendResult->fetch_assoc()) {
    $trendData[] = $row;
}

// 3. Top Faulty Units
$unitSql = "SELECT unit_no, COUNT(*) as count FROM breakdowns GROUP BY unit_no ORDER BY count DESC LIMIT 5";
$unitResult = $conn->query($unitSql);
$unitData = [];
while ($row = $unitResult->fetch_assoc()) {
    $unitData[] = $row;
}

// --- AI Insights Generator ---
$aiInsight = "Our AI is currently analyzing your data...";
if (count($categoryData) > 0) {
    $dataSummary = "Current breakdown counts by category: ";
    foreach ($categoryData as $item) {
        $dataSummary .= ($item['category'] ?? 'Uncategorized') . ": " . $item['count'] . ", ";
    }
    
    $aiInsight = AIService::generateInsight($dataSummary);
}
?>

<div class="container py-4">
    <div class="row mb-4 animate__animated animate__fadeIn">
        <div class="col-12">
            <div class="premium-card p-4 d-flex align-items-center justify-content-between bg-white shadow-sm border-0" style="border-radius: 20px;">
                <div>
                    <h2 class="fw-bold mb-1 text-dark">AI Maintenance Analytics</h2>
                    <p class="text-muted mb-0 small">Visual insights and performance metrics powered by AI</p>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2" onclick="window.print()">
                        <i class="fas fa-download me-1"></i> Export Report
                    </button>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insight Alert -->
    <div class="row mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-0 d-flex flex-column flex-md-row">
                    <div class="bg-primary p-4 d-flex align-items-center justify-content-center text-white" style="min-width: 100px;">
                        <i class="fas fa-robot fa-2x"></i>
                    </div>
                    <div class="p-4 bg-white flex-grow-1">
                        <h6 class="fw-bold text-uppercase small text-primary mb-2">AI Executive Summary</h6>
                        <p class="mb-0 text-dark fw-medium" id="ai-insight-text">
                            <?= htmlspecialchars($aiInsight) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Breakdown by Category (Pie) -->
        <div class="col-lg-5 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="premium-card p-4 bg-white shadow-sm border-0 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">Breakdowns by Category</h5>
                <div style="height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Trend (Line) -->
        <div class="col-lg-7 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="premium-card p-4 bg-white shadow-sm border-0 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">6-Month Reliability Trend</h5>
                <div style="height: 300px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Faulty Units (Bar) -->
        <div class="col-lg-12 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="premium-card p-4 bg-white shadow-sm border-0" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4">Most Frequent Failures by Unit</h5>
                <div style="height: 300px;">
                    <canvas id="unitChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Category Chart
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryLabels = <?= json_encode(array_column($categoryData, 'category')) ?>;
    const categoryCounts = <?= json_encode(array_column($categoryData, 'count')) ?>;
    
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryCounts,
                backgroundColor: [
                    '#6366f1', '#a855f7', '#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#94a3b8'
                ],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            cutout: '70%'
        }
    });

    // 2. Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendLabels = <?= json_encode(array_column($trendData, 'month')) ?>;
    const trendCounts = <?= json_encode(array_column($trendData, 'count')) ?>;

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Total Breakdowns',
                data: trendCounts,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#6366f1'
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // 3. Unit Chart
    const unitCtx = document.getElementById('unitChart').getContext('2d');
    const unitLabels = <?= json_encode(array_column($unitData, 'unit_no')) ?>;
    const unitCounts = <?= json_encode(array_column($unitData, 'count')) ?>;

    new Chart(unitCtx, {
        type: 'bar',
        data: {
            labels: unitLabels,
            datasets: [{
                label: 'Incident Count',
                data: unitCounts,
                backgroundColor: '#a855f7',
                borderRadius: 8
            }]
        },
        options: {
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: { beginAtZero: true, grid: { display: false } },
                y: { grid: { display: false } }
            }
        }
    });
});
</script>

<?php
include 'component/footer.php';
?>
