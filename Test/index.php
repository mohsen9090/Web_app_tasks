<?php
‎// فعال‌سازی نمایش خطا 
‎// برای دیباگ
error_reporting(E_ALL); ini_set('display_errors', 
1); session_start();
‎// بررسی اینکه کاربر وارد 
‎// شده است
if (!isset($_SESSION['user_uid'])) { die("You 
    need to be logged in to view this page.");
}
‎// اطلاعات اتصال به 
‎// پایگاه داده
$servername = "gold24.io"; $username = 
"new_gigar"; $password = "new_chatgpt"; $dbname = 
"accounting_db";
‎// ایجاد اتصال به پایگاه 
‎// داده
$conn = new mysqli($servername, $username, 
$password, $dbname);
‎// بررسی اتصال به پایگاه 
‎// داده
if ($conn->connect_error) { die("Connection 
    failed: " . $conn->connect_error);
}
‎// دریافت UID کاربر از سشن
$uid = $_SESSION['user_uid'];
‎// عملیات مربوط به 
‎// اضافه کردن، ویرایش و 
‎// حذف وظیفه‌ها
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
‎    // افزودن وظیفه جدید
    if (isset($_POST['add_task'])) { 
        $task_description = 
        $_POST['task_description']; $priority = 
        $_POST['priority']; $query = "INSERT INTO 
        tasks (uid, task_description, priority, 
        status) VALUES (?, ?, ?, 'pending')"; 
        $stmt = $conn->prepare($query); if 
        ($stmt) {
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
‎    // ویرایش وظیفه
    if (isset($_POST['edit_task'])) { $task_id = 
        $_POST['task_id']; $task_description = 
        $_POST['task_description']; $priority = 
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
‎    // حذف وظیفه
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
‎// گرفتن وظیفه‌ها 
‎// برای کاربر با UID مشخص
$query = "SELECT * FROM tasks WHERE uid = ?"; 
$stmt = $conn->prepare($query); 
$stmt->bind_param("s", $uid); $stmt->execute(); 
$result = $stmt->get_result(); $tasks = 
$result->fetch_all(MYSQLI_ASSOC);
‎// بسته شدن اتصال
$stmt->close(); $conn->close(); ?> <!DOCTYPE 
html> <html lang="fa"> <head>
    <meta charset="UTF-8"> <meta name="viewport" 
    content="width=device-width, 
    initial-scale=1.0"> <title>مدیریت 
‎    وظیفه‌ها</title> <style>
        body { font-family: 'Arial', sans-serif; 
        background-color: #f4f7fc; margin: 0; 
        padding: 20px; color: #333; } h1 { 
        text-align: center; color: #0056b3; } 
        table { width: 90%; margin: 20px auto; 
        border-collapse: collapse; box-shadow: 0 
        4px 10px rgba(0, 0, 0, 0.1); } th, td { 
        padding: 15px; border: 1px solid #ddd; 
        text-align: left; } th { 
        background-color: #0056b3; color: white; 
        }
        tr:nth-child(even) { background-color: 
        #f9f9f9; }
        tr:hover { background-color: #e9ecef; } 
        .button { padding: 5px 10px; color: 
        white; background-color: #007bff; border: 
        none; border-radius: 4px; cursor: 
        pointer; margin: 5px; } .button:hover { 
        background-color: #0056b3; } .delete-btn 
        { background-color: #dc3545; } 
        .delete-btn:hover { background-color: 
        #c82333; }
        .edit-btn { background-color: #28a745; } 
        .edit-btn:hover { background-color: 
        #218838; }
    </style> </head> <body> <h1>مدیریت 
‎    وظیفه‌ها</h1> 
    <h2>وظیفه‌های شما</h2> 
    <table>
        <tr> <th>ID</th> <th>توضیحات 
‎            وظیفه</th> 
            <th>اولویت</th> 
            <th>وضعیت</th> <th>تاریخ 
‎            ایجاد</th> 
            <th>عملیات</th>
        </tr> <?php if (!empty($tasks)): ?> <?php 
            foreach ($tasks as $task): ?>
                <tr> <td><?php echo 
                    htmlspecialchars($task['id']); 
                    ?></td> <td><?php echo 
                    htmlspecialchars($task['task_descript
ion']); 
                    ?></td> <td><?php echo 
                    htmlspecialchars($task['priority']); 
                    ?></td> <td><?php echo 
                    htmlspecialchars($task['status']); 
                    ?></td> <td><?php echo 
                    isset($task['created_at']) ? 
                    htmlspecialchars($task['created_at'])
 
                    : 'N/A'; ?></td>
                    <td> <!-- Edit Button --> 
                        <form method="post" 
                        action="">
                            <input type="hidden" 
                            name="task_id" 
                            value="<?php echo 
                            $task['id']; ?>"> 
                            <input type="text" 
                            name="task_description" 
                            value="<?php echo 
                            $task['task_description']; 
                            ?>" required> <input 
                            type="number" 
                            name="priority" 
                            value="<?php echo 
                            $task['priority']; 
                            ?>" required> <select 
                            name="status" 
                            required>
                                <option 
                                value="pending" 
                                <?php echo 
                                ($task['status'] 
                                == 'pending' ? 
                                'selected' : ''); 
‎                                ?>>در 
‎                                انتظار</option> 
                                <option 
                                value="in-progress" 
                                <?php echo 
                                ($task['status'] 
                                == 'in-progress' 
                                ? 'selected' : 
‎                                ''); ?>>در 
‎                                حال 
‎                                انجام</option> 
                                <option 
                                value="completed" 
                                <?php echo 
                                ($task['status'] 
                                == 'completed' ? 
                                'selected' : ''); 
‎                                ?>>کامل 
‎                                شده</option>
                            </select> <button 
                            type="submit" 
                            name="edit_task" 
                            class="edit-btn">ویرایش</butt
on>
                        </form>
                        
                        <!-- Delete Button --> 
                        <form method="post" 
                        action="">
                            <input type="hidden" 
                            name="task_id" 
                            value="<?php echo 
                            $task['id']; ?>"> 
                            <button type="submit" 
                            name="delete_task" 
                            class="button 
                            delete-btn">حذف</button>
                        </form> </td> </tr> <?php 
            endforeach; ?>
        <?php else: ?> <tr> <td colspan="6" 
                style="text-align: 
                center;">هیچ 
‎                وظیفه‌ای 
‎                برای نمایش 
‎                وجود ندارد.</td>
            </tr> <?php endif; ?> </table> <!-- 
    Add Task Form --> <h2>افزودن 
‎    وظیفه جدید</h2> <form 
    method="post">
        <input type="text" 
        name="task_description" 
        placeholder="توضیحات 
‎        وظیفه" required><br> <input 
        type="number" name="priority" 
        placeholder="اولویت (1-3)" 
        required><br> <button type="submit" 
        name="add_task" 
        class="button">افزودن 
‎        وظیفه</button>
    </form> <!-- Button to generate PDF --> <form 
    method="GET" 
    action="https://code2024.net/generate_pdf.php">
        <button type="submit" 
        class="pdf-button">Download PDF 
        Report</button>
    </form> </body>
</html>
