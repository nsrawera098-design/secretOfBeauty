<?php
session_start();
include 'connectToDB.php';
include('navbar.php');

if (!isset($_SESSION['email'])) {
    echo "לא נמצאה כתובת אימייל במערכת.";
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$query = "SELECT * FROM skin_diagnosis WHERE email = '$email' ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    // אם אין אבחון – מפנה את המשתמש למילוי אבחון
    header("Location: skin_diagnosis.php");
    exit();
}


$data = mysqli_fetch_assoc($result);
$skinType = $data['skin_type'];

$productsQuery = "SELECT * FROM Products_cosmetics WHERE skin_type = '$skinType'";
$productsResult = mysqli_query($conn, $productsQuery);
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>תוצאת אבחון עור</title>
    <style>
        body {
            font-family: 'Heebo', sans-serif;
            background: url('photos/skiin.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 40px;
            font-size: 28px;
            color: #2c3e50;
        }

        .top-bar {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
            color: #333;
        }

        .buttons-bar {
            text-align: center;
            margin-top: 15px;
        }

        .buttons-bar a {
            display: inline-block;
            background-color: #ffafcc;
            color: #fff;
            padding: 10px 20px;
            margin: 6px;
            border-radius: 12px;
            text-decoration: none;
            transition: 0.3s;
            font-weight: bold;
        }

        .buttons-bar a:hover {
            background-color: #f78fb3;
        }

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 30px;
        }

        .product-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            width: 250px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease-in-out;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .product-card h4 {
            margin: 10px 0 5px;
            font-size: 18px;
            color: #2c3e50;
        }

        .product-card p {
            margin: 4px 0;
            color: #555;
        }

        .add-form {
            margin-top: 10px;
        }

        .add-form input[type="number"] {
            width: 50px;
            padding: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .add-form button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            margin-top: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .add-form button:hover {
            background-color: #218838;
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

    <h2>🔒 מוצרים מותאמים לעורך</h2>

    <div class="top-bar">
        שם: <?= htmlspecialchars($data['full_name']) ?> | סוג עור: <?= htmlspecialchars($skinType) ?>
    </div>

    <div class="buttons-bar">
        <a href="homePage.php">🔙 חזרה לדף הבית</a>
        <a href="cart.php">🛒 עגלת קניות</a>
        <a href="update_skin_diagnosis.php?email=<?= urlencode($email) ?>">🔁 ערוך אבחון</a>
        <a href="pro_cosmetics.php?show=all">👁️ הצג את כל המוצרים</a>
        </div>

    <div class="products">
        <?php if ($productsResult && mysqli_num_rows($productsResult) > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($productsResult)): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="מוצר">
                    <h4><?= htmlspecialchars($product['productco_name']) ?></h4>
                    <p>מחיר: ₪<?= number_format($product['price'], 2) ?></p>
                    <p>כמות במלאי: <?= $product['quantity'] ?></p>

                    <form method="get" action="addToCart.php" class="add-form">
                        <input type="hidden" name="product_id" value="<?= $product['productco_id'] ?>">
                        <input type="hidden" name="product_type" value="cosmetics">
                        <label>כמות:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>">
                        <br>
                        <button type="submit">➕ הוסף לעגלה</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>לא נמצאו מוצרים מתאימים עבורך.</p>
        <?php endif; ?>
    </div>
</body>
</html>
