<?php require('fpdf.php'); session_start(); 
$user_uid = isset($_SESSION['user_uid']) ? 
$_SESSION['user_uid'] : null; if (!$user_uid) {
    die("User UID is not set in session.");
}
$api_url_get = 
'http://gold24.io:3006/api/tasks?uid=' . 
urlencode($user_uid); $ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $api_url_get); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
$response = curl_exec($ch); curl_close($ch); 
$data = json_decode($response, true); if (!$data 
|| !is_array($data)) {
    die("No tasks found or error retrieving data 
    from API.");
}
$pdf = new FPDF(); $pdf->AddPage(); 
$pdf->SetFont('Arial', 'B', 14); $pdf->Cell(0, 
10, 'Daily Tasks Report for User: ' . 
htmlspecialchars($user_uid), 0, 1, 'C'); 
$pdf->Ln(10); $pdf->SetFont('Arial', 'B', 12); 
$pdf->Cell(15, 10, 'ID', 1, 0, 'C'); 
$pdf->Cell(60, 10, 'Description', 1, 0, 'C'); 
$pdf->Cell(20, 10, 'Priority', 1, 0, 'C'); 
$pdf->Cell(30, 10, 'Status', 1, 0, 'C'); 
$pdf->Cell(50, 10, 'Created At', 1, 1, 'C'); 
$pdf->SetFont('Arial', '', 12); $hasTasks = 
false; foreach ($data as $task) {
    if (isset($task['uid']) && (int)$task['uid'] 
    === (int)$user_uid) {
        $hasTasks = true; $pdf->Cell(15, 10, 
        $task['id'], 1, 0, 'C'); $pdf->Cell(60, 
        10, $task['task_description'], 1, 0, 
        'C'); $pdf->Cell(20, 10, 
        $task['priority'], 1, 0, 'C'); 
        $pdf->Cell(30, 10, $task['status'], 1, 0, 
        'C'); $formatted_date = 
        isset($task['created_at']) ? date("Y-m-d 
        H:i:s", strtotime($task['created_at'])) : 
        'N/A'; $pdf->Cell(50, 10, 
        $formatted_date, 1, 1, 'C');
    }
}
if (!$hasTasks) { $pdf->Ln(10); 
    $pdf->SetFont('Arial', 'I', 12); 
    $pdf->Cell(0, 10, 'No tasks found for this 
    user.', 0, 1, 'C');
}
$pdf->Output('D', 'Daily_Tasks_Report.pdf');
exit;
