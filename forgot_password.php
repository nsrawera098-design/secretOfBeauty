<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';
include("connectToDB.php");
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // בדיקה אם המשתמש קיים
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // יצירת סיסמה אקראית חדשה
        $newPassword = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 10);

        // עדכון הסיסמה במסד הנתונים
        $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $newPassword, $email);
        $stmt->execute();

        // שליחת מייל עם הסיסמה החדשה
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'yosraqadri838@gmail.com'; // כתובת המייל שלך
            $mail->Password   = 'fncp srlc agyr bbiu'; // סיסמת אפליקציה
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('yosraqadri838@gmail.com', 'Balanced Diabetes');
            $mail->addAddress($email);

            $mail->Subject = '🔐 איפוס סיסמה לאתר סוכרת מאוזנת';
            $mail->Body    = "שלום,\n\nהסיסמה החדשה שלך לאתר היא: $newPassword\n\nמומלץ להיכנס ולעדכן אותה בעצמך.\n\nבברכה,\nצוות Balanced Diabetes";

            $mail->send();
            $message = "<span style='color:green'>📧 סיסמה חדשה נשלחה לכתובת: <b>$email</b></span>";
        } catch (Exception $e) {
            $message = "<span style='color:red'>❌ שגיאה בשליחת המייל: {$mail->ErrorInfo}</span>";
        }
    } else {
        $message = "<span style='color:red'>❌ כתובת המייל לא קיימת במערכת.</span>";
    }
}
?>


<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>שכחת סיסמה?</title>
    <style>
    body {
        direction: rtl;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #fbefff, #fde6f5);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .box {
        background: white;
        padding: 50px 40px;
        border-radius: 25px;
        box-shadow: 0 10px 25px rgba(255, 140, 198, 0.2);
        text-align: center;
        width: 420px;
    }

    .box h2 {
        color: #d63384;
        margin-bottom: 30px;
        font-size: 28px;
        font-weight: bold;
    }

    .box input[type=email] {
        padding: 14px;
        width: 100%;
        border-radius: 12px;
        border: 1px solid #f3c5d9;
        background-color: #fff0f6;
        margin-bottom: 20px;
        font-size: 16px;
        transition: 0.3s;
    }

    .box input[type=email]:focus {
        border-color: #d63384;
        outline: none;
        background-color: #ffe6f0;
    }

    .box button {
        padding: 12px 30px;
        background: linear-gradient(to right, #ff7eb9, #ff65a3);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .box button:hover {
        background: linear-gradient(to right, #e0559e, #d63384);
    }

    .message {
        margin-top: 20px;
        font-size: 15px;
        color: #28a745;
    }

    .error {
        color: red;
        font-weight: bold;
    }
        .back-button {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 25px;
    background-color: #ffe6f0;
    color: #d63384;
    border: 2px solid #d63384;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s ease;
}

.back-button:hover {
    background-color: #fcd5e5;
    color: #8b005f;
}

</style>

</head>
<body>

<div class="box">
<h2>🔐 שכחת סיסמה?</h2>
    <form method="post">
        <input type="email" name="email" placeholder="הכנס כתובת אימייל" required>
        <button type="submit">שלח סיסמה חדשה</button>
    </form>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>
    <a href="index.php" class="back-button">⬅ חזרה להתחברות</a>
</div>

</body>
</html>
