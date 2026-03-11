<?php
/**
 * Excel Export Handler
 * Exports breakdown records to Excel file
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

// Validate CSRF Token (passed as GET parameter for download links)
$csrfToken = $_GET['csrf_token'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    header("Location: ../page/dataHistory.php?error=InvalidRequest");
    exit;
}

ob_start();

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

$query = "SELECT * FROM breakdowns";
$params = [];
$types = "";

if ($start && $end) {
    $query .= " WHERE inform_date BETWEEN ? AND ?";
    $params = [$start, $end];
    $types = "ss";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Define headers
$headers = ['Inform Date', 'Inform Time', 'Unit No', 'Nature', 'Description', 'Attended Date', 'Attended Time', 'Team', 'Submit Date'];

// Set headers
$sheet->fromArray($headers, NULL, 'A1');

// Apply bold styling to headers
$headerStyle = $sheet->getStyle('A1:I1');
$headerStyle->getFont()->setBold(true)->setSize(12);

// Auto-size columns
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Freeze header row
$sheet->freezePane('A2');

// Add data rows
$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    $dataRow = [
        $row['inform_date'] ?? '',
        $row['inform_time'] ?? '',
        $row['unit_no'] ?? '',
        $row['nature_of_breakdown'] ?? '',
        $row['work_description'] ?? '',
        $row['attendent_date'] ?? '',
        $row['attended_time'] ?? '',
        $row['team'] ?? '',
        $row['submit_date'] ?? ''
    ];
    $sheet->fromArray($dataRow, NULL, "A$rowIndex");
    $rowIndex++;
}

$stmt->close();

// Set filename
$startFormatted = $start ? date('Y-m-d', strtotime($start)) : 'All';
$endFormatted = $end ? date('Y-m-d', strtotime($end)) : 'All';
$filename = "Breakdowns_Report_{$startFormatted}_to_{$endFormatted}.xlsx";

// Output headers
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1'); // IE 9
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // Always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

ob_end_clean();

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;