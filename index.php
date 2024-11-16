<?php error_reporting(E_ALL); 
ini_set('display_errors', 1); session_start(); 
$servername = "gold24.io"; $username = 
"new_gigar"; $password = "new_chatgpt"; $dbname = 
"accounting_db"; $conn = new mysqli($servername, 
$username, $password, $dbname); if 
($conn->connect_error) {
    die("Connection failed: " . 
    $conn->connect_error);
}
$errorMessage = ''; $successMessage = ''; if 
($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && 
    $_POST['action'] == 'register') {
        $name = $_POST['name']; $email = 
        $_POST['email']; $password = 
        $_POST['password']; $confirmPassword = 
        $_POST['confirm_password']; $uid = 
        $_POST['uid']; if ($password !== 
        $confirmPassword) {
            $errorMessage = "Passwords do not 
            match!";
        } else {
            $hashed_password = 
            password_hash($password, 
            PASSWORD_DEFAULT); $stmt = 
            $conn->prepare("SELECT id FROM users 
            WHERE email = ?"); 
            $stmt->bind_param("s", $email); 
            $stmt->execute(); 
            $stmt->store_result(); if 
            ($stmt->num_rows > 0) {
                $errorMessage = "This email is 
                already registered.";
            } else {
                $stmt = $conn->prepare("SELECT id 
                FROM users WHERE uid = ?"); 
                $stmt->bind_param("s", $uid); 
                $stmt->execute(); 
                $stmt->store_result(); if 
                ($stmt->num_rows > 0) {
                    $errorMessage = "This UID is 
                    already taken. Please just 
                    choose another number (just 
                    number).";
                } else {
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
                    } else {
                        $errorMessage = "Error: " 
                        . $stmt->error;
                    }
                }
            }
            $stmt->close();
        }
    }
    if (isset($_POST['action']) && 
    $_POST['action'] == 'login') {
        $email = $_POST['email']; $password = 
        $_POST['password']; $stmt = 
        $conn->prepare("SELECT id, name, 
        password, uid FROM users WHERE email = 
        ?"); $stmt->bind_param("s", $email); 
        $stmt->execute(); $stmt->store_result(); 
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, 
            $hashed_password, $uid); 
            $stmt->fetch(); if 
            (password_verify($password, 
            $hashed_password)) {
                $_SESSION['user_id'] = $id; 
                $_SESSION['user_name'] = $name; 
                $_SESSION['user_uid'] = $uid; 
                $successMessage = "Login 
                successful! Redirecting to your 
                dashboard..."; echo 
                "<script>setTimeout(function() { 
                window.location.href = 
                'https://code2024.net/Test/index.php';
                }, 3000);</script>";
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
        body { font-family: Arial, sans-serif; 
            background-color: #f8f9fa; display: 
            flex; justify-content: center; 
            align-items: center; height: 100vh; 
            margin: 0;
        }
        .container { width: 400px; padding: 2rem; 
            background-color: #ffffff; 
            box-shadow: 0px 4px 12px rgba(0, 0, 
            0, 0.2); border-radius: 8px; 
            text-align: center;
        }
        h1 { color: #007bff; font-size: 2rem; 
            margin-bottom: 1.5rem;
        }
        h2 { color: #333; font-size: 1.5rem; 
            margin: 1rem 0;
        }
        label { display: block; margin: 0.5rem 0 
            0.2rem; color: #495057; text-align: 
            left;
        }
        input[type="text"], input[type="email"], 
        input[type="password"] {
            width: 100%; padding: 0.8rem; border: 
            1px solid #ced4da; border-radius: 
            5px; margin-bottom: 1rem; transition: 
            all 0.3s ease;
        }
        input[type="text"]:focus, 
        input[type="email"]:focus, 
        input[type="password"]:focus {
            border-color: #007bff; box-shadow: 0 
            0 5px rgba(0, 123, 255, 0.3); 
            outline: none;
        }
        .button { width: 100%; padding: 0.8rem; 
            background-color: #007bff; border: 
            none; color: white; font-size: 1rem; 
            border-radius: 5px; cursor: pointer; 
            transition: background-color 0.3s;
        }
        .button:hover { background-color:
            #dc3545;
        }
        .error { color: #dc3545; font-weight: 
            bold;
        }
        .success { color: #28a745; font-weight: 
            bold;
        }
    </style> </head> <body> <div 
    class="container">
        <h1>Login / Register</h1>
        
        <div class="form-container"> 
            <h2>Login</h2> <form method="POST" 
            action="index.php">
                <input type="hidden" 
                name="action" value="login"> 
                <label for="email">Email:</label> 
                <input type="email" name="email" 
                id="email" required>
                
                <label 
                for="password">Password:</label> 
                <input type="password" 
                name="password" id="password" 
                required>
                
                <button type="submit" 
                class="button">Login</button>
            </form> </div> <div 
        class="form-container">
            <h2>Register</h2> <form method="POST" 
            action="index.php">
                <input type="hidden" 
                name="action" value="register">
                
                <label for="name">Name:</label> 
                <input type="text" name="name" 
                id="name" required>
                
                <label 
                for="registerEmail">Email:</label> 
                <input type="email" name="email" 
                id="registerEmail" required>
                
                <label 
                for="registerPassword">Password:</label> 
                <input type="password" 
                name="password" 
                id="registerPassword" required>
                
                <label 
                for="confirm_password">Confirm 
                Password:</label> <input 
                type="password" 
                name="confirm_password" 
                id="confirm_password" required>
                
                <label for="uid">Choose 
                UID:</label> <input type="text" 
                name="uid" id="uid" required>
                
                <button type="submit" 
                class="button">Register</button>
            </form> </div> <div id="message"> 
            <?php if ($errorMessage) { echo "<p 
            class='error'>$errorMessage</p>"; } 
            ?> <?php if ($successMessage) { echo 
            "<p 
            class='success'>$successMessage</p>";
            } ?>
        </div> </div> <?php if ($successMessage 
    && isset($_POST['action']) && 
    $_POST['action'] == 'login') { ?>
        <script> setTimeout(function() { 
                window.location.href = 
                "https://code2024.net/Test/index.php";
                // URL to redirect after login
            }, 3000); // 3 seconds delay
        </script> <?php } ?> </body>
</html>
