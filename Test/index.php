<?php
// فعال‌سازی نمایش خطا برای دیباگ
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// بررسی لاگین بودن کاربر
if (!isset($_SESSION['user_uid'])) {
    die("You need to be logged in to view this page.");
}

// اطلاعات اتصال به دیتابیس
$servername = "gold24.io";
$username = "new_gigar";
$password = "new_chatgpt";
$dbname = "accounting_db";

// اتصال به پایگاه داده
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// دریافت UID کاربر
$uid = $_SESSION['user_uid'];
$search_keyword = "";
$tasks = [];

// بارگذاری وظایف از پایگاه داده
function loadTasks($conn, $uid, $search_keyword = "") {
    $tasks = [];
    if (!empty($search_keyword)) {
        $query = "SELECT * FROM tasks WHERE uid = ? AND task_description LIKE ?";
        $stmt = $conn->prepare($query);
        $search_param = "%" . $search_keyword . "%";
        $stmt->bind_param("ss", $uid, $search_param);
    } else {
        $query = "SELECT * FROM tasks WHERE uid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $uid);
    }

    if ($stmt && $stmt->execute()) {
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
    }

    if ($stmt) {
        $stmt->close();
    }

    return $tasks;
}

// مدیریت درخواست POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // دریافت مقدار جستجو
    if (isset($_POST['search_task'])) {
        $search_keyword = trim($_POST['search_keyword']);
    }

    // عملیات افزودن وظیفه
    if (isset($_POST['add_task'])) {
        $task_description = trim($_POST['task_description']);
        $priority = $_POST['priority'];

        if (substr_count($task_description, "\n") > 1) {
            die("<p style='color: red;'>توضیحات نباید بیش از دو خط باشد.</p>");
        }

        $query = "INSERT INTO tasks (uid, task_description, priority, status, created_at) VALUES (?, ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $uid, $task_description, $priority);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // عملیات ویرایش وظیفه
    if (isset($_POST['edit_task'])) {
        $task_id = $_POST['task_id'];
        $task_description = trim($_POST['task_description']);
        $priority = $_POST['priority'];
        $status = $_POST['status'];

        $query = "UPDATE tasks SET task_description = ?, priority = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisi", $task_description, $priority, $status, $task_id);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // عملیات حذف وظیفه
    if (isset($_POST['delete_task'])) {
        $task_id = $_POST['task_id'];
        $query = "DELETE FROM tasks WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// بارگذاری وظایف
$tasks = loadTasks($conn, $uid, $search_keyword);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>مدیریت وظایف</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        .header-banner { background: #007bff; color: white; padding: 10px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #007bff; color: white; }
        .highlight { background-color: yellow; font-weight: bold; }
        .task-form { margin: 20px 0; }
        .button, .logout-btn { padding: 10px 15px; margin: 5px; border: none; cursor: pointer; background-color: #28a745; color: white; }
        .logout-btn { background-color: #dc3545; }
    </style>
</head>
<body>
<div class="header-banner">مدیریت وظیفه‌ها</div>

<!-- فرم جستجو -->
<form method="POST" class="task-form">
    <input type="text" name="search_keyword" placeholder="عبارت جستجو" value="<?php echo htmlspecialchars($search_keyword); ?>">
    <button type="submit" name="search_task">جستجو</button>
</form>

<h2>وظیفه‌های شما</h2>
<table>
    <tr>
        <th>ID</th>
        <th>توضیحات وظیفه</th>
        <th>اولویت</th>
        <th>وضعیت</th>
        <th>تاریخ ایجاد</th>
        <th>عملیات</th>
    </tr>
    <?php if (!empty($tasks)): ?>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?php echo htmlspecialchars($task['id']); ?></td>
                <td>
                    <?php
                    // استفاده از preg_replace برای هایلایت
                    if (!empty($search_keyword)) {
                        $highlighted = preg_replace(
                            "/(" . preg_quote($search_keyword, '/') . ")/i",
                            "<span class='highlight'>$1</span>",
                            htmlspecialchars($task['task_description'])
                        );
                        echo $highlighted;
                    } else {
                        echo htmlspecialchars($task['task_description']);
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($task['priority']); ?></td>
                <td><?php echo htmlspecialchars($task['status']); ?></td>
                <td><?php echo htmlspecialchars($task['created_at']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                        <input type="text" name="task_description" placeholder="توضیحات جدید" required>
                        <input type="number" name="priority" placeholder="اولویت" required>
                        <select name="status">
                            <option value="pending">در حال انتظار</option>
                            <option value="in-progress">در حال انجام</option>
                            <option value="completed">انجام شده</option>
                        </select>
                        <button type="submit" name="edit_task">ویرایش</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                        <button type="submit" name="delete_task">حذف</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">هیچ وظیفه‌ای یافت نشد. <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="button">بازگشت به لیست وظایف</a></td>
        </tr>
    <?php endif; ?>
</table>

<h2>افزودن وظیفه جدید</h2>
<form method="POST" class="task-form">
    <textarea name="task_description" placeholder="توضیحات وظیفه" required></textarea><br>
    <input type="number" name="priority" placeholder="اولویت (1-3)" required><br>
    <button type="submit" name="add_task">افزودن وظیفه</button>
</form>

<!-- دکمه خروج از سیستم و دانلود PDF -->
<form method="POST" action="logout.php" style="text-align: right; margin-top: 20px;">
    <button type="submit" class="logout-btn">خروج از سیستم</button>
</form>
<form method="GET" action="https://code2024.net/generate_pdf.php" style="text-align: right; margin-top: 10px;">
    <button type="submit" class="button">دانلود گزارش PDF</button>
</form>

</body>
</html>
