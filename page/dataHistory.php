<?php
/**
 * Data History Page
 * View and manage breakdown records
 */

require_once __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/../db/db.php';
startSession();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: ../index.php?error=UnauthorizedAccess");
    exit;
}

if (!isAdminVerified()) {
    header("Location: requestAdminPassword.php?next=dataHistory");
    exit;
}

include_once "component/header.php";

// Safe Message Handling
function getAlertMessage(?string $msgCode): ?array {
    $messages = [
        'DeleteSuccessful' => ['success', 'Record deleted successfully!'],
        'InvalidRequest' => ['danger', 'Invalid request. Please try again.'],
        'InvalidId' => ['danger', 'Invalid record ID.'],
        'InvalidMethod' => ['danger', 'Invalid request method.'],
        'DeleteFailed' => ['danger', 'Failed to delete record. Please try again.'],
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

// Fetch and sanitize filter dates
$filter_start = validateDate($_GET['start'] ?? null) ?: null;
$filter_end = validateDate($_GET['end'] ?? null) ?: null;

// Pagination setup
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Count total records
$count_query = "SELECT COUNT(*) FROM breakdowns";
if ($filter_start && $filter_end) {
    $count_query .= " WHERE inform_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param("ss", $filter_start, $filter_end);
} else {
    $stmt = $conn->prepare($count_query);
}
$stmt->execute();
$stmt->bind_result($total_records);
$stmt->fetch();
$stmt->close();

$total_pages = ceil($total_records / $limit);

// Fetch records
$query = "SELECT * FROM breakdowns";
if ($filter_start && $filter_end) {
    $query .= " WHERE inform_date BETWEEN ? AND ?";
}
$query .= " ORDER BY inform_date DESC LIMIT ? OFFSET ?";

if ($filter_start && $filter_end) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $filter_start, $filter_end, $limit, $offset);
} else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</main>

<div class="container-fluid px-md-5">
    <div class="mb-4 animate__animated animate__fadeIn">
        <h2 class="fw-bold mb-1">Maintenance History</h2>
        <p class="text-muted">Review, filter, and export historical breakdown records.</p>
    </div>

    <!-- Filter Card -->
    <div class="premium-card mb-4 animate__animated animate__fadeInUp">
        <div class="p-4">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start" class="form-label small fw-bold text-uppercase text-secondary">From Date</label>
                    <input type="date" name="start" id="start" class="form-control premium-input" value="<?= htmlspecialchars($filter_start ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="end" class="form-label small fw-bold text-uppercase text-secondary">To Date</label>
                    <input type="date" name="end" id="end" class="form-control premium-input" value="<?= htmlspecialchars($filter_end ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-premium-submit flex-grow-1">
                            <i class="fas fa-filter me-2"></i> Apply Filters
                        </button>
                        <?php if ($filter_start || $filter_end): ?>
                            <a href="dataHistory.php" class="btn btn-light border-0 px-3 d-flex align-items-center" style="border-radius: 12px">
                                <i class="fas fa-redo"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-2 text-md-end">
                    <button type="button" class="btn btn-success fw-bold py-2 px-4 shadow-sm" style="border-radius: 12px" onclick="exportData()">
                        <i class="fas fa-file-excel me-2"></i> Export
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="premium-card animate__animated animate__fadeInUp animate__delay-1s">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 small text-uppercase fw-bold text-secondary">Inform Info</th>
                        <th class="py-3 border-0 small text-uppercase fw-bold text-secondary">Unit No</th>
                        <th class="py-3 border-0 small text-uppercase fw-bold text-secondary">Category</th>
                        <th class="py-3 border-0 small text-uppercase fw-bold text-secondary">Nature of Problem</th>
                        <th class="py-3 border-0 small text-uppercase fw-bold text-secondary">Work Description</th>
                        <th class="py-3 border-0 small text-uppercase fw-bold text-secondary">Attended Info</th>
                        <th class="py-3 border-0 small text-uppercase fw-bold text-secondary">Maintenance Team</th>
                        <th class="py-3 border-0 small text-uppercase fw-bold text-secondary text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $catClass = 'bg-secondary';
                            switch($row['category']) {
                                case 'Electrical': $catClass = 'bg-warning text-dark'; break;
                                case 'Mechanical': $catClass = 'bg-info text-dark'; break;
                                case 'Safety System': $catClass = 'bg-danger'; break;
                                case 'Door System': $catClass = 'bg-primary'; break;
                                case 'Electronic Board': $catClass = 'bg-purple text-white'; break;
                                case 'Hydraulic': $catClass = 'bg-primary bg-opacity-75'; break;
                                case 'General Maintenance': $catClass = 'bg-success'; break;
                            }
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= htmlspecialchars($row['inform_date']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($row['inform_time']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                        <?= htmlspecialchars($row['unit_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $catClass ?> px-2 py-1 rounded small fw-600">
                                        <?= htmlspecialchars($row['category'] ?? 'Uncategorized') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium"><?= htmlspecialchars($row['nature_of_breakdown']) ?></div>
                                </td>
                                <td style="max-width: 300px">
                                    <div class="text-truncate" title="<?= htmlspecialchars($row['work_description']) ?>">
                                        <?= htmlspecialchars($row['work_description']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['attendent_date']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($row['attended_time']) ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 p-2 rounded-circle me-2">
                                            <i class="fas fa-tools text-secondary small"></i>
                                        </div>
                                        <span class="small fw-medium"><?= htmlspecialchars($row['team']) ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger btn-delete-record border-0" 
                                            data-id="<?= (int)$row['id'] ?>" 
                                            style="padding: 8px 12px; border-radius: 8px">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No records found matching your filters.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="p-4 border-top bg-light bg-opacity-25">
            <nav>
                <ul class="pagination pagination-premium justify-content-center mb-0 gap-2">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?start=<?= urlencode($filter_start ?? '') ?>&end=<?= urlencode($filter_end ?? '') ?>&page=<?= $page - 1 ?>"><i class="fas fa-chevron-left"></i></a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?start=<?= urlencode($filter_start ?? '') ?>&end=<?= urlencode($filter_end ?? '') ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?start=<?= urlencode($filter_start ?? '') ?>&end=<?= urlencode($filter_end ?? '') ?>&page=<?= $page + 1 ?>"><i class="fas fa-chevron-right"></i></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hidden forms for delete & export -->
<form id="deleteForm" method="post" action="../include/delete.inc.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="delete_id" id="delete_id">
    <input type="hidden" name="start" value="<?= htmlspecialchars($filter_start ?? '') ?>">
    <input type="hidden" name="end" value="<?= htmlspecialchars($filter_end ?? '') ?>">
    <input type="hidden" name="page" value="<?= $page ?>">
</form>

<form id="exportForm" method="get" action="../include/download.inc.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="start" value="<?= htmlspecialchars($filter_start ?? '') ?>">
    <input type="hidden" name="end" value="<?= htmlspecialchars($filter_end ?? '') ?>">
</form>

<style>
    .pagination-premium .page-link {
        border: none;
        border-radius: 10px !important;
        margin: 0 2px;
        color: var(--secondary);
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .pagination-premium .page-item.active .page-link {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.02);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete-record');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (id) { confirmDelete(id); }
        });
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Record?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Delete',
        borderRadius: '20px'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteForm').submit();
        }
    });
}

function exportData() {
    document.getElementById('exportForm').submit();
}
</script>

<?php include_once "component/footer.php"; ?>
