<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';


// ==========================================
// Mail configuration
// ==========================================

function configureMailer(PHPMailer $mail): void
{
    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // Read credentials from environment variables
    $mail->Username = getenv('MAIL_USERNAME');
    $mail->Password = getenv('MAIL_PASSWORD');

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom(
        getenv('MAIL_USERNAME'),
        'Belissa'
    );

    $mail->isHTML(true);
}


// ==========================================
// Appointment confirmation
// ==========================================

function sendAppointmentEmailSimple(
    $to,
    $name,
    $business,
    $date,
    $time
) {
    $mail = new PHPMailer(true);

    try {

        configureMailer($mail);

        $mail->addAddress($to, $name);

        $mail->Subject = 'אישור תור';

        $mail->Body = "
            <h2>שלום " . htmlspecialchars($name) . ",</h2>

            <p>
                התור שלך ב־
                <strong>" . htmlspecialchars($business) . "</strong>
                נקבע לתאריך
                <strong>" . htmlspecialchars($date) . "</strong>
                בשעה
                <strong>" . htmlspecialchars($time) . "</strong>.
            </p>

            <p>נשמח לראותך!</p>

            <p style='color: gray;'>
                הודעה זו נשלחה אוטומטית.
            </p>
        ";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log(
            "Appointment email error: "
            . $mail->ErrorInfo
        );

        return false;
    }
}


// ==========================================
// Waiting list email
// ==========================================

function sendWaitingListEmail(
    $to,
    $name,
    $business,
    $date,
    $time,
    $position
) {
    $mail = new PHPMailer(true);

    try {

        configureMailer($mail);

        $mail->addAddress($to, $name);

        $mail->Subject = 'נרשמת לרשימת המתנה';

        $mail->Body = "
            <h2>שלום " . htmlspecialchars($name) . ",</h2>

            <p>
                לא ניתן היה לקבוע תור ל־
                <strong>" . htmlspecialchars($business) . "</strong>
                בתאריך
                <strong>" . htmlspecialchars($date) . "</strong>
                בשעה
                <strong>" . htmlspecialchars($time) . "</strong>.
            </p>

            <p>
                נרשמת בהצלחה לרשימת ההמתנה.
                מספרך:
                <strong>" . (int)$position . "</strong>
            </p>

            <p>
                נעדכן אותך אם יתפנה מקום.
            </p>
        ";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log(
            "Waiting list email error: "
            . $mail->ErrorInfo
        );

        return false;
    }
}


// ==========================================
// Notify employee about waiting list
// ==========================================

function notifyEmployeeWaitingList(
    $employeeEmail,
    $clientName,
    $business,
    $date,
    $time
) {
    $mail = new PHPMailer(true);

    try {

        configureMailer($mail);

        $mail->addAddress(
            $employeeEmail,
            'עובד'
        );

        $mail->Subject =
            'לקוח חדש ברשימת המתנה';

        $mail->Body = "
            <h3>שלום,</h3>

            <p>
                הלקוח
                <strong>" . htmlspecialchars($clientName) . "</strong>
                נכנס לרשימת המתנה בסניף
                <strong>" . htmlspecialchars($business) . "</strong>.
            </p>

            <p>
                תאריך:
                <strong>" . htmlspecialchars($date) . "</strong>
                שעה:
                <strong>" . htmlspecialchars($time) . "</strong>
            </p>
        ";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log(
            "Employee waiting-list email error: "
            . $mail->ErrorInfo
        );

        return false;
    }
}


// ==========================================
// Notify employee about new appointment
// ==========================================

function notifyEmployeeAppointmentCreated(
    $employeeEmail,
    $clientName,
    $business,
    $date,
    $time
) {
    $mail = new PHPMailer(true);

    try {

        configureMailer($mail);

        $mail->addAddress(
            $employeeEmail,
            'עובד'
        );

        $mail->Subject = 'נקבע תור חדש';

        $mail->Body = "
            <h3>שלום,</h3>

            <p>
                הלקוח
                <strong>" . htmlspecialchars($clientName) . "</strong>
                קבע תור בסניף
                <strong>" . htmlspecialchars($business) . "</strong>.
            </p>

            <p>
                בתאריך:
                <strong>" . htmlspecialchars($date) . "</strong>
                שעה:
                <strong>" . htmlspecialchars($time) . "</strong>
            </p>
        ";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log(
            "Employee appointment email error: "
            . $mail->ErrorInfo
        );

        return false;
    }
}


// ==========================================
// Notify employee about promoted waiting user
// ==========================================

function notifyEmployeePromoted(
    $employeeEmail,
    $clientName,
    $business,
    $date,
    $time
) {
    $mail = new PHPMailer(true);

    try {

        configureMailer($mail);

        $mail->addAddress(
            $employeeEmail,
            'עובד'
        );

        $mail->Subject =
            'משתמש קודם לרשימת התורים';

        $mail->Body = "
            <h3>שלום,</h3>

            <p>
                הלקוח
                <strong>" . htmlspecialchars($clientName) . "</strong>
                קודם לרשימת התורים בעסק
                <strong>" . htmlspecialchars($business) . "</strong>.
            </p>

            <p>
                בתאריך:
                <strong>" . htmlspecialchars($date) . "</strong>
                בשעה:
                <strong>" . htmlspecialchars($time) . "</strong>.
            </p>
        ";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log(
            "Promotion email error: "
            . $mail->ErrorInfo
        );

        return false;
    }
}


// ==========================================
// Cancellation email to user
// ==========================================

function sendCancelEmail(
    $to,
    $name,
    $business,
    $date,
    $time
) {
    $mail = new PHPMailer(true);

    try {

        configureMailer($mail);

        $mail->addAddress($to, $name);

        $mail->Subject = 'ביטול תור';

        $mail->Body = "
            <h2>שלום " . htmlspecialchars($name) . ",</h2>

            <p>
                התור שלך ל־
                <strong>" . htmlspecialchars($business) . "</strong>
                בתאריך
                <strong>" . htmlspecialchars($date) . "</strong>
                בשעה
                <strong>" . htmlspecialchars($time) . "</strong>
                בוטל.
            </p>

            <p>
                נשמח לעזור לך לקבוע תור חדש.
            </p>
        ";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log(
            "Cancellation email error: "
            . $mail->ErrorInfo
        );

        return false;
    }
}


// ==========================================
// Cancellation notification to employee
// ==========================================

function notifyEmployeeCancel(
    $employeeEmail,
    $clientName,
    $business,
    $date,
    $time
) {
    $mail = new PHPMailer(true);

    try {

        configureMailer($mail);

        $mail->addAddress(
            $employeeEmail,
            'עובד'
        );

        $mail->Subject =
            'תור בוטל על ידי משתמש';

        $mail->Body = "
            <h3>שלום,</h3>

            <p>
                הלקוח
                <strong>" . htmlspecialchars($clientName) . "</strong>
                ביטל תור בסניף
                <strong>" . htmlspecialchars($business) . "</strong>.
            </p>

            <p>
                תאריך:
                <strong>" . htmlspecialchars($date) . "</strong>
                שעה:
                <strong>" . htmlspecialchars($time) . "</strong>
            </p>
        ";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log(
            "Employee cancellation email error: "
            . $mail->ErrorInfo
        );

        return false;
    }
}

?>
