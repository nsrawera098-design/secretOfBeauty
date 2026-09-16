<?php
session_start();
require_once 'ConnectToDB.php';
if (!isset($_SESSION['username'])) {
    die("❌ שגיאה: עליך להיות מחובר כדי להוסיף מוצרים לעגלה.");
}
$username = $_SESSION['username'];

if (isset($_GET['product_id'], $_GET['quantity'], $_GET['product_type'])) {
    $product_id = $_GET['product_id'];
    $quantity = (int)$_GET['quantity'];
    $product_type = $_GET['product_type'];

    if ($quantity < 1) {
        die("❌ שגיאה: הכמות חייבת להיות לפחות 1.");
    }
    $sql = "SELECT quantity FROM cart 
            WHERE name = '$username' AND product_id = '$product_id' AND product_type = '$product_type'";
    $result = $conn->query($sql);

    if (!$result) {
        die("❌ שגיאה בשליפת המוצר מהעגלה: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;
        $update_sql = "UPDATE cart 
                       SET quantity = '$new_quantity' 
                       WHERE name = '$username' AND product_id = '$product_id' AND product_type = '$product_type'";

        if (!$conn->query($update_sql)) {
            die("❌ שגיאה בעדכון הכמות: " . $conn->error);
        }
    } else {
        $insert_sql = "INSERT INTO cart (name, product_id, quantity, product_type) 
                       VALUES ('$username', '$product_id', '$quantity', '$product_type')";

        if (!$conn->query($insert_sql)) {
            die("❌ שגיאה בהוספת המוצר לעגלה: " . $conn->error);
        }
    }
    header("Location: cart.php");
    exit();
} else {
    die("❌ שגיאה: חסר מידע - ודא שהמוצר, הכמות וסוג המוצר נשלחים בפורמט תקין.");
}
?>
