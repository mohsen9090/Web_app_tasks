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
$uid = $_SESSION['user_uid']; $search_keyword = 
""; if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search_task'])) { 
        $search_keyword = 
        trim($_POST['search_keyword']);
    }
    // Add new task
    if (isset($_POST['add_task'])) { 
        $task_description = 
        trim($_POST['task_description']); 
        $priority = $_POST['priority']; $due_date 
        = $_POST['due_date']; $is_alarm = 
        isset($_POST['is_alarm']) ? 1 : 0; 
        $is_highlighted = 
        isset($_POST['is_highlighted']) ? 1 : 0; 
        $query = "INSERT INTO tasks (uid, 
        task_description, priority, status, 
        due_date, is_alarm, is_highlighted, 
        created_at) VALUES (?, ?, ?, 'pending', 
        ?, ?, ?, NOW())"; $stmt = 
        $conn->prepare($query); if (!$stmt) {
            error_log("Error preparing SQL query: 
            " . $conn->error); die("<p 
            style='color: red;'>An error occurred 
            while adding the task. Please try 
            again later.</p>");
        }
        $stmt->bind_param("ssisii", $uid, 
        $task_description, $priority, $due_date, 
        $is_alarm, $is_highlighted); if 
        (!$stmt->execute()) {
            error_log("Error executing SQL query: 
            " . $stmt->error); die("<p 
            style='color: red;'>An error occurred 
            while adding the task. Please try 
            again later.</p>");
        }
        header("Location: " . 
        $_SERVER['PHP_SELF']); exit();
    }
    // Edit task
    if (isset($_POST['edit_task'])) { $task_id = 
        $_POST['task_id']; $task_description = 
        trim($_POST['task_description']); 
        $priority = $_POST['priority']; $status = 
        $_POST['status']; $due_date = 
        $_POST['due_date']; $is_alarm = 
        isset($_POST['is_alarm']) ? 1 : 0; 
        $is_highlighted = 
        isset($_POST['is_highlighted']) ? 1 : 0; 
        $query = "UPDATE tasks SET 
        task_description = ?, priority = ?, 
        status = ?, due_date = ?, is_alarm = ?, 
        is_highlighted = ? WHERE id = ?"; $stmt = 
        $conn->prepare($query); if (!$stmt) {
            error_log("Error preparing SQL query: 
            " . $conn->error); die("<p 
            style='color: red;'>An error occurred 
            while updating the task. Please try 
            again later.</p>");
        }
        $stmt->bind_param("sissiii", 
        $task_description, $priority, $status, 
        $due_date, $is_alarm, $is_highlighted, 
        $task_id); if (!$stmt->execute()) {
            error_log("Error executing SQL query: 
            " . $stmt->error); die("<p 
            style='color: red;'>An error occurred 
            while updating the task. Please try 
            again later.</p>");
        }
        header("Location: " . 
        $_SERVER['PHP_SELF']); exit();
    }
    // Delete task
    if (isset($_POST['delete_task'])) { $task_id 
        = $_POST['task_id']; $query = "DELETE 
        FROM tasks WHERE id = ?"; $stmt = 
        $conn->prepare($query); if (!$stmt) {
            error_log("Error preparing SQL query: 
            " . $conn->error); die("<p 
            style='color: red;'>An error occurred 
            while deleting the task. Please try 
            again later.</p>");
        }
        $stmt->bind_param("i", $task_id); if 
        (!$stmt->execute()) {
            error_log("Error executing SQL query: 
            " . $stmt->error); die("<p 
            style='color: red;'>An error occurred 
            while deleting the task. Please try 
            again later.</p>");
        }
        header("Location: " . 
        $_SERVER['PHP_SELF']); exit();
    }
    // Delete selected tasks
    if (isset($_POST['delete_selected_tasks'])) { 
        $task_ids = isset($_POST['task_ids']) ? 
        $_POST['task_ids'] : []; if 
        (!empty($task_ids)) {
            foreach ($task_ids as $task_id) { 
                $query = "DELETE FROM tasks WHERE 
                id = ?"; $stmt = 
                $conn->prepare($query); if 
                (!$stmt) {
                    error_log("Error preparing 
                    SQL query: " . $conn->error); 
                    die("<p style='color: 
                    red;'>An error occurred while 
                    deleting the tasks. Please 
                    try again later.</p>");
                }
                $stmt->bind_param("i", $task_id); 
                if (!$stmt->execute()) {
                    error_log("Error executing 
                    SQL query: " . $stmt->error); 
                    die("<p style='color: 
                    red;'>An error occurred while 
                    deleting the tasks. Please 
                    try again later.</p>");
                }
            }
            header("Location: " . 
            $_SERVER['PHP_SELF']); exit();
        }
    }
    // Remove all highlights
    if (isset($_POST['remove_all_highlights'])) { 
        $query = "UPDATE tasks SET is_highlighted 
        = 0 WHERE uid = ?"; $stmt = 
        $conn->prepare($query); if (!$stmt) {
            error_log("Error preparing SQL query: 
            " . $conn->error); die("<p 
            style='color: red;'>An error occurred 
            while removing all highlights. Please 
            try again later.</p>");
        }
        $stmt->bind_param("s", $uid); if 
        (!$stmt->execute()) {
            error_log("Error executing SQL query: 
            " . $stmt->error); die("<p 
            style='color: red;'>An error occurred 
            while removing all highlights. Please 
            try again later.</p>");
        }
        header("Location: " . 
        $_SERVER['PHP_SELF']); exit();
    }
    // Remove all alarms
    if (isset($_POST['remove_all_alarms'])) { 
        $query = "UPDATE tasks SET is_alarm = 0 
        WHERE uid = ?"; $stmt = 
        $conn->prepare($query); if (!$stmt) {
            error_log("Error preparing SQL query: 
            " . $conn->error); die("<p 
            style='color: red;'>An error occurred 
            while removing all alarms. Please try 
            again later.</p>");
        }
        $stmt->bind_param("s", $uid); if 
        (!$stmt->execute()) {
            error_log("Error executing SQL query: 
            " . $stmt->error); die("<p 
            style='color: red;'>An error occurred 
            while removing all alarms. Please try 
            again later.</p>");
        }
        header("Location: " . 
        $_SERVER['PHP_SELF']); exit();
    }
}
$tasks = loadTasks($conn, $uid, $search_keyword); 
$conn->close(); function loadTasks($conn, $uid, 
$search_keyword = "") {
    $tasks = []; $query = "SELECT * FROM tasks 
    WHERE uid = ?"; if (!empty($search_keyword)) 
    {
        $query .= " AND task_description LIKE ?";
    }
    $stmt = $conn->prepare($query); if 
    (!empty($search_keyword)) {
        $search_param = "%" . $search_keyword . 
        "%"; $stmt->bind_param("ss", $uid, 
        $search_param);
    } else {
        $stmt->bind_param("s", $uid);
    }
    if ($stmt->execute()) { $result = 
        $stmt->get_result(); $tasks = 
        $result->fetch_all(MYSQLI_ASSOC);
    } else {
        error_log("Error executing SQL query: " . 
        $stmt->error);
    }
    $stmt->close(); return $tasks;
}
?> <!DOCTYPE html> <html lang="fa"> <head> <meta 
    charset="UTF-8"> <title>Task 
    Management</title> <style>
        body { font-family: Arial, sans-serif; 
            margin: 0; padding: 0; 
            background-color: #f4f4f4;
        }
        .header-banner { background-color: 
            #007bff;
            color: white; padding: 20px; 
            text-align: center;
        }
        .task-form { padding: 20px; margin: 20px; 
            background-color: #ffffff; 
            border-radius: 5px; box-shadow: 0 2px 
            5px rgba(0, 0, 0, 0.1);
        }
        .edit-btn, .delete-btn, 
        .remove-alarm-btn, .logout-btn {
            background-color: #007bff; color: 
            #fff;
            border: none; padding: 8px 16px; 
            text-decoration: none; cursor: 
            pointer; border-radius: 4px; 
            transition: background-color 0.3s;
        }
        .edit-btn:hover, .delete-btn:hover, 
        .remove-alarm-btn:hover, 
        .logout-btn:hover {
            background-color: #0056b3;
        }
        .alarm-task { background-color: #ffcdd2; 
            font-weight: bold; animation: blink 
            1s infinite;
        }
        .highlighted-task { background-color: 
            #e0e0e0;
            font-weight: bold;
        }
        .normal-task { font-weight: normal;
        }
        @keyframes blink { 50% { 
                background-color: #e57373;
            }
        }
        table { width: 80%; margin: 20px auto; 
            border-collapse: collapse; 
            background-color: #ffffff;
        }
        th, td { padding: 12px; border: 1px solid 
            #ddd;
            text-align: center;
        }
        th { background-color: #007bff; color: 
            white;
        }
        .task-form input[type="text"], .task-form 
        textarea, .task-form 
        input[type="number"], .task-form select {
            border: 1px solid #888; width: 
            calc(100% - 22px); padding: 10px; 
            margin-bottom: 10px; border-radius: 
            4px; font-size: 16px;
        }
    </style> <link 
    href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" 
    rel="stylesheet" /> <script 
    src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script> 
    <script>
        function updateClock() { var now = new 
            Date(); var time = 
            now.toLocaleString('fa-IR', { 
            timeZone: 'Asia/Tehran', hour12: 
            false, hour: 'numeric', minute: 
            'numeric', second: 'numeric' }); 
            document.getElementById('clock').textContent 
            = time; setTimeout(updateClock, 
            1000);
        }
        window.onload = function() { 
            updateClock();
        };
    </script> </head> <body> <div 
    class="header-banner">
        <span class="clock" id="clock"></span> 
        Task Management
    </div> <!-- Search form --> <div 
    class="task-form">
        <form method="POST"> <input type="text" 
            name="search_keyword" 
            placeholder="Search keyword" 
            value="<?php echo 
            htmlspecialchars($search_keyword ?? 
            ''); ?>"> <button class="edit-btn" 
            type="submit" 
            name="search_task">Search</button>
        </form> </div> <h2 style="text-align: 
    center;">Your Tasks</h2> <form method="POST">
        <table> <tr> <th>Select</th> <th>ID</th> 
                <th>Task Description</th> 
                <th>Priority</th> <th>Status</th> 
                <th>Due Date</th> <th>Alarm</th> 
                <th>Highlight</th> <th>Created 
                At</th> <th>Actions</th>
            </tr> <?php if (!empty($tasks)): ?> 
                <?php foreach ($tasks as $task): 
                ?>
                    <tr class="<?php echo 
                    $task['is_alarm'] ? 
                    'alarm-task' : ''; ?> <?php 
                    echo $task['is_highlighted'] 
                    ? 'highlighted-task' : 
                    'normal-task'; ?>">
                        <td><input 
                        type="checkbox" 
                        name="task_ids[]" 
                        value="<?php echo 
                        htmlspecialchars($task['id']); 
                        ?>"></td> <td><?php echo 
                        htmlspecialchars($task['id']); 
                        ?></td> <td><?php echo 
                        htmlspecialchars($task['task_description']); 
                        ?></td> <td><?php echo 
                        htmlspecialchars($task['priority']); 
                        ?></td> <td><?php echo 
                        htmlspecialchars($task['status']); 
                        ?></td> <td><?php echo 
                        htmlspecialchars($task['due_date']); 
                        ?></td> <td><input 
                        type="checkbox" 
                        name="is_alarm" <?php 
                        echo $task['is_alarm'] ? 
                        'checked' : ''; ?>></td> 
                        <td><input 
                        type="checkbox" 
                        name="is_highlighted" 
                        <?php echo 
                        $task['is_highlighted'] ? 
                        'checked' : ''; ?>></td> 
                        <td><?php echo 
                        htmlspecialchars($task['created_at']); 
                        ?></td> <td>
                            <form method="POST" 
                            style="display:inline;">
                                <input 
                                type="hidden" 
                                name="task_id" 
                                value="<?php echo 
                                htmlspecialchars($task['id']); 
                                ?>"> <input 
                                type="text" 
                                name="task_description" 
                                value="<?php echo 
                                htmlspecialchars($task['task_description']); 
                                ?>" required> 
                                <input 
                                type="number" 
                                name="priority" 
                                value="<?php echo 
                                htmlspecialchars($task['priority']); 
                                ?>" required> 
                                <input 
                                type="date" 
                                name="due_date" 
                                value="<?php echo 
                                htmlspecialchars($task['due_date']); 
                                ?>"> <input 
                                type="checkbox" 
                                name="is_alarm" 
                                <?php echo 
                                $task['is_alarm'] 
                                ? 'checked' : ''; 
                                ?>> <input 
                                type="checkbox" 
                                name="is_highlighted" 
                                <?php echo 
                                $task['is_highlighted'] 
                                ? 'checked' : ''; 
                                ?>> <select 
                                name="status">
                                    <option 
                                    value="pending" 
                                    <?php if 
                                    ($task['status'] 
                                    == 'pending') 
                                    echo 
                                    'selected'; 
                                    ?>>Pending</option> 
                                    <option 
                                    value="in-progress" 
                                    <?php if 
                                    ($task['status'] 
                                    == 
                                    'in-progress') 
                                    echo 
                                    'selected'; 
                                    ?>>In 
                                    Progress</option> 
                                    <option 
                                    value="completed" 
                                    <?php if 
                                    ($task['status'] 
                                    == 
                                    'completed') 
                                    echo 
                                    'selected'; 
                                    ?>>Completed</option>
                                </select> <button 
                                class="edit-btn" 
                                type="submit" 
                                name="edit_task">Edit</button>
                            </form> <form 
 method="POST" style="display:inline;">
                                <input 
                                type="hidden" 
                                name="task_id" 
                                value="<?php echo 
                                htmlspecialchars($task['id']); 
                                ?>"> <button 
                                class="delete-btn" 
                                type="submit" 
                                name="delete_task">Delete</button>
                            </form> </td> </tr> 
                <?php endforeach; ?>
            <?php else: ?> <tr> <td 
                    colspan="10">No tasks 
                    found.</td>
                </tr> <?php endif; ?> </table> 
        <button type="submit" 
        name="delete_selected_tasks" 
        class="delete-btn">Delete 
        Selected</button> <button type="submit" 
        name="remove_all_highlights" 
        class="edit-btn">Remove All 
        Highlights</button> <button type="submit" 
        name="remove_all_alarms" 
        class="remove-alarm-btn">Remove All 
        Alarms</button>
    </form> <!-- Add new task form --> <div 
    class="task-form">
        <h2>Add New Task</h2> <form 
        method="POST">
            <textarea name="task_description" 
            rows="2" placeholder="Task 
            description" required></textarea><br> 
            <input type="number" name="priority" 
            placeholder="Priority (1-3)" 
            required> <input type="date" 
            name="due_date" placeholder="Due 
            Date" required> <input 
            type="checkbox" name="is_alarm"> Set 
            Alarm <input type="checkbox" 
            name="is_highlighted"> Highlight 
            <button class="edit-btn" 
            type="submit" name="add_task">Add 
            Task</button>
        </form> </div> <!-- Calendar --> <div 
    class="task-form">
        <h2>Calendar</h2> <div 
        id="calendar"></div> <script>
            document.addEventListener('DOMContentLoaded', 
            function() {
                var calendarEl = 
                document.getElementById('calendar'); 
                var calendar = new 
                FullCalendar.Calendar(calendarEl, 
                {
                    initialView: 'dayGridMonth', 
                    events: [
                        { title: 'Task 1', start: 
                        '2024-11-22' }, { title: 
                        'Task 2', start: 
                        '2024-11-23' }
                    ]
                });
                calendar.render();
            });
        </script> </div> <!-- Logout button --> 
    <div class="task-form">
        <form method="POST" action="logout.php"> 
            <button class="logout-btn" 
            type="submit" 
            name="logout">Logout</button>
        </form> </div> <!-- PDF download link --> 
    <div class="task-form">
        <a 
        href="https://code2024.net/generate_pdf.php" 
        target="_blank" class="button-link 
        edit-btn">Download PDF Report</a>
    </div> </body>
</html>
