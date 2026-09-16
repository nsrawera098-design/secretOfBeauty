<?php
include 'connectToDB.php';
session_start();

$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    // שליפת התור
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();

    if ($appointment) {
        $business_name = $appointment['business_name'];
        $appointment_date = $appointment['appointment_date'];
        $appointment_time = $appointment['appointment_time'];

        // מחיקת התור
        $delete_stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
        $delete_stmt->bind_param("i", $appointment_id);
        $delete_stmt->execute();

        // בדיקת רשימת המתנה
        $waitlist_stmt = $conn->prepare("
            SELECT * FROM waiting_list 
            WHERE business_name = ? AND preferred_date = ? AND preferred_time = ?
            ORDER BY created_at ASC LIMIT 1
        ");
        $waitlist_stmt->bind_param("sss", $business_name, $appointment_date, $appointment_time);
        $waitlist_stmt->execute();
        $wait_result = $waitlist_stmt->get_result();
        $waiting_user = $wait_result->fetch_assoc();

        if ($waiting_user) {
            $insert_stmt = $conn->prepare("
                INSERT INTO appointments 
                (name, email, phone, category, location, business_name, appointment_date, appointment_time, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert_stmt->bind_param("sssssssss",
                $waiting_user['full_name'],
                $waiting_user['email'],
                $waiting_user['phone'],
                $waiting_user['category'],
                $waiting_user['location'],
                $waiting_user['business_name'],
                $waiting_user['preferred_date'],
                $waiting_user['preferred_time'],
                $waiting_user['comments']
            );
            $insert_stmt->execute();

            // הסרת הממתין
            $delete_wait_stmt = $conn->prepare("DELETE FROM waiting_list WHERE id = ?");
            $delete_wait_stmt->bind_param("i", $waiting_user['id']);
            $delete_wait_stmt->execute();

            $message = "התור שלך בוטל ✅ והממתין הבא בתור הוזמן במקומך.";
            $success = true;
        } else {
            $message = "התור שלך בוטל ✅ אך לא נמצאה המתנה מתאימה.";
            $success = true;
        }
    } else {
        $message = "⚠️ לא נמצא תור מתאים לביטול.";
    }

    $stmt->close();
    $conn->close();
} else {
    $message = "שגיאה: לא נשלח מזהה תור.";
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>ביטול תור</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #ffe4ec, #fff0f5);
        color: #4a4a4a;
        text-align: center;
        padding: 60px;
        margin: 0;
    }

    .message-box {
        background: <?= $success ? '#ffe6f0' : '#ffe0e6' ?>;
        color: <?= $success ? '#8b005d' : '#a1002f' ?>;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(255, 192, 203, 0.4);
        max-width: 550px;
        margin: auto;
        font-size: 20px;
        line-height: 1.6;
        border: 2px dashed #ffc0cb;
    }

    a.button {
        display: inline-block;
        margin-top: 30px;
        padding: 12px 28px;
        background-color: #ff69b4;
        color: white;
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
        box-shadow: 0 4px 12px rgba(255, 105, 180, 0.3);
    }

    a.button:hover {
        background-color: #e055a0;
        transform: scale(1.05);
    }
</style>

</head>
<body>

<div class="message-box">
    <?= $message ?>
    <br>
    <a href="appointment_history.php" class="button">חזרה להיסטוריית התורים</a>
</div>

</body>
</html>
