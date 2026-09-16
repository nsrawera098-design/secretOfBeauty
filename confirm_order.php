<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function createMailer() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'shimaa.sabe123@gmail.com';
    $mail->Password = 'unma xluu gkbz tfnx';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('shimaa.sabe123@gmail.com', 'המערכת שלנו');
    return $mail;
}

// למשתמש - אישור תור
function sendAppointmentEmailSimple($to, $name, $business, $date, $time) {
    try {
        $mail = createMailer();
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = 'אישור תור';
        $mail->Body = "<h2>שלום $name,</h2><p>תורך ל־<strong>$business</strong> נקבע לתאריך <strong>$date</strong> בשעה <strong>$time</strong>.</p><p>נשמח לראותך!</p>";
        $mail->send();
    } catch (Exception $e) {}
}

// למשתמש - רשימת המתנה
function sendWaitingListEmail($to, $name, $business, $date, $time, $position) {
    try {
        $mail = createMailer();
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = 'נרשמת לרשימת המתנה';
        $mail->Body = "<h2>שלום $name,</h2><p>לא ניתן היה לקבוע את התור שלך ל־<strong>$business</strong>.</p><p>הוספת לרשימת ההמתנה. מספרך: <strong>$position</strong>.</p>";
        $mail->send();
    } catch (Exception $e) {}
}

// לעובדים - משתמש נכנס להמתנה
function notifyEmployeeWaitingList($employeeEmail, $clientName, $business, $date, $time) {
    try {
        $mail = createMailer();
        $mail->addAddress($employeeEmail);
        $mail->isHTML(true);
        $mail->Subject = 'לקוח ברשימת המתנה';
        $mail->Body = "<p>$clientName נכנס לרשימת המתנה בסניף <strong>$business</strong> בתאריך <strong>$date</strong> בשעה <strong>$time</strong>.</p>";
        $mail->send();
    } catch (Exception $e) {}
}

// לעובדים - משתמש קבע תור
function notifyEmployeeNewAppointment($employeeEmail, $clientName, $business, $date, $time) {
    try {
        $mail = createMailer();
        $mail->addAddress($employeeEmail);
        $mail->isHTML(true);
        $mail->Subject = 'נקבע תור חדש';
        $mail->Body = "<p>$clientName קבע תור בסניף <strong>$business</strong> לתאריך <strong>$date</strong> בשעה <strong>$time</strong>.</p>";
        $mail->send();
    } catch (Exception $e) {}
}

// למשתמש ולעובד - קדמת מהמתנה
function notifyPromotion($to, $name, $business, $date, $time) {
    try {
        $mail = createMailer();
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'התור שלך קודם!';
        $mail->Body = "<p>שלום $name,<br>קודמת לרשימת התורים לסניף <strong>$business</strong> בתאריך <strong>$date</strong> בשעה <strong>$time</strong>.</p>";
        $mail->send();
    } catch (Exception $e) {}
}

// ביטול - למשתמש
function notifyCancelToUser($to, $name, $business, $date, $time) {
    try {
        $mail = createMailer();
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'התור שלך בוטל';
        $mail->Body = "<p>שלום $name, התור שלך ל־<strong>$business</strong> בתאריך <strong>$date</strong> בשעה <strong>$time</strong> בוטל.</p>";
        $mail->send();
    } catch (Exception $e) {}
}

// ביטול - לעובד
function notifyCancelToEmployee($employeeEmail, $clientName, $business, $date, $time) {
    try {
        $mail = createMailer();
        $mail->addAddress($employeeEmail);
        $mail->isHTML(true);
        $mail->Subject = 'תור בוטל';
        $mail->Body = "<p>$clientName ביטל את התור שלו לסניף <strong>$business</strong> בתאריך <strong>$date</strong> בשעה <strong>$time</strong>.</p>";
        $mail->send();
    } catch (Exception $e) {}
}


$conn->close();
?>
</div>
</body>
</html>
