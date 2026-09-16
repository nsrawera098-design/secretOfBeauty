<?php
session_start();
require_once 'connectToDB.php';

$error_message = "";

$max_attempts = 3;
$lockout_time_seconds = 60;

// قراءة عدد المحاولات
$attempts = isset($_COOKIE['login_attempts'])
    ? (int)$_COOKIE['login_attempts']
    : 0;

// قراءة وقت الحظر
$lockout_time = isset($_COOKIE['lockout_time'])
    ? (int)$_COOKIE['lockout_time']
    : 0;


// ==============================
// CHECK LOCKOUT
// ==============================

if (time() < $lockout_time) {

    $left_time = $lockout_time - time();

    $error_message =
        "ניסית 3 פעמים. נא לנסות שוב עוד "
        . $left_time
        . " שניות.";
}


// ==============================
// LOGIN
// ==============================

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["btn"])
    && time() >= $lockout_time) {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";


    // בדיקה ששני השדות מלאים
    if (empty($username) || empty($password)) {

        $error_message = "נא למלא שם משתמש וסיסמה.";

    } else {

        // ==============================
        // PREPARED STATEMENT
        // ==============================

        $stmt = $conn->prepare(
            "SELECT id,
                    username,
                    password,
                    email,
                    role,
                    birthdate,
                    profile_image
             FROM user
             WHERE username = ?
             LIMIT 1"
        );

        if (!$stmt) {
            die("Database error.");
        }

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $result = $stmt->get_result();


        // ==============================
        // USER FOUND
        // ==============================

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            // בדיקת סיסמה מוצפנת
            if (password_verify($password, $row['password'])) {

                // איפוס ניסיונות
                setcookie(
                    'login_attempts',
                    '',
                    time() - 3600,
                    '/'
                );

                setcookie(
                    'lockout_time',
                    '',
                    time() - 3600,
                    '/'
                );


                // הגנה על session fixation
                session_regenerate_id(true);


                // שמירת פרטי המשתמש
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = (int)$row['role'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['birthdate'] = $row['birthdate'];
                $_SESSION['profile_image'] =
                    $row['profile_image'];


                // ==============================
                // REDIRECT BY ROLE
                // ==============================

                if ((int)$row['role'] === 1) {

                    header("Location: Adminpage.php");

                } elseif ((int)$row['role'] === 2) {

                    header(
                        "Location: employee_branches.php"
                    );

                } else {

                    header("Location: homePage.php");
                }

                $stmt->close();
                $conn->close();

                exit();

            } else {

                // סיסמה לא נכונה
                $attempts++;
            }

        } else {

            // משתמש לא נמצא
            $attempts++;
        }


        // ==============================
        // FAILED LOGIN
        // ==============================

        if (!empty($error_message) === false
            && $attempts > 0) {

            if ($attempts >= $max_attempts) {

                $lockout_time =
                    time() + $lockout_time_seconds;

                setcookie(
                    'login_attempts',
                    $attempts,
                    $lockout_time,
                    '/'
                );

                setcookie(
                    'lockout_time',
                    $lockout_time,
                    $lockout_time,
                    '/'
                );

                $error_message =
                    "ניסית 3 פעמים. נא לנסות שוב עוד "
                    . $lockout_time_seconds
                    . " שניות.";

            } else {

                setcookie(
                    'login_attempts',
                    $attempts,
                    time() + 3600,
                    '/'
                );

                $error_message =
                    "שם משתמש או סיסמה שגויים. "
                    . "ניסית "
                    . $attempts
                    . " מתוך "
                    . $max_attempts
                    . ".";
            }
        }


        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>התחברות - Belissa</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
            font-family: 'Heebo', sans-serif;
        }

        body {

            margin: 0;
            padding: 0;

            min-height: 100vh;

            display: flex;

            justify-content: center;
            align-items: center;

            direction: rtl;

            background:
                url('atar.jpg')
                no-repeat
                center center;

            background-size: cover;
        }

        .container {

            background:
                rgba(255,255,255,0.96);

            border-radius:
                50% 0 0 50%
                / 100% 0 0 100%;

            padding: 60px 40px;

            width: 400px;

            box-shadow:
                0 8px 20px
                rgba(0,0,0,0.2);

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

            border:
                1px solid #bbb;

            border-radius: 6px;

            font-size: 14px;
        }

        .btn {

            width: 100%;

            padding: 10px;

            background:
                linear-gradient(
                    to right,
                    #f06292,
                    #ba68c8
                );

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

            border:
                1px solid #f5c6cb;

            border-radius: 5px;

            margin-bottom: 15px;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>ברוכה הבאה</h1>

    <p>התחברי לחשבון שלך</p>


    <?php if (!empty($error_message)): ?>

        <div class="error-msg">

            <?php
            echo htmlspecialchars(
                $error_message
            );
            ?>

        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label for="username">
                שם משתמש
            </label>

            <input
                type="text"
                name="username"
                id="username"
                placeholder="הקלידי שם משתמש"
                required
                autocomplete="username"
            >

        </div>


        <div class="form-group">

            <label for="password">
                סיסמה
            </label>

            <input
                type="password"
                name="password"
                id="password"
                placeholder="הקלידי סיסמה"
                required
                autocomplete="current-password"
            >

        </div>


        <button
            type="submit"
            name="btn"
            class="btn"
        >
            כניסה
        </button>

    </form>


    <div class="links">

        <a href="forgot_password.php">
            שכחתי סיסמה
        </a>

        |

        <a href="signUp.php">
            אין לך חשבון? הירשמי
        </a>

    </div>

</div>

</body>
</html>
