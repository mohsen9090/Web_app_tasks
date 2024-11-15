<?php
// اتصال به پایگاه داده
$servername = "localhost"; $username = 
"new_user"; // نام کاربری 
دیتابیس خود را وارد 
کنید $password = "new_password"; // 
پسورد دیتابیس خود را 
وارد کنید $dbname = "accounting_db"; 
// نام دیتابیس خود را 
وارد کنید
// ایجاد اتصال
$conn = new mysqli($servername, $username, 
$password, $dbname);
// بررسی اتصال
if ($conn->connect_error) { die("Connection 
    failed: " . $conn->connect_error);
}
// دریافت ID تسک از URL
$task_id = $_GET['id'];
// حذف تسک از دیتابیس
$sql = "DELETE FROM daily_tasks WHERE id = 
$task_id"; if ($conn->query($sql) === TRUE) {
    echo "Task deleted successfully";
} else {
    echo "Error: " . $sql . "<br>" . 
    $conn->error;
}
// بستن اتصال
$conn->close();
?>
