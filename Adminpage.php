<?php
session_start();
include("connectToDB.php"); 
include('navbar.php');

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
$email = $_SESSION['email'];
$query = "SELECT role FROM user WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$user = $result->fetch_assoc();
if ($user['role'] != 1) {
    echo "<h2>אין לך הרשאה לגשת לדף זה. עמוד זה מיועד למנהלים בלבד.</h2>";
    exit();
}
$low_stock_items = [];
$tables = [
    ['table' => 'products_hair', 'name_col' => 'product_name'],
    ['table' => 'products_cosmetics', 'name_col' => 'productco_name'],
    ['table' => 'products_nails', 'name_col' => 'productN_name']
];

foreach ($tables as $t) {
    $sql = "SELECT {$t['name_col']}, quantity FROM {$t['table']} WHERE quantity < 3";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $low_stock_items[] = $row[$t['name_col']] . " (כמות: " . $row['quantity'] . ")";
        }
    }
}

if (!empty($low_stock_items)) {
    echo "<div style='background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; margin: 20px auto; width: 80%; border-radius: 10px; text-align: center; font-weight: bold;'>";
    echo "⚠️ שימו לב! המוצרים הבאים במלאי נמוך:<br>";
    echo implode("<br>", $low_stock_items);
    echo "</div>";
}

?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>דף ניהול</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #ffe6f0, #f9d8e7);
            margin: 0;
            padding: 0;
            text-align: center;
        }

        header {
            background-color: #fbd3e9;
            padding: 60px 20px;
        }

        header h1 {
            color: #80004d;
            font-size: 40px;
            margin-bottom: 10px;
        }

        header p {
            font-size: 18px;
            color: #555;
        }

        .admin-options {
            margin: 40px auto;
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .admin-btn {
            background-color: #ff99cc;
            color: white;
            padding: 20px 30px;
            border: none;
            border-radius: 30px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .admin-btn:hover {
            background-color: #ff66aa;
        }

        footer {
            margin-top: 60px;
            padding: 20px;
            font-size: 14px;
            color: #666;
        }

        @media (max-width: 600px) {
            .admin-options {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>ברוכה הבאה לדף הניהול 💼</h1>
        <p>כאן תוכלי לנהל עובדים, לקוחות, מוצרים ולצפות בסטטיסטיקות</p>
    </header>

    <div class="admin-options">
        <a href="manage_users.php"><button class="admin-btn">👥 ניהול עובדים ולקוחות</button></a>
        <a href="admin_products.php"><button class="admin-btn">🛍️ ניהול מוצרים</button></a>
        <a href="admain.analytics.php"><button class="admin-btn">📊 ניתוחים וסטטיסטיקות</button></a>
    </div>

    <footer>
        &copy; 2025 מערכת ניהול יופי
    </footer>
</body>
</html>