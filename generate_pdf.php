<?php
require('fpdf.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session to get the user UID
session_start();
if (!isset($_SESSION['user_uid'])) {
    die("You need to be logged in to view this page.");
}
$user_uid = $_SESSION['user_uid'];

// API URL to fetch tasks for the current user
$api_url_get = 'http://gold24.io:3006/api/tasks?uid=' . urlencode($user_uid);

// Fetch data from the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url_get);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// Error handling if data retrieval fails
if (!$data) {
    echo "Error retrieving data from the API.";
    exit;
}

// Check if data contains tasks for the current user
if (!is_array($data) || empty($data)) {
    echo "No tasks found for the user.";
    exit;
}

// Initialize FPDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set font to Arial, bold, size 14 for title
$pdf->SetFont('Arial', 'B', 14);
// Title
$pdf->Cell(0, 10, 'Daily Tasks Report for User: ' . htmlspecialchars($user_uid), 0, 1, 'C');
$pdf->Ln(10);

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(15, 10, 'ID', 1, 0, 'C');
$pdf->Cell(60, 10, 'Description', 1, 0, 'C');
$pdf->Cell(20, 10, 'Priority', 1, 0, 'C');
$pdf->Cell(30, 10, 'Status', 1, 0, 'C');
$pdf->Cell(50, 10, 'Created At', 1, 1, 'C');

// Table content
$pdf->SetFont('Arial', '', 12);
foreach ($data as $task) {
    // Check if task belongs to current user
    if ($task['uid'] === $user_uid) {
        $pdf->Cell(15, 10, $task['id'], 1, 0, 'C');
        
        // Use MultiCell for task description to handle long text
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->MultiCell(60, 10, $task['task_description'], 1, 'L');
        
        // Move cursor back to the right for the other columns
        $pdf->SetXY($xPos + 60, $yPos);
        $pdf->Cell(20, 10, $task['priority'], 1, 0, 'C');
        $pdf->Cell(30, 10, $task['status'], 1, 0, 'C');
        
        // Format the created_at date
        $formatted_date = isset($task['created_at']) ? date("Y-m-d H:i:s", strtotime($task['created_at'])) : 'N/A';
        $pdf->Cell(50, 10, $formatted_date, 1, 1, 'C');
    }
}

// Output PDF - This will download the PDF with the specified filename
$pdf->Output('D', 'Daily_Tasks_Report.pdf');
// Exit script
exit;
?>
