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
    // حذف گروهی وظایف
    if (isset($_POST['task_ids'])) { $task_ids = 
        $_POST['task_ids']; if 
        (!empty($task_ids)) {
            foreach ($task_ids as $task_id) { 
                $query = "DELETE FROM tasks WHERE 
                id = ? AND uid = ?"; $stmt = 
                $conn->prepare($query); 
                $stmt->bind_param("is", $task_id, 
                $uid); $stmt->execute();
            }
            header("Location: " . 
            $_SERVER['PHP_SELF']); exit();
        }
    }
}
$conn->close();
?>
