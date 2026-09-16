<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function checkStockAndSendEmail() {
    $conn = new mysqli("localhost", "root", "", "user");
    if ($conn->connect_error) {
        error_log(date("Y-m-d H:i:s") . " ❌ Database connection failed: " . $conn->connect_error . "\n", 3, "log_run.txt");
        return;
    }

    $tables = [
        'products_cosmetics' => 'productco_name',
        'products_hair' => 'product_name',
        'products_nails' => 'productN_name'
    ];

    $lowStockMsg = "📦 Low Stock Alerts:\n\n";
    $hasLowStock = false;

    foreach ($tables as $table => $nameField) {
        $sql = "SELECT $nameField AS name, quantity FROM $table WHERE quantity <= 5";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $hasLowStock = true;
            while ($row = $result->fetch_assoc()) {
                $product = $row["name"];
                $quantity = $row["quantity"];

                if ($quantity == 0) {
                    $lowStockMsg .= "❌ Product '$product' in table '$table' is OUT OF STOCK!\n";
                } else {
                    $lowStockMsg .= "⚠️ Product '$product' in table '$table' has LOW stock: $quantity units\n";
                }
            }
        }
    }

    if ($hasLowStock) {
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'shimaa.sabe123@gmail.com';
            $mail->Password = 'unma xluu gkbz tfnx';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('alert@balanced-diabetes.com', 'Stock Alert System');
            $mail->addAddress('shimaa.sabe123@gmail.com');

            $mail->Subject = '🚨 Low Stock Alert!';
            $mail->Body    = $lowStockMsg;

            $mail->send();

            error_log(date("Y-m-d H:i:s") . " ✅ Email sent successfully.\n", 3, "log_run.txt");
        } catch (Exception $e) {
            error_log(date("Y-m-d H:i:s") . " ❌ Email send failed: {$mail->ErrorInfo}\n", 3, "log_run.txt");
        }
    } else {
        error_log(date("Y-m-d H:i:s") . " ℹ️ No low stock.\n", 3, "log_run.txt");
    }

    $conn->close();
}

// ✨✨ מאוד חשוב: לקרוא לפונקציה!! ✨✨
checkStockAndSendEmail();
?>

