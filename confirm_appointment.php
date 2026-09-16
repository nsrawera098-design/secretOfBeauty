<?php
session_start();
require_once 'connectToDB.php';
require_once 'mail.php';
$conn->set_charset("utf8");
header('Content-Type: text/html; charset=utf-8');

include('navbar.php');

if (!isset($_SESSION['today_appointments'])) {
    $_SESSION['today_appointments'] = [];
}

function sanitize($conn, $value) {
    return $conn->real_escape_string(trim($value));
}

function sendMessage($email, $message) {
    echo "<p>📨 נשלחה הודעה ל־<strong>$email</strong>: $message</p>";
}

// בדיקת כל השדות החיוניים
function notifyEmployeeAppointment($employeeEmail, $clientName, $businessName, $date, $time) {
    $subject = "📅 תור חדש נקבע";
    $message = "נקבע תור חדש עבור $clientName בעסק $businessName בתאריך $date בשעה $time.";
    // כאן אתה יכול לשלוח מייל אמיתי אם תרצה
    // mail($employeeEmail, $subject, $message);
    echo "<p>📧 נשלחה הודעה לעובד: $employeeEmail - $message</p>";
}

if (
    !empty($_POST['full_name']) &&
    !empty($_POST['email']) &&
    !empty($_POST['phone']) &&
    !empty($_POST['category']) &&
    !empty($_POST['location']) &&
    !empty($_POST['business_name']) &&
    !empty($_POST['appointment_date']) &&
    !empty($_POST['appointment_time'])
) {
    $full_name = sanitize($conn, $_POST['full_name']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $category = sanitize($conn, $_POST['category']);
    $location = sanitize($conn, $_POST['location']);
    $business_name = sanitize($conn, $_POST['business_name']);
    $appointment_date = sanitize($conn, $_POST['appointment_date']);
    $appointment_time = sanitize($conn, $_POST['appointment_time']);
    $comments = isset($_POST['comments']) ? sanitize($conn, $_POST['comments']) : '';

    // בדיקה אם התור כבר קיים (שעה תפוסה)
    $checkSql = "SELECT * FROM appointments
                 WHERE business_name = '$business_name'
                 AND appointment_date = '$appointment_date'
                 AND appointment_time = '$appointment_time'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult && $checkResult->num_rows > 0) {
        // ➕ התור תפוס - כניסה לרשימת המתנה
        $getQueueNumber = "SELECT COUNT(*) AS total FROM waiting_list 
                           WHERE business_name = '$business_name' 
                           AND preferred_date = '$appointment_date' 
                           AND preferred_time = '$appointment_time'";
        $resultQueue = $conn->query($getQueueNumber);
        $row = $resultQueue->fetch_assoc();
        $queue_position = $row['total'] + 1;

        $waitSql = "INSERT INTO waiting_list
            (full_name, email, phone, category, location, business_name, preferred_date, preferred_time, comments)
            VALUES
            ('$full_name', '$email', '$phone', '$category', '$location', '$business_name', '$appointment_date', '$appointment_time', '$comments')";

        if ($conn->query($waitSql) === TRUE) {
            echo "<div class='error'><strong>😞 התור תפוס</strong>
            <br>הוספת לרשימת ההמתנה. מספרך: <strong>$queue_position</strong></div>";


            // מייל למשתמש
            sendWaitingListEmail($email, $full_name, $business_name, $appointment_date, $appointment_time, $queue_position);

            // מייל לכל העובדים
            $employee_query = "SELECT email FROM user WHERE role = 2";
            $employee_result = $conn->query($employee_query);
            while ($emp = $employee_result->fetch_assoc()) {
                notifyEmployeeWaitingList($emp['email'], $full_name, $business_name, $appointment_date, $appointment_time);
            }
        } else {
            echo "<p style='color:red;'>שגיאה בהוספה לרשימת המתנה: " . $conn->error . "</p>";
        }

    } else {
        // ➕ תור פנוי
        $insertSql = "INSERT INTO appointments
                      (full_name, email, phone, category, location, business_name, appointment_date, appointment_time, comments)
                      VALUES
                      ('$full_name', '$email', '$phone', '$category', '$location', '$business_name', '$appointment_date', '$appointment_time', '$comments')";

        if ($conn->query($insertSql) === TRUE) {
           echo "<div class='success'><strong>✅ הזמנתך נקלטה בהצלחה!</strong>
           <br>תור ל־<strong>$business_name</strong> בתאריך <strong>$appointment_date</strong> בשעה <strong>$appointment_time</strong>.</div>";


            // מייל למשתמש
            sendAppointmentEmailSimple($email, $full_name, $business_name, $appointment_date, $appointment_time);

            // מייל לכל העובדים
            $employee_query = "SELECT email FROM user WHERE role = 2";
            $employee_result = $conn->query($employee_query);
            while ($emp = $employee_result->fetch_assoc()) {
                notifyEmployeeAppointment($emp['email'], $full_name, $business_name, $appointment_date, $appointment_time);
            }

            $_SESSION['today_appointments'][] = [
                'full_name' => $full_name,
                'business_name' => $business_name,
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time
            ];
        } else {
            echo "<p style='color:red;'>שגיאה בהזמנת תור: " . $conn->error . "</p>";
        }
    }

} else {
    echo "<p style='color:red;'>❌ שדות חסרים. אנא מלא את כל הטופס.</p>";
} 
?>

<h2 style="text-align:center; color:#d63384;">📅 היסטוריית תורים</h2> 

<?php
$user_email = $_SESSION['email'];
$sql = "SELECT * FROM appointments WHERE email = '$user_email' ORDER BY appointment_date DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table style='width:90%; margin:auto; border:1px solid #ffd3e0;'>
            <tr>
                <th>תאריך</th>
                <th>שעה</th>
                <th>מיקום</th>
                <th>קטגוריה</th>
                <th>עסק</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['appointment_date']}</td>
                <td>{$row['appointment_time']}</td>
                <td>{$row['location']}</td>
                <td>{$row['category']}</td>
                <td>{$row['business_name']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>לא נמצאו תורים קודמים.</p>";
}
$conn->close();
?>
<head>
  <meta charset="UTF-8">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Heebo:wght@400;700&display=swap');

    body {
        font-family: 'Heebo', sans-serif;
        background: #fff0f5;
        color: #333;
        margin: 0;
        padding: 0;
        direction: rtl;
        text-align: right;
    }

    h2 {
        color: #d63384;
        text-align: center;
        margin-top: 30px;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
        border-right: 6px solid #28a745;
        padding: 15px;
        margin: 20px auto;
        width: 90%;
        border-radius: 8px;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
        border-right: 6px solid #dc3545;
        padding: 15px;
        margin: 20px auto;
        width: 90%;
        border-radius: 8px;
    }

    .message {
        background-color: #fff3cd;
        color: #856404;
        border-right: 6px solid #ffc107;
        padding: 15px;
        margin: 20px auto;
        width: 90%;
        border-radius: 8px;
    }

    table {
        width: 90%;
        margin: 30px auto;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ffe0eb;
    }

    th {
        background-color: #ffd3e0;
        color: #5e1c39;
    }

    tr:hover {
        background-color: #fff0f5;
    }

    p {
        font-size: 16px;
        line-height: 1.5;
        width: 90%;
        margin: 10px auto;
    }

    strong {
        font-weight: bold;
        color: #d63384;
    }
  </style>
</head>

