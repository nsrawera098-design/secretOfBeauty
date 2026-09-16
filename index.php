<?php
session_start();
require_once 'connectToDB.php';

$error_message = "";
$max_attempts = 3;
$lockout_time_seconds = 60;

$attempts = $_COOKIE['login_attempts'] ?? 0;
$lockout_time = $_COOKIE['lockout_time'] ?? 0;

if (time() < $lockout_time) {
    $left_time = $lockout_time - time();
    if ($left_time >= 60) {
        $minutes = ceil($left_time / 60);
        die("ניסית 3 פעמים. נא לנסות שוב עוד $minutes דקות.");
    } else {
        die("ניסית 3 פעמים. נא לנסות שוב עוד $left_time שניות.");
    }
}

if (isset($_GET["btn"]) && time() >= $lockout_time) {
    $username = $_GET["username"];
    $password = $_GET["password"];

    $sql = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Database query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        setcookie('login_attempts', 0, time() - 3600, '/');
        setcookie('lockout_time', '', time() - 3600, '/');

        $_SESSION['username'] = $username;
        $_SESSION['role'] = $row['role'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['birthdate'] = $row['birthdate'];
        $_SESSION['profile_image'] = $row['profile_image'];

        if ($row['role'] == 1) {
            header("Location: Adminpage.php");
        } elseif ($_SESSION['role'] == 2) {
            header("Location: employee_branches.php");
        } else {
            header("Location: homePage.php");
        }
        exit();
    } else {
        $attempts++;
        setcookie('login_attempts', $attempts, time() + 60, '/');

        if ($attempts >= $max_attempts) {
            $lockout_time = time() + $lockout_time_seconds;
            setcookie('lockout_time', $lockout_time, time() + $lockout_time_seconds, '/');
            $error_message = "ניסית 3 פעמים. נא לנסות שוב עוד " . ($lockout_time_seconds / 60) . " דקות.";
        } else {
            $error_message = "שם משתמש או סיסמה שגויים. ניסית $attempts מתוך $max_attempts.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>התחברות - Belissa</title>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;700&display=swap" rel="stylesheet">
    <style>
    * {
        box-sizing: border-box;
        font-family: 'Heebo', sans-serif;
    }

    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        direction: rtl;
        background: url('atar.jpg') no-repeat center center;
        background-size: cover;
    }

    .container {
        background: rgba(255, 255, 255, 0.96);
        border-radius: 50% 0 0 50% / 100% 0 0 100%;
        padding: 60px 40px;
        width: 400px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        text-align: center;
        backdrop-filter: blur(4px);
    }

    h1 {
        color: #9c27b0;
        font-size: 28px;
        margin-bottom: 10px;
    }

    p {
        margin-bottom: 25px;
        color: #555;
    }

    .form-group {
        text-align: right;
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #bbb;
        border-radius: 6px;
        font-size: 14px;
    }

    .btn {
        width: 100%;
        padding: 10px;
        background: linear-gradient(to right, #f06292, #ba68c8);
        border: none;
        color: white;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }

    .btn:hover {
        opacity: 0.9;
    }

    .links {
        margin-top: 20px;
        font-size: 14px;
    }

    .links a {
        text-decoration: none;
        color: #9c27b0;
        font-weight: bold;
        margin: 0 5px;
    }

    .links a:hover {
        text-decoration: underline;
    }

    .error-msg {
        background-color: #f8d7da;
        padding: 10px;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        margin-bottom: 15px;
    }
</style>
</head>
<body>
    <div class="container">
        <h1>ברוכה הבאה</h1>
        <p>התחברי לחשבון שלך</p>

        <?php if (!empty($error_message)) echo "<div class='error-msg'>$error_message</div>"; ?>

        <form method="get">
            <div class="form-group">
                <label for="username">שם משתמש</label>
                <input type="text" name="username" id="username" placeholder="הקלידי שם משתמש" required>
            </div>
            <div class="form-group">
                <label for="password">סיסמה</label>
                <input type="password" name="password" id="password" placeholder="הקלידי סיסמה" required>
            </div>
            <button type="submit" name="btn" class="btn">כניסה</button>
        </form>

        <div class="links">
            <a href="forgot_password.php">שכחתי סיסמה</a> | 
            <a href="signUp.php">אין לך חשבון? הירשמי</a>
        </div>
    </div>
</body>
</html>
