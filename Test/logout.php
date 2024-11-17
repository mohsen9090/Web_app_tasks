<?php
// فعال کردن نمایش خطاها 
// برای دیباگ
error_reporting(E_ALL); ini_set('display_errors', 
1); session_start();
// بررسی اینکه کاربر وارد 
// شده است یا خیر
if (!isset($_SESSION['user_uid'])) { die("You 
    need to be logged in to view this page.");
}
// اطلاعات اتصال به 
// پایگاه داده
$servername = "gold24.io"; $username = 
"new_gigar"; $password = "new_chatgpt"; $dbname = 
"accounting_db";
// ایجاد اتصال به پایگاه 
// داده
$conn = new mysqli($servername, $username, 
$password, $dbname);
// بررسی اتصال به پایگاه 
// داده
if ($conn->connect_error) { die("Connection 
    failed: " . $conn->connect_error);
}
// خالی کردن متغیرهای 
// نشست
$_SESSION = array();
// نابود کردن نشست
session_destroy();
// بستن اتصال به پایگاه 
// داده
$conn->close();
// هدایت به صفحه اصلی 
// سایت
header("Location: https://code2024.net"); exit;
?>
