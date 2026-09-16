<?php
session_start();
require 'connectToDB.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// שליפת כל המוצרים מהעגלה
$stmt = $conn->prepare("SELECT * FROM cart WHERE name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$lowStockProducts = [];

while ($row = $result->fetch_assoc()) {
    $product_id = $row['product_id'];
    $quantity = $row['quantity'];
    $product_type = $row['product_type'];

    if ($product_type == "hair") {
        $table = "products_hair";
        $id_field = "product_id";
        $name_field = "product_name";
    } elseif ($product_type == "nails") {
        $table = "products_nails";
        $id_field = "productN_id";
        $name_field = "productN_name";
    } elseif ($product_type == "cosmetics") {
        $table = "products_cosmetics";
        $id_field = "productco_id";
        $name_field = "productco_name";
    } else {
        continue;
    }

    // שליפת מחיר ומלאי נוכחי
    $query = "SELECT $name_field, quantity FROM $table WHERE $id_field = ?";
    $prod_stmt = $conn->prepare($query);
    $prod_stmt->bind_param("i", $product_id);
    $prod_stmt->execute();
    $prod_result = $prod_stmt->get_result();
    $product = $prod_result->fetch_assoc();

    if (!$product) continue;

    $product_name = $product[$name_field];
    $current_stock = $product['quantity'];

    // האם יש מספיק מלאי
    if ($current_stock < $quantity) {
        die("<h2 style='color:red;'>❌ אין מספיק מלאי עבור $product_name.</h2>");
    }

    // עדכון מלאי
    $new_quantity = $current_stock - $quantity;
    $update = $conn->prepare("UPDATE $table SET quantity = ? WHERE $id_field = ?");
    $update->bind_param("ii", $new_quantity, $product_id);
    $update->execute();

    // אם מלאי קטן מ-5 – מוסיפים לרשימת המייל
    if ($new_quantity <= 5) {
        $lowStockProducts[] = [
            'name' => $product_name,
            'quantity' => $new_quantity,
            'table' => $table
        ];
    }
}

// ניקוי עגלה אחרי הקנייה
$delete = $conn->prepare("DELETE FROM cart WHERE name = ?");
$delete->bind_param("s", $username);
$delete->execute();

// שליחת מייל אם צריך
if (!empty($lowStockProducts)) {
    sendLowStockEmail($lowStockProducts);
}

// ✅ הפניה אוטומטית הביתה
header("Location: homePage.php");
exit();


// פונקציה לשליחת מייל
function sendLowStockEmail($products) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'shimaa.sabe123@gmail.com';
        $mail->Password = 'unma xluu gkbz tfnx'; // סיסמת אפליקציה
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = "UTF-8";

        $mail->setFrom('alert@balanced-diabetes.com', 'Beauty Secret');
        $mail->addAddress('shimaa.sabe123@gmail.com');

        $mail->Subject = '🚨 התראה: מלאי נמוך/אזל';
        $body = "📦 שלום,\n\n";
        $body .= "המוצרים הבאים במלאי נמוך:\n\n";
        foreach ($products as $p) {
            if ($p['quantity'] == 0) {
                $body .= "❌ המוצר '{$p['name']}' בטבלה '{$p['table']}' אזל מהמלאי.\n";
            } else {
                $body .= "⚠️ המוצר '{$p['name']}' בטבלה '{$p['table']}' נשארו רק {$p['quantity']} יחידות.\n";
            }
        }
        $body .= "\n📢 נא לבצע הזמנת מלאי חדשה.";

        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("שגיאה בשליחת מייל: {$mail->ErrorInfo}");
    }
}
?>