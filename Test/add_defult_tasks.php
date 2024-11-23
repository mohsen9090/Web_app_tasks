<?php session_start(); error_reporting(E_ALL); 
ini_set('display_errors', 1);
// بررسی اینکه کاربر 
// لاگین کرده باشد
if (!isset($_SESSION['user_uid'])) { die("You 
    need to be logged in to view this page.");
}
$servername = "gold24.io"; $username = 
"new_gigar"; $password = "new_chatgpt"; $dbname = 
"accounting_db"; $conn = new mysqli($servername, 
$username, $password, $dbname); if 
($conn->connect_error) {
    die("Connection failed: " . 
    $conn->connect_error);
}
$uid = $_SESSION['user_uid']; if 
($_SERVER['REQUEST_METHOD'] === 'POST') {
    // افزودن تسک‌های 
    // پیش‌فرض
    $default_tasks = [ "Home work", "Meeting with 
        friends", "Go doctor", "Go shopping", "Do 
        exercise", "Clean home", "See TV", 
        "Listen to music", "Prepare 
        presentation", "Write report"
    ];
    
    $priority = 1; // اولویت 
    پیش‌فرض foreach ($default_tasks 
    as $task_description) {
        $query = "SELECT id FROM tasks WHERE uid 
        = ? AND task_description = ?"; $stmt = 
        $conn->prepare($query); 
        $stmt->bind_param("ss", $uid, 
        $task_description); $stmt->execute(); 
        $stmt->store_result(); if 
        ($stmt->num_rows == 0) {
            $query = "INSERT INTO tasks (uid, 
            task_description, priority, status, 
            created_at) VALUES (?, ?, ?, 
            'pending', NOW())"; $stmt = 
            $conn->prepare($query); 
            $stmt->bind_param("ssi", $uid, 
            $task_description, $priority); 
            $stmt->execute();
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}
$conn->close();
?>
