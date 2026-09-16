<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('navbar.php');



if (isset($_POST["submit"])) {
    $name    = $_POST["name"];
    $email   = $_POST["email"];
    $phone   = $_POST["phone"];
    $address = $_POST["address"];
    $message = $_POST["message"];

    // שלב 1: שמירה במסד נתונים
    $conn = new mysqli("localhost", "root", "", "user");
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO tblmessages(name, email, phone, address, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $address, $message);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // שלב 2: שליחת מייל ל-shimaa
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shimaa.sabe123@gmail.com';  // המייל של שימאא
        $mail->Password   = 'unma xluu gkbz tfnx';       // סיסמת אפליקציה
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom($email, $name);
        $mail->addAddress('shimaa.sabe123@gmail.com'); // רק שימאא תקבל

        $mail->Subject = '📩 הודעה חדשה מטופס צור קשר';
        $mail->Body    = "שם: $name\nאימייל: $email\nטלפון: $phone\nכתובת: $address\n\nהודעה:\n$message";

        $mail->send();
        echo "<p style='color: green; font-size: 18px; text-align: center;'>✅ ההודעה נשלחה בהצלחה!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red; font-size: 18px; text-align: center;'>❌ שגיאה בשליחת המייל: {$mail->ErrorInfo}</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>צור קשר</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #fddde6, #ffc4d6);
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(255, 105, 180, 0.2);
            width: 400px;
            text-align: center;
            margin-top: 80px;
        }

        h2 {
            color: #d63384;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ffc0cb;
            border-radius: 6px;
        }

        button {
            background: #ff69b4;
            border: none;
            padding: 10px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 6px;
            width: 100%;
        }

        button:hover {
            background: #e754a6;
        }
        .back-home-btn {
    display: inline-block;
    margin-top: 15px;
    background-color: #d63384; /* ורוד כהה קרוב לצבע הכותרת */
    color: white;
    padding: 10px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
    font-size: 16px;
}

.back-home-btn:hover {
    background-color: #a02c6c; /* כהה יותר בהובר */
}

    </style>
</head>
<body>

<div class="container">
    <h2>צור קשר</h2>
    <form method="post" action="contact.php">
        <input type="text" name="name" placeholder="השם שלך" required>
        <input type="email" name="email" placeholder="כתובת אימייל" required>
        <input type="text" name="phone" placeholder="מספר טלפון" required>
        <input type="text" name="address" placeholder="כתובת" required>
        <textarea name="message" placeholder="ההודעה שלך" required></textarea>
        <button type="submit" name="submit">שלח הודעה</button>
    </form>
    <a href="homepage.php" class="back-home-btn">🏠 חזרה לדף הבית</a>
</div>

</body>
</html>