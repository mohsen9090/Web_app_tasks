<?php session_start(); error_reporting(E_ALL); 
ini_set('display_errors', 1); if 
(!isset($_SESSION['user_uid'])) {
    die("You need to be logged in to view this 
    page.");
}
$servername = "gold24.io"; $username = 
"new_gigar"; $password = "new_chatgpt"; $dbname = 
"accounting_db"; $conn = new mysqli($servername, 
$username, $password, $dbname); if 
($conn->connect_error) {
    die("Connection failed: " . 
    $conn->connect_error);
}
// Edit task
if (isset($_POST['edit_task'])) { $task_id = 
    $_POST['task_id']; $task_description = 
    isset($_POST['task_description']) ? 
    trim($_POST['task_description']) : ''; 
    $priority = isset($_POST['priority']) ? 
    $_POST['priority'] : 0; $status = 
    isset($_POST['status']) ? $_POST['status'] : 
    ''; $due_date = isset($_POST['due_date']) ? 
    $_POST['due_date'] : ''; $is_alarm = 
    isset($_POST['is_alarm']) ? 1 : 0; $query = 
    "UPDATE tasks SET task_description = ?, 
    priority = ?, status = ?, due_date = ?, 
    is_alarm = ? WHERE id = ?"; $stmt = 
    $conn->prepare($query); if (!$stmt) {
        error_log("Error preparing SQL query: " . 
        $conn->error); die("<p style='color: 
        red;'>An error occurred while updating 
        the task. Please try again later.</p>");
    }
    $stmt->bind_param("sissii", 
    $task_description, $priority, $status, 
    $due_date, $is_alarm, $task_id); if 
    (!$stmt->execute()) {
        error_log("Error executing SQL query: " . 
        $stmt->error); die("<p style='color: 
        red;'>An error occurred while updating 
        the task. Please try again later.</p>");
    }
    header("Location: index.php"); exit();
}
$conn->close();
?>
