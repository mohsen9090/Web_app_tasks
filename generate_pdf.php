<?php 
require('fpdf.php');

// Enable error reporting for debugging
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

// API URL to fetch tasks
$api_url_get = 'http://gold24.io:3006/api/tasks';

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

// Initialize FPDF instance
$pdf = new FPDF(); 
$pdf->AddPage();

// Set font to Arial, bold, size 14 for title
$pdf->SetFont('Arial', 'B', 14);

// Title
$pdf->Cell(0, 10, 'Daily Tasks Report', 0, 1, 'C'); 
$pdf->Ln(10);

// Table header
$pdf->SetFont('Arial', 'B', 12); 
$pdf->Cell(10, 10, 'ID', 1); 
$pdf->Cell(60, 10, 'Description', 1); 
$pdf->Cell(20, 10, 'Priority', 1); 
$pdf->Cell(30, 10, 'Status', 1); 
$pdf->Cell(40, 10, 'Created At', 1); 
$pdf->Ln();

// Table content
$pdf->SetFont('Arial', '', 12); 
foreach ($data as $task) {
    $pdf->Cell(10, 10, $task['id'], 1); 
    $pdf->Cell(60, 10, $task['task_description'], 1); 
    $pdf->Cell(20, 10, $task['priority'], 1); 
    $pdf->Cell(30, 10, $task['status'], 1); 

    // Format the created_at date
    $formatted_date = isset($task['created_at']) ? date("Y-m-d H:i:s", strtotime($task['created_at'])) : 'N/A';
    $pdf->Cell(40, 10, $formatted_date, 1); 

    $pdf->Ln();
}

// Output PDF - This will download the PDF with the specified filename
$pdf->Output('D', 'Daily_Tasks_Report.pdf');

// Exit script
exit;
?>
