<?php
session_start();
require_once 'connectToDB.php';
require_once 'mail.php';
$conn->set_charset("utf8");
include('navbar.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 2 || !isset($_GET['business_id'])) {
    header("Location: index.php");
    exit();
}

$business_id = intval($_GET['business_id']);
$today = date('Y-m-d');

// ביטול תור
if (isset($_GET['cancel_id'])) {
    $appointment_id = intval($_GET['cancel_id']);

    $appointmentInfo = $conn->query("SELECT * FROM appointments WHERE id = $appointment_id")->fetch_assoc();
    $conn->query("DELETE FROM appointments WHERE id = $appointment_id");

    sendCancelEmail($appointmentInfo['email'], $appointmentInfo['full_name'], $appointmentInfo['business_name'], $appointmentInfo['appointment_date'], $appointmentInfo['appointment_time']);

    $employee_query = "SELECT email FROM user WHERE role = 2";
    $employee_result = $conn->query($employee_query);

    if ($employee_result && $employee_result->num_rows > 0) {
        while ($emp = $employee_result->fetch_assoc()) {
            notifyEmployeeCancel($emp['email'], $appointmentInfo['full_name'], $appointmentInfo['business_name'], $appointmentInfo['appointment_date'], $appointmentInfo['appointment_time']);
        }
    }

    $wait = $conn->query("SELECT * FROM waiting_list WHERE business_name = (SELECT name FROM businesses WHERE id = $business_id) ORDER BY id LIMIT 1");
    if ($wait && $wait->num_rows > 0) {
        $next = $wait->fetch_assoc();
        $insert = "INSERT INTO appointments 
            (full_name, email, phone, category, location, business_name, appointment_date, appointment_time, comments)
            VALUES (
                '{$next['full_name']}', '{$next['email']}', '{$next['phone']}', '{$next['category']}',
                '{$next['location']}', '{$next['business_name']}', '{$next['preferred_date']}',
                '{$next['preferred_time']}', '{$next['comments']}'
            )";
        $conn->query($insert);
        $conn->query("DELETE FROM waiting_list WHERE id = " . $next['id']);

        sendAppointmentEmailSimple($next['email'], $next['full_name'], $next['business_name'], $next['preferred_date'], $next['preferred_time']);

        $employee_result->data_seek(0);
        while ($emp = $employee_result->fetch_assoc()) {
            notifyEmployeePromoted($emp['email'], $next['full_name'], $next['business_name'], $next['preferred_date'], $next['preferred_time']);
        }

        $message = "הלקוח קיבל את התור שהתפנה.";
    } else {
        $message = "התור בוטל. אין לקוחות ממתינים.";
    }
}

// קידום מרשימת המתנה
if (isset($_GET['promote_id'])) {
    $wait_id = intval($_GET['promote_id']);
    $next = $conn->query("SELECT * FROM waiting_list WHERE id = $wait_id")->fetch_assoc();

    if ($next) {
        $insert = "INSERT INTO appointments 
            (full_name, email, phone, category, location, business_name, appointment_date, appointment_time, comments)
            VALUES (
                '{$next['full_name']}', '{$next['email']}', '{$next['phone']}', '{$next['category']}',
                '{$next['location']}', '{$next['business_name']}', '{$next['preferred_date']}',
                '{$next['preferred_time']}', '{$next['comments']}'
            )";
        if ($conn->query($insert)) {
            $conn->query("DELETE FROM waiting_list WHERE id = $wait_id");

            sendAppointmentEmailSimple($next['email'], $next['full_name'], $next['business_name'], $next['preferred_date'], $next['preferred_time']);

            $employee_query = "SELECT email FROM user WHERE role = 2";
            $employee_result = $conn->query($employee_query);
            if ($employee_result && $employee_result->num_rows > 0) {
                while ($emp = $employee_result->fetch_assoc()) {
                    notifyEmployeePromoted($emp['email'], $next['full_name'], $next['business_name'], $next['preferred_date'], $next['preferred_time']);
                }
            }

            $message = "המשתמש קודם לרשימת התורים.";
        }
    }
}

$business = $conn->query("SELECT * FROM businesses WHERE id = $business_id")->fetch_assoc();
$appointments = $conn->query("SELECT * FROM appointments WHERE business_name = '{$business['name']}' AND appointment_date = '$today'");
$waiting_list = $conn->query("SELECT * FROM waiting_list WHERE business_name = '{$business['name']}' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>ניהול תורים - <?php echo htmlspecialchars($business['category']); ?></title>
    <style>
        body { font-family: Arial; direction: rtl; background-color: #f8f8fc; padding: 30px; }
        h2 { background-color: #b58fd6; color: white; padding: 20px; border-radius: 10px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #a36fc4; color: white; }
        a.btn { background: #d63384; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; }
        a.btn:hover { background-color: #b21f5f; }
        .msg { color: green; text-align: center; margin: 20px; font-weight: bold; }
        .back-btn {
    display: inline-block;
    background-color: #6a1b9a;
    color: white;
    padding: 10px 20px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: bold;
    margin-top: 20px;
    margin-bottom: 20px;
    transition: background-color 0.3s ease;
}

.back-btn:hover {
    background-color: #4a148c;
}
    </style>
</head>
<body>

<h2>ניהול תורים - <?php echo htmlspecialchars($business['category']) . " (" . htmlspecialchars($business['name']) . ")"; ?></h2>
<a href="employee_branches.php" class="back-btn">🔙 חזרה לסניפים</a>
<?php if (isset($message)) echo "<p class='msg'>$message</p>"; ?>

<h3>תורים להיום</h3>
<table>
    <tr><th>שם</th><th>אימייל</th><th>שעה</th><th>הערות</th><th>פעולה</th></tr>
    <?php while ($a = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?= $a['full_name'] ?></td>
            <td><?= $a['email'] ?></td>
            <td><?= $a['appointment_time'] ?></td>
            <td><?= $a['comments'] ?></td>
            <td><a class="btn" href="?business_id=<?= $business_id ?>&cancel_id=<?= $a['id'] ?>" onclick="return confirm('לבטל את התור?')">בטל</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<h3>רשימת המתנה</h3>
<table>
    <tr><th>שם</th><th>אימייל</th><th>תאריך/שעה</th><th>הערות</th><th>פעולה</th></tr>
    <?php while ($w = $waiting_list->fetch_assoc()): ?>
        <tr>
            <td><?= $w['full_name'] ?></td>
            <td><?= $w['email'] ?></td>
            <td><?= $w['preferred_date'] ?> <?= $w['preferred_time'] ?></td>
            <td><?= $w['comments'] ?></td>
            <td><a class="btn" href="?business_id=<?= $business_id ?>&promote_id=<?= $w['id'] ?>" onclick="return confirm('לקדם את הלקוח?')">קדם</a></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
