<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

// ✅ שליחת מייל אישור תור למשתמש
function sendAppointmentEmailSimple($to, $name, $business, $date, $time) {
    $mail = new PHPMailer(true);
    try {
        configureMailer($mail);
        $mail->addAddress($to, $name);
        $mail->Subject = 'אישור תור';
        $mail->Body = "
            <h2>שלום $name,</h2>
            <p>התור שלך ב־<strong>$business</strong> נקבע לתאריך <strong>$date</strong> בשעה <strong>$time</strong>.</p>
            <p>נשמח לראותך!</p>
            <p style='color: gray;'>הודעה זו נשלחה אוטומטית. אין להשיב עליה.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("שגיאה בשליחת אישור תור: " . $mail->ErrorInfo);
    }
}

// ✅ שליחת מייל למשתמש שנכנס להמתנה
function sendWaitingListEmail($to, $name, $business, $date, $time, $position) {
    $mail = new PHPMailer(true);
    try {
        configureMailer($mail);
        $mail->addAddress($to, $name);
        $mail->Subject = 'נרשמת לרשימת המתנה';
        $mail->Body = "
            <h2>שלום $name,</h2>
            <p>לא ניתן היה לקבוע תור ל־<strong>$business</strong> בתאריך <strong>$date</strong> בשעה <strong>$time</strong>.</p>
            <p>נרשמת בהצלחה לרשימת המתנה. מספרך: <strong>$position</strong>.</p>
            <p>נעדכן אותך אם יתפנה מקום.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("שגיאה בשליחת מייל המתנה: " . $mail->ErrorInfo);
    }
}

// ✅ שליחת מייל לעובדים כשמשתמש נכנס לרשימת המתנה
function notifyEmployeeWaitingList($employeeEmail, $clientName, $business, $date, $time) {
    $mail = new PHPMailer(true);
    try {
        configureMailer($mail);
        $mail->addAddress($employeeEmail, 'עובד');
        $mail->Subject = 'לקוח חדש ברשימת המתנה';
        $mail->Body = "
            <h3>שלום,</h3>
            <p>הלקוח <strong>$clientName</strong> נכנס לרשימת המתנה בסניף <strong>$business</strong>.</p>
            <p>לתאריך: <strong>$date</strong>, שעה: <strong>$time</strong>.</p>
            <p>נא לטפל בהתאם ברגע שיתפנה תור.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("שגיאה בשליחת מייל לעובד על המתנה: " . $mail->ErrorInfo);
    }
}

// ✅ שליחת מייל לעובדים כשמשתמש קובע תור חדש
function notifyEmployeeAppointmentCreated($employeeEmail, $clientName, $business, $date, $time) {
    $mail = new PHPMailer(true);
    try {
        configureMailer($mail);
        $mail->addAddress($employeeEmail, 'עובד');
        $mail->Subject = 'נקבע תור חדש';
        $mail->Body = "
            <h3>שלום,</h3>
            <p>הלקוח <strong>$clientName</strong> קבע תור בסניף <strong>$business</strong>.</p>
            <p>בתאריך: <strong>$date</strong>, שעה: <strong>$time</strong>.</p>
            <p>בדוק את מערכת הניהול.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("שגיאה בשליחת מייל על תור חדש לעובד: " . $mail->ErrorInfo);
    }
}

// ✅ שליחת מייל לעובדים כשמשתמש קודם מרשימת ההמתנה
function notifyEmployeePromoted($employeeEmail, $clientName, $business, $date, $time) {
    $mail = new PHPMailer(true);
    try {
        configureMailer($mail);
        $mail->addAddress($employeeEmail, 'עובד');
        $mail->Subject = 'משתמש קודם לרשימת התורים';
        $mail->Body = "
            <h3>שלום,</h3>
            <p>הלקוח <strong>$clientName</strong> קודם לרשימת התורים בעסק <strong>$business</strong>.</p>
            <p>בתאריך: <strong>$date</strong>, בשעה: <strong>$time</strong>.</p>
            <p>נא לבדוק את מערכת הניהול לעדכון נוסף.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("שגיאה בשליחת מייל על קידום: " . $mail->ErrorInfo);
    }
}

// ✅ שליחת מייל למשתמש כאשר התור שלו בוטל
function sendCancelEmail($to, $name, $business, $date, $time) {
    $mail = new PHPMailer(true);
    try {
        configureMailer($mail);
        $mail->addAddress($to, $name);
        $mail->Subject = 'ביטול תור';
        $mail->Body = "
            <h2>שלום $name,</h2>
            <p>התור שלך ל־<strong>$business</strong> בתאריך <strong>$date</strong> בשעה <strong>$time</strong> בוטל.</p>
            <p>נשמח לעזור לך לקבוע תור חדש בכל עת.</p>
            <p style='color: gray;'>הודעה זו נשלחה אוטומטית. אין להשיב עליה.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("שגיאה בשליחת מייל ביטול תור: " . $mail->ErrorInfo);
    }
}

// ✅ שליחת מייל לעובדים כאשר משתמש ביטל תור
function notifyEmployeeCancel($employeeEmail, $clientName, $business, $date, $time) {
    $mail = new PHPMailer(true);
    try {
        configureMailer($mail);
        $mail->addAddress($employeeEmail, 'עובד');
        $mail->Subject = 'תור בוטל על ידי משתמש';
        $mail->Body = "
            <h3>שלום,</h3>
            <p>הלקוח <strong>$clientName</strong> ביטל תור בסניף <strong>$business</strong>.</p>
            <p>תאריך: <strong>$date</strong>, שעה: <strong>$time</strong>.</p>
            <p>המערכת עודכנה בהתאם.</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("שגיאה בשליחת מייל ביטול לעובד: " . $mail->ErrorInfo);
    }
}


// ✅ פונקציית קונפיגורציה אחת אחידה
function configureMailer($mail) {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'shimaa.sabe123@gmail.com';
    $mail->Password = 'unma xluu gkbz tfnx'; // סיסמת אפליקציה בלבד!
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = "UTF-8";

    $mail->setFrom('shimaa.sabe123@gmail.com', 'המערכת שלנו');
    $mail->isHTML(true);
    $mail->addReplyTo('noreply@balanced-diabetes.com', 'אין להשיב');
    $mail->addCustomHeader('X-Mailer', 'PHP Mail System');
    $mail->addCustomHeader('Precedence', 'bulk');
}
?>
