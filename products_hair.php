<?php
session_start();
require_once 'connectToDB.php';
include('navbar.php');
include_once 'PROstock.php';
checkStockAndSendEmail();

// אם המשתמש לא מחובר
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$showAll = isset($_GET['show']) && $_GET['show'] === 'all';

// בדיקת אם המשתמש עבר אבחון שיער
// ✅ אם לא ביקש להציג את כל המוצרים – נבדוק אם עשה אבחון שיער
if (!$showAll) {
    $checkQuery = "SELECT * FROM hair_diagnosis WHERE email = '$email' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkQuery);

    if ($checkResult) {
        if (mysqli_num_rows($checkResult) > 0) {
            header("Location: hair_result.php");
            exit();
        } else {
            header("Location: diagnose_hair.php");
            exit();
        }
    } else {
        die("שגיאה בבדיקת אבחון: " . mysqli_error($conn));
    }
}
// הוספת מוצרים אם רשימת המלאי ריקה
$check_sql = "SELECT COUNT(*) as count FROM Products_hair";
$result_check = $conn->query($check_sql);
$row_check = $result_check->fetch_assoc();

if ($row_check['count'] == 0) {
    $insert_sql = "INSERT INTO Products_hair (product_name, quantity, price, image) VALUES
        ('Mielle SHAMPOO, (355 ml)', 10, 99.99, 'photos/shampo1.avif'),
        ('COLOR E''CLAT', 50, 139.00 , 'photos/shampo2.webp'),
        ('OLAPLEX SHAMPOO', 10, 240.99, 'photos/shampo3.webp'),
        ('OLAPLEX NUMBER 3', 60, 160.99, 'photos/44.webp'),
        ('OLAPLEX SHAMPOO - Mini', 10, 180.99, 'photos/shampo4.avif');";

    $conn->query($insert_sql); // תפעיל רק אם רוצים להכניס מוצרים פעם אחת
}

// שליפת המוצרים מהמסד
$select_sql = "SELECT * FROM Products_hair ORDER BY price DESC";
$result = $conn->query($select_sql);

if ($result === FALSE) {
    die("SQL Error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
 <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('photos/hai3.jpg') no-repeat center center fixed;
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
            background-color: #e76594;
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            margin: 6px;
            font-weight: bold;
        }

        .buttons-bar a:hover {
            background-color: #ff7ba9;
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
        .buttons-bar {
    margin: 20px 0;
}

.buttons-bar a {
    display: inline-block;
    background-color: #e76594;   /* צבע ורוד כהה */
    color: white;
    padding: 10px 20px;
    border-radius: 12px;
    text-decoration: none;
    margin: 6px;
    font-weight: bold;
}

.buttons-bar a:hover {
    background-color: #ff7ba9; /* ורוד בהיר בהובר */
}

    </style>
</head>
<body>

<div class="search-header">
  <div class="search-container">
    <form action="search.php" method="GET" class="search-bar">
      <input type="text" name="query" placeholder="חפשי מוצר..." required>
      <button type="submit">🔍</button>
    </form>
  </div>
</div>

<h1>🧴 מוצרי שיער מותאמים</h1>

<div class="buttons-bar">
    <a href="homePage.php">🏠 חזרה לדף הבית</a>
    <a href="cart.php">🛒 עגלת קניות</a>
    <a href="hair_result.php">🎯 חזרה למוצרים מותאמים</a>
</div>


    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="product">';
                echo '<img src="' . $row['image'] . '" alt="' . htmlspecialchars($row['product_name']) . '">';
                echo '<h3>' . htmlspecialchars($row['product_name']) . '</h3>';
                echo '<p>מחיר: ₪' . $row['price'] . '</p>';

                if ($row['quantity'] > 0) {
                    echo '<p>כמות במלאי: ' . $row['quantity'] . '</p>';
                    echo '<form method="get" action="addToCart.php">';
                    echo '<input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                    echo '<input type="hidden" name="product_type" value="hair">';
                    echo '<label for="quantity_' . $row['product_id'] . '">כמות:</label>';
                    echo '<input type="number" id="quantity_' . $row['product_id'] . '" class="quantity-input" name="quantity" value="0" min="0" max="' . $row['quantity'] . '">';
                    echo '<button type="submit">➕ הוסף לעגלה</button>';
                    echo '</form>';
                } else {
                    echo '<p class="out-of-stock">❌ אזל מהמלאי</p>';
                }

                echo '</div>';
            }
        } else {
            echo '<p>🚫 לא נמצאו מוצרים במערכת.</p>';
        }
        ?>
    </div>
</body>
</html>
