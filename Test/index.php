<?php
// فعال‌سازی نمایش خطا 
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
// دریافت UID کاربر از سشن
$uid = $_SESSION['user_uid'];
// عملیات مربوط به 
// اضافه کردن، ویرایش و 
// حذف وظایف
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // بررسی تعداد خطوط 
    // توضیحات وظیفه
    if (isset($_POST['task_description'])) { 
        $task_description = 
        $_POST['task_description'];
        
        // چک کردن تعداد خطوط 
        // برای محدودیت به 
        // دو خط
        $line_count = 
        substr_count($task_description, "\n") + 
        1; if ($line_count > 2) {
            die("توضیحات وظیفه 
            نباید بیشتر از دو 
            خط باشد.");
        }
    }
    // افزودن وظیفه جدید
    if (isset($_POST['add_task'])) { $priority = 
        $_POST['priority']; $query = "INSERT INTO 
        tasks (uid, task_description, priority, 
        status, created_at) VALUES (?, ?, ?, 
        'pending', NOW())"; $stmt = 
        $conn->prepare($query); if ($stmt) {
            $stmt->bind_param("ssi", $uid, 
            $task_description, $priority); 
            $stmt->execute(); if ($stmt->error) {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . 
            $conn->error;
        }
    }
    
    // ویرایش وظیفه
    if (isset($_POST['edit_task'])) { $task_id = 
        $_POST['task_id']; $priority = 
        $_POST['priority']; $status = 
        $_POST['status']; $query = "UPDATE tasks 
        SET task_description = ?, priority = ?, 
        status = ? WHERE id = ?"; $stmt = 
        $conn->prepare($query); if ($stmt) {
            $stmt->bind_param("sisi", 
            $task_description, $priority, 
            $status, $task_id); $stmt->execute(); 
            if ($stmt->error) {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . 
            $conn->error;
        }
    }
    
    // حذف وظیفه
    if (isset($_POST['delete_task'])) { $task_id 
        = $_POST['task_id']; $query = "DELETE 
        FROM tasks WHERE id = ?"; $stmt = 
        $conn->prepare($query); if ($stmt) {
            $stmt->bind_param("i", $task_id); 
            $stmt->execute(); if ($stmt->error) {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . 
            $conn->error;
        }
    }
}
// گرفتن وظیفه‌ها 
// برای کاربر با UID مشخص
$query = "SELECT * FROM tasks WHERE uid = ?"; 
$stmt = $conn->prepare($query); 
$stmt->bind_param("s", $uid); $stmt->execute(); 
$result = $stmt->get_result(); $tasks = 
$result->fetch_all(MYSQLI_ASSOC);
// بسته شدن اتصال
$stmt->close(); $conn->close(); ?> <!DOCTYPE 
html> <html lang="fa"> <head>
    <meta charset="UTF-8"> <meta name="viewport" 
    content="width=device-width, 
    initial-scale=1.0"> <title>مدیریت 
    وظیفه‌ها</title> <style>
        /* استایل‌های CSS */ 
        body {
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; color: 
            #333;
        }
        .header-banner { background: #007bff; 
            color: white; padding: 10px; 
            text-align: center;
        }
        table { width: 100%; border-collapse: 
            collapse; margin: 20px 0;
        }
        th, td { padding: 10px; border: 1px solid 
            #ddd;
            text-align: center;
        }
        th { background-color: #007bff; color: 
            white;
        }
        .task-form { margin: 20px 0;
        }
        .button, .logout-btn { padding: 10px 
            15px; margin: 5px; border: none; 
            cursor: pointer; background-color: 
            #28a745;
            color: white;
        }
        .logout-btn { background-color: #dc3545;
        }
    </style> </head> <body> <div 
    class="header-banner">مدیریت 
    وظیفه‌ها</div> 
    <h2>وظیفه‌های شما</h2> 
    <table>
        <tr> <th>ID</th> <th>توضیحات 
            وظیفه</th> 
            <th>اولویت</th> 
            <th>وضعیت</th> <th>تاریخ 
            ایجاد</th> 
            <th>عملیات</th>
        </tr> <?php if (!empty($tasks)): ?> <?php 
            foreach ($tasks as $task): ?>
                <tr> <td><?php echo 
                    htmlspecialchars($task['id']); 
                    ?></td> <td><?php echo 
                    htmlspecialchars($task['task_description']); 
                    ?></td> <td><?php echo 
                    htmlspecialchars($task['priority']); 
                    ?></td> <td><?php echo 
                    htmlspecialchars($task['status']); 
                    ?></td> <td><?php echo 
                    isset($task['created_at']) ? 
                    htmlspecialchars($task['created_at']) 
                    : 'N/A'; ?></td>
                    <td> <!-- فرم‌ها 
                        برای ویرایش 
                        و حذف --> <form 
                        method="POST" 
                        style="display:inline;">
                            <input type="hidden" 
                            name="task_id" 
                            value="<?php echo 
                            htmlspecialchars($task['id']); 
                            ?>"> <button 
                            type="submit" 
                            name="delete_task" 
                            class="button">حذف</button>
                        </form> <form 
                        method="POST" 
                        style="display:inline;">
                            <input type="hidden" 
                            name="task_id" 
                            value="<?php echo 
                            htmlspecialchars($task['id']); 
                            ?>"> <input 
                            type="text" 
                            name="task_description" 
                            placeholder="توضیحات 
                            جدید" required> 
                            <input type="number" 
                            name="priority" 
                            placeholder="اولویت" 
                            required> <select 
                            name="status">
                                <option 
                                value="pending">در 
                                حال 
                                انتظار</option> 
                                <option 
                                value="in-progress">در 
                                حال 
                                انجام</option> 
                                <option 
                                value="completed">انجام 
                                شده</option>
                            </select> <button 
                            type="submit" 
                            name="edit_task" 
                            class="button">ویرایش</button>
                        </form> </td> </tr> <?php 
            endforeach; ?>
        <?php else: ?> <tr> <td colspan="6" 
                style="text-align: 
                center;">هیچ 
                وظیفه‌ای 
                برای نمایش 
                وجود ندارد.</td>
            </tr> <?php endif; ?> </table> 
    <h2>افزودن وظیفه 
    جدید</h2> <form method="post" 
    class="task-form">
        <textarea name="task_description" 
        placeholder="توضیحات 
        وظیفه" required></textarea><br> 
        <input type="number" name="priority" 
        placeholder="اولویت (1-3)" 
        required><br> <button type="submit" 
        name="add_task" 
        class="button">افزودن 
        وظیفه</button>
    </form> <form method="POST" 
    action="logout.php" style="text-align: 
    right;">
        <button type="submit" 
        class="logout-btn">خروج از 
        سیستم</button>
    </form> <form method="GET" 
    action="https://code2024.net/generate_pdf.php">
        <button type="submit" 
        class="button">دانلود گزارش 
        PDF</button>
    </form> </body>
</html>
