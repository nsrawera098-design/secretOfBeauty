<?php
session_start();
require_once 'connectToDB.php';
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>תוצאות חיפוש</title>
    <style>
        body {
            background: linear-gradient(to bottom, #ffe4f0, #f0f0f0);
            font-family: 'Varela Round', sans-serif;
            text-align: center;
            padding: 20px;
        }

        h2 {
            color: purple;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: hotpink;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .product {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 15px;
            width: 240px;
            text-align: center;
        }

        .product img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product h3 {
            font-size: 18px;
            margin: 10px 0 5px;
        }

        .product p {
            font-size: 16px;
            color: #444;
        }
    </style>
</head>
<body>

<?php
$search = mysqli_real_escape_string($conn, $_GET['query']);
echo "<h2>תוצאות חיפוש עבור: <span style='font-weight:bold;'>$search</span></h2>";
?>

<a href="homePage.php" class="back-link">חזרה לדף הבית</a>

<?php
function displayResults($conn, $table, $nameColumn, $search) {
    $sql = "SELECT * FROM $table WHERE $nameColumn LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<div class='product-container'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='product'>";
            echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row[$nameColumn]) . "'>";
            echo "<h3>" . htmlspecialchars($row[$nameColumn]) . "</h3>";
            echo "<p>מחיר: ₪" . $row['price'] . "</p>";
            echo "</div>";
        }
        echo "</div><br>";
        return true;
    }

    return false;
}

$found = false;
$found |= displayResults($conn, "products_cosmetics", "productco_name", $search);
$found |= displayResults($conn, "products_hair", "product_name", $search);
$found |= displayResults($conn, "products_nails", "productN_name", $search);

if (!$found) {
    echo "<p>❌ לא נמצאו מוצרים התואמים לחיפוש שלך.</p>";
}
?>

</body>
</html>
