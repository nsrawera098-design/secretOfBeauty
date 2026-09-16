<?php
require_once 'connectToDB.php';
include('navbar.php'); 
include_once 'PROstock.php';
checkStockAndSendEmail();

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$showAll = isset($_GET['show']) && $_GET['show'] === 'all';

// ✅ אם לא ביקש להציג את כל המוצרים – נבדוק אם עשה אבחון
if (!$showAll) {
    $checkQuery = "SELECT * FROM skin_diagnosis WHERE email = '$email' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult) {
        if (mysqli_num_rows($checkResult) > 0) {
            header("Location: skin_result.php");
            exit();
        } else {
            header("Location: skin_diagnosis.php");
            exit();
        }
    } else {
        die("שגיאה בבדיקת אבחון: " . mysqli_error($conn));
    }
}
// בודק אם יש מוצרים – רק אם אין, מוסיף אותם
$check_sql = "SELECT COUNT(*) as count FROM Products_cosmetics";
$result_check = $conn->query($check_sql);
$row_check = $result_check->fetch_assoc();

if ($row_check['count'] == 0) {
    $insert_sql = "INSERT INTO Products_cosmetics (productco_name, quantity, price, image) VALUES
        ('Dr. Kadir Hemp Moisturizing Cream', 20, 99.90, 'photos/cream1.jpg'),
        ('Dr. Kadir Creative Nourishing Cream', 15, 175.00, 'photos/cream2.png'),
        ('Dr. Kadir New Collagen Moisturizer', 10, 336.00, 'photos/cream3.png'),
        ('KB Pure Hydrating Face Cream', 25, 120.00, 'photos/cream4.jpg'),
        ('HL Always Active Alpha-Beta & Retinol Night Cream', 12, 150.00, 'photos/cream5.jpg'),
        ('Christina BioPhyto Herbal Complex Cream', 18, 130.00, 'photos/CHR6.webp');";

if ($conn->query($insert_sql) === FALSE) {//בדיקה אם הכנסה הצליחה
    die("Error inserting data: " . $conn->error);
}
}  

// אם כן הגיע לפה – נציג את כל המוצרים
$select_sql = "SELECT * FROM Products_cosmetics ORDER BY price DESC";
$result = $conn->query($select_sql);
if ($result === FALSE) {
    die("SQL Error: " . $conn->error);
}
?>



<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>כל מוצרי הקוסמטיקה</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('photos/cosmi8.png') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            text-align: center;
            padding: 20px;
        }

        h1 {
            font-size: 30px;
            margin-top: 20px;
            color: #2c3e50;
        }

        .buttons-bar {
            margin: 20px 0;
        }

        .buttons-bar a {
            display: inline-block;
            background-color: #ffafcc;
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            margin: 6px;
            font-weight: bold;
        }

        .buttons-bar a:hover {
            background-color: #f78fb3;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 20px;
        }

        .product {
            background: white;
            border-radius: 16px;
            padding: 20px;
            width: 250px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .product:hover {
            transform: translateY(-5px);
        }

        .product img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .product h3 {
            font-size: 18px;
            color: #2c3e50;
        }

        .product p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }

        .product form {
            margin-top: 10px;
        }

        .quantity-input {
            width: 50px;
            padding: 5px;
            border-radius: 6px;
            text-align: center;
        }

        .product button {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 8px;
            font-weight: bold;
        }

        .product button:hover {
            background: #218838;
        }

        .out-of-stock {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>



<h1>💄 כל מוצרי הקוסמטיקה</h1>

<div class="buttons-bar">
    <a href="homePage.php">🏠 חזרה לדף הבית</a>
    <a href="cart.php">🛒 עגלת קניות</a>
    <a href="skin_result.php">🎯 חזרה למוצרים מותאמים</a>
</div>

<div class="product-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product">
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['productco_name']) ?>">
                <h3><?= htmlspecialchars($row['productco_name']) ?></h3>
                <p>מחיר: ₪<?= number_format($row['price'], 2) ?></p>
                <?php if ($row['quantity'] > 0): ?>
                    <p>כמות במלאי: <?= $row['quantity'] ?></p>
                    <form method="get" action="addToCart.php">
                        <input type="hidden" name="product_id" value="<?= $row['productco_id'] ?>">
                        <input type="hidden" name="product_type" value="cosmetics">
                        <label>כמות:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?= $row['quantity'] ?>" class="quantity-input">
                        <br>
                        <button type="submit">➕ הוסף לעגלה</button>
                    </form>
                <?php else: ?>
                    <p class="out-of-stock">❌ אזל מהמלאי</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>🚫 לא נמצאו מוצרים להצגה.</p>
    <?php endif; ?>
</div>

</body>
</html>