<?php
session_start();
require_once 'connectToDB.php';

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["btn"])) {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $email === "" || $password === "") {

        $error_message = "נא למלא את כל השדות.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error_message = "כתובת האימייל אינה תקינה.";

    } elseif (strlen($password) < 6) {

        $error_message = "הסיסמה חייבת להכיל לפחות 6 תווים.";

    } else {

        // ==============================
        // CHECK IF USER ALREADY EXISTS
        // ==============================

        $stmt = $conn->prepare("
            SELECT id
            FROM user
            WHERE username = ? OR email = ?
            LIMIT 1
        ");

        if (!$stmt) {
            die("Database error.");
        }

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $error_message =
                "שם המשתמש או כתובת האימייל כבר קיימים במערכת.";

        } else {

            // ==============================
            // HASH PASSWORD
            // ==============================

            $hashedPassword =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

            $role = 0;

            // ==============================
            // INSERT USER
            // ==============================

            $insertStmt = $conn->prepare("
                INSERT INTO user
                (username, email, password, role)
                VALUES (?, ?, ?, ?)
            ");

            if (!$insertStmt) {
                die("Database error.");
            }

            $insertStmt->bind_param(
                "sssi",
                $username,
                $email,
                $hashedPassword,
                $role
            );

            if ($insertStmt->execute()) {

                $success_message =
                    "ההרשמה בוצעה בהצלחה!";

            } else {

                $error_message =
                    "אירעה שגיאה בהרשמה.";
            }

            $insertStmt->close();
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

    <title>הרשמה - Belissa</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            direction: rtl;

            font-family:
                'Varela Round',
                sans-serif;

            background:
                linear-gradient(
                    to right,
                    #ff758c,
                    #ff7eb3
                );

            display: flex;

            align-items: center;
            justify-content: center;

            min-height: 100vh;

            padding: 20px;
        }

        .container {

            display: flex;

            background:
                rgba(255,255,255,0.2);

            backdrop-filter: blur(10px);

            border-radius: 20px;

            overflow: hidden;

            box-shadow:
                0 10px 30px
                rgba(0,0,0,0.2);

            max-width: 950px;

            width: 100%;
        }

        .image-side {

            flex: 1;

            background-color: #ffeef5;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 20px;
        }

        .image-side img {

            width: 90%;

            max-width: 400px;
        }

        .form-side {

            flex: 1;

            padding: 40px;

            color: white;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }

        .form-side h1 {

            margin-bottom: 25px;

            font-size: 32px;
        }

        .form-side label {

            margin-top: 10px;

            display: block;

            font-size: 15px;
        }

        .form-side input {

            width: 100%;

            padding: 12px;

            margin-top: 5px;

            margin-bottom: 15px;

            border: none;

            border-radius: 10px;

            font-size: 16px;

            background: white;

            color: #333;
        }

        .form-side button {

            width: 100%;

            margin-top: 10px;

            background-color: #ff4757;

            color: white;

            padding: 12px;

            border: none;

            border-radius: 10px;

            font-weight: bold;

            font-size: 16px;

            cursor: pointer;

            transition: all 0.3s ease;
        }

        .form-side button:hover {

            background-color: #ff75afcf;

            transform: scale(1.02);
        }

        .back-link {

            display: block;

            margin-top: 20px;

            text-align: center;

            color: white;

            text-decoration: none;

            font-weight: bold;
        }

        .message {

            padding: 10px;

            margin-bottom: 15px;

            border-radius: 8px;

            color: #333;
        }

        .error {

            background-color: #f8d7da;

            border: 1px solid #f5c6cb;
        }

        .success {

            background-color: #d4edda;

            border: 1px solid #c3e6cb;
        }

        @media (max-width: 768px) {

            .container {
                flex-direction: column;
            }

            .image-side {
                display: none;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <div class="image-side">

        <img
            src="photos/aa.png"
            alt="מוצרי קוסמטיקה"
        >

    </div>


    <div class="form-side">

        <h1>הרשמה</h1>


        <?php if (!empty($error_message)): ?>

            <div class="message error">

                <?php
                echo htmlspecialchars(
                    $error_message
                );
                ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($success_message)): ?>

            <div class="message success">

                <?php
                echo htmlspecialchars(
                    $success_message
                );
                ?>

            </div>

            <script>
                setTimeout(function () {
                    window.location.href = "index.php";
                }, 1500);
            </script>

        <?php endif; ?>


        <form method="POST">

            <label for="username">
                שם משתמש
            </label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="בחר שם משתמש"
                required
                autocomplete="username"
            >


            <label for="email">
                אימייל
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="הכנס כתובת אימייל"
                required
                autocomplete="email"
            >


            <label for="password">
                סיסמה
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="בחר סיסמה"
                required
                minlength="6"
                autocomplete="new-password"
            >


            <button
                type="submit"
                name="btn"
            >
                צור חשבון
            </button>

        </form>


        <a
            class="back-link"
            href="index.php"
        >
            חזרה לדף התחברות
        </a>

    </div>

</div>

</body>
</html>
