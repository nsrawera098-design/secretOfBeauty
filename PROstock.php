<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/connectToDB.php';

function checkStockAndSendEmail($conn)
{
    $tables = [
        'Products_cosmetics' => 'productco_name',
        'Products_hair'      => 'product_name',
        'Products_nails'     => 'productN_name'
    ];

    $lowStockMsg = "Low Stock Alerts:\n\n";
    $hasLowStock = false;

    foreach ($tables as $table => $nameField) {

        $sql = "
            SELECT $nameField AS name, quantity
            FROM $table
            WHERE quantity <= 5
        ";

        $result = $conn->query($sql);

        if (!$result) {
            error_log(
                date("Y-m-d H:i:s")
                . " Database query failed for table "
                . $table
                . "\n",
                3,
                __DIR__ . "/log_run.txt"
            );

            continue;
        }

        while ($row = $result->fetch_assoc()) {

            $hasLowStock = true;

            $product = $row['name'];
            $quantity = (int)$row['quantity'];

            if ($quantity === 0) {

                $lowStockMsg .=
                    "OUT OF STOCK: "
                    . $product
                    . " ("
                    . $table
                    . ")\n";

            } else {

                $lowStockMsg .=
                    "LOW STOCK: "
                    . $product
                    . " - "
                    . $quantity
                    . " units\n";
            }
        }
    }


    if (!$hasLowStock) {

        error_log(
            date("Y-m-d H:i:s")
            . " No low stock.\n",
            3,
            __DIR__ . "/log_run.txt"
        );

        return;
    }


    $mail = new PHPMailer(true);

    try {

        configureMailer($mail);

        $adminEmail = getenv('STOCK_ALERT_EMAIL');

        if (!$adminEmail) {
            throw new Exception(
                'STOCK_ALERT_EMAIL is not configured.'
            );
        }

        $mail->addAddress($adminEmail);

        $mail->Subject = 'Low Stock Alert';

        $mail->Body =
            nl2br(
                htmlspecialchars(
                    $lowStockMsg
                )
            );

        $mail->AltBody = $lowStockMsg;

        $mail->send();


        error_log(
            date("Y-m-d H:i:s")
            . " Stock alert email sent successfully.\n",
            3,
            __DIR__ . "/log_run.txt"
        );

    } catch (Exception $e) {

        error_log(
            date("Y-m-d H:i:s")
            . " Email send failed.\n",
            3,
            __DIR__ . "/log_run.txt"
        );
    }
}


checkStockAndSendEmail($conn);

$conn->close();

?>

