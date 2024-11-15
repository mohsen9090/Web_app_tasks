<?php error_reporting(E_ALL); 
ini_set('display_errors', 1); session_start();
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
$errorMessage = ''; $successMessage = '';
// بررسی ارسال فرم
if ($_SERVER['REQUEST_METHOD'] == 'POST') { if 
    (isset($_POST['action']) && $_POST['action'] 
    == 'register') {
        $name = $_POST['name']; $email = 
        $_POST['email']; $password = 
        $_POST['password']; $confirmPassword = 
        $_POST['confirm_password']; $uid = 
        $_POST['uid'];
        // بررسی تطابق 
        // پسوردها
        if ($password !== $confirmPassword) { 
            $errorMessage = "Passwords do not 
            match!";
        } else {
            // هش کردن رمز عبور
            $hashed_password = 
            password_hash($password, 
            PASSWORD_DEFAULT);
            // بررسی وجود 
            // ایمیل در جدول 
            // users
            $stmt = $conn->prepare("SELECT id 
            FROM users WHERE email = ?"); 
            $stmt->bind_param("s", $email); 
            $stmt->execute(); 
            $stmt->store_result(); if 
            ($stmt->num_rows > 0) {
                $errorMessage = "This email is 
                already registered.";
            } else {
                // بررسی وجود UID 
                // در جدول users
                $stmt = $conn->prepare("SELECT id 
                FROM users WHERE uid = ?"); 
                $stmt->bind_param("s", $uid); 
                $stmt->execute(); 
                $stmt->store_result(); if 
                ($stmt->num_rows > 0) {
                    $errorMessage = "This UID is 
                    already taken. Please choose 
                    another one.";
                } else {
                    // ثبت اطلاعات 
                    // در جدول users
                    $stmt = 
                    $conn->prepare("INSERT INTO 
                    users (name, email, password, 
                    uid) VALUES (?, ?, ?, ?)"); 
                    $stmt->bind_param("ssss", 
                    $name, $email, 
                    $hashed_password, $uid); if 
                    ($stmt->execute()) {
                        $successMessage = 
                        "Registration successful! 
                        Please log in to 
                        continue.";
                        // ثبت اطلاعات 
                        // در جدول 
                        // user_authentication
                        $stmt = 
                        $conn->prepare("INSERT 
                        INTO user_authentication 
                        (name, email, password, 
                        uid) VALUES (?, ?, ?, 
                        ?)"); 
                        $stmt->bind_param("ssss", 
                        $name, $email, 
                        $hashed_password, $uid); 
                        $stmt->execute();
                    } else {
                        $errorMessage = "Error: " 
                        . $stmt->error;
                    }
                }
            }
            $stmt->close();
        }
    }
    // ورود
    if (isset($_POST['action']) && 
    $_POST['action'] == 'login') {
        $email = $_POST['email']; $password = 
        $_POST['password'];
        // بررسی ایمیل و رمز 
        // عبور
        $stmt = $conn->prepare("SELECT id, name, 
        password, uid FROM users WHERE email = 
        ?"); $stmt->bind_param("s", $email); 
        $stmt->execute(); $stmt->store_result(); 
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, 
            $hashed_password, $uid); 
            $stmt->fetch();
            // بررسی رمز عبور
            if (password_verify($password, 
            $hashed_password)) {
                $_SESSION['user_id'] = $id; 
                $_SESSION['user_name'] = $name; 
                $_SESSION['user_uid'] = $uid; 
                $successMessage = "Login 
                successful! Redirecting to your 
                dashboard...";
            } else {
                $errorMessage = "Invalid 
                password.";
            }
        } else {
            $errorMessage = "No user found with 
            this email.";
        }
        $stmt->close();
    }
}
$conn->close(); ?> <!DOCTYPE html> <html 
lang="en"> <head>
    <meta charset="UTF-8"> <meta name="viewport" 
    content="width=device-width, 
    initial-scale=1.0"> <title>Login / 
    Register</title> <style>
        body { font-family: Arial, sans-serif; } 
        .error { color: red; } .success { color: 
        green; } .form-container { margin-bottom: 
        20px; }
    </style> </head> <body> <div 
    class="container">
        <h1>Login / Register</h1> <!-- فرم 
        ورود --> <div 
        class="form-container">
            <h2>Login</h2> <form method="POST" 
            action="index.php">
                <input type="hidden" 
                name="action" value="login"> <div 
                class="form-group">
                    <label 
                    for="email">Email:</label> 
                    <input type="email" 
                    name="email" id="email" 
                    required>
                </div> <div class="form-group"> 
                    <label 
                    for="password">Password:</label> 
                    <input type="password" 
                    name="password" id="password" 
                    required>
                </div> <div class="form-group"> 
                    <button 
                    type="submit">Login</button>
                </div> </form> </div> <!-- 
        فرم ثبت‌نام --> <div 
        class="form-container">
            <h2>Register</h2> <form method="POST" 
            action="index.php">
                <input type="hidden" 
                name="action" value="register"> 
                <div class="form-group">
                    <label 
                    for="name">Name:</label> 
                    <input type="text" 
                    name="name" id="name" 
                    required>
                </div> <div class="form-group"> 
                    <label 
                    for="email">Email:</label> 
                    <input type="email" 
                    name="email" 
                    id="registerEmail" required>
                </div> <div class="form-group"> 
                    <label 
                    for="password">Password:</label> 
                    <input type="password" 
                    name="password" 
                    id="registerPassword" 
                    required>
                </div> <div class="form-group"> 
                    <label 
                    for="confirm_password">Confirm 
                    Password:</label> <input 
                    type="password" 
                    name="confirm_password" 
                    id="confirm_password" 
                    required>
                </div> <div class="form-group"> 
                    <label for="uid">Choose 
                    UID:</label> <input 
                    type="text" name="uid" 
                    id="uid" required>
                </div> <div class="form-group"> 
                    <button 
                    type="submit">Register</button>
                </div> </form> </div> <!-- 
        پیام‌های خطا و 
        موفقیت --> <div id="message">
            <?php if ($errorMessage) { echo "<p 
            class='error'>$errorMessage</p>"; } 
            ?> <?php if ($successMessage) { echo 
            "<p 
            class='success'>$successMessage</p>"; 
            } ?>
        </div> </div> <!-- Redirect after login 
    success using JavaScript --> <?php if 
    ($successMessage && isset($_POST['action']) 
    && $_POST['action'] == 'login') { ?>
        <script> setTimeout(function() { 
                window.location.href = 
                "https://code2024.net/Test/index.php"; 
                // URL to redirect
            }, 3000); // 3 seconds delay
        </script> <?php } ?> </body>
</html>
