<?php
session_start();
include 'connectToDB.php';
include('navbar.php');

if (!isset($_SESSION['email'])) {
    echo "לא נמצאה כתובת אימייל במערכת.";
    exit();
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$query = "SELECT * FROM hair_diagnosis WHERE email = '$email' ORDER BY created_at DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    // אם אין אבחון – מפנה את המשתמש למילוי אבחון
    header("Location: diagnose_hair.php");
    exit();
}


$data = mysqli_fetch_assoc($result);
$hairType = $data['hair_type'];

$productsQuery = "SELECT * FROM Products_hair WHERE hair_type = '$hairType'";
$productsResult = mysqli_query($conn, $productsQuery);
$name = $_SESSION['name'] ?? 'לקוח/ה';

$goal = $data['goal'] ?? '';
$hair_type = $data['hair_type'] ?? '';
$uses_heat_tools = $data['uses_heat_tools'] ?? '';

$products = [];

if (strpos($goal, 'הגנה על צבע') !== false) {
    $products[] = [
        'id' => 3,
        'name' => "COLOR E'CLAT Shampoo",
        'price' => 139.00,
        'amount' => 15,
        'image' => 'photos/shampo2.webp'
    ];
}
if (strpos($goal, 'שיקום עמוק') !== false || $uses_heat_tools === 'כן') {
    $products[] = [
        'id' => 2,
        'name' => "OLAPLEX Number 3",
        'price' => 160.99,
        'amount' => 60,
        'image' => 'photos/44.webp'
    ];
}
if ($goal === 'שגרת טיפוח' && ($hair_type === 'גלי' || $hair_type === 'רגיל')) {
    $products[] = [
        'id' => 5,
        'name' => "Mielle SHAMPOO",
        'price' => 99.99,
        'amount' => 50,
        'image' => 'photos/shampo1.avif'
    ];
}
if (empty($products)) {
    $products[] = [
        'id' => 1,
        'name' => "OLAPLEX SHAMPOO - Mini",
        'price' => 180.99,
        'amount' => 20,
        'image' => 'photos/shampo4.avif'
    ];
}



?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>מוצרים מותאמים לשיערך</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Varela+Round&display=swap">
    <style>
body {
    background: url('photos/haiir1.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Varela Round', sans-serif;
    direction: rtl;
    text-align: center;
    padding: 30px;
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}

h2 {
    font-size: 30px;
    color: #222;
    margin-bottom: 20px;
}

.top-buttons {
    margin: 20px 0;
}

.btn {
    display: inline-block;
    margin: 10px;
    padding: 12px 25px;
    font-size: 16px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s ease;
    border: none;
    cursor: pointer;
}

.pink { background-color: #f78fb3; color: white; }
.pink:hover { background-color: #ff7ba9; }

.blue { background-color: #88c2ff; color: white; }
.blue:hover { background-color: #74b2ef; }

.green { background-color: #28a745; color: white; }
.green:hover { background-color: #218838; }

.products-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 25px;
    margin-top: 40px;
}

.card {
    background-color: #fff;
    border-radius: 16px;
    padding: 20px;
    width: 300px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
}

.card img {
    max-width: 100%;
    border-radius: 12px;
    margin-bottom: 15px;
}

.card h3 {
    font-size: 20px;
    color: #d81b60;
    margin-bottom: 10px;
}

.card p {
    font-size: 14px;
    color: #555;
    margin-bottom: 10px;
}

.quantity-select {
    width: 60px;
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin: 10px 0;
}

.card button {
    background: linear-gradient(to right, #ec407a, #d81b60);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.card button:hover {
    background: linear-gradient(to right, #d81b60, #ad1457);
}

    </style>
</head>
<body>

    <h2>🧴 מוצרים מותאמים לשיערך <span>🔒</span></h2>
    <p>שם: <?= htmlspecialchars($data['full_name'])  ?>  | סוג שיער:    <?= htmlspecialchars($hair_type) ?>  |    מטרה:    <?= htmlspecialchars($goal) ?></p>

    <div class="top-buttons">
        <a href="products_hair.php?show=all" class="btn pink">👁️ הצג את כל המוצרים</a>
        <a href="edit_hair_diagnosis.php" class="btn pink">✏️ערוך אבחון</a>
        <a href="cart.php" class="btn pink">🛒 עגלת קניות</a>
        <a href="homePage.php" class="btn pink">חזרה לדף הבית ⬅️</a>
    </div>

    <div class="products-container">
        <?php foreach ($products as $product): ?>
            <div class="card">
                <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                <h3><?= $product['name'] ?></h3>
                <p>מחיר: <?= number_format($product['price'], 2) ?> ₪</p>
                <p>כמות במ"ל: <?= $product['amount'] ?></p>
<form method="get" action="addToCart.php" class="add-form">
    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
    <input type="hidden" name="product_type" value="hair">
    <label>כמות:</label>
    <input type="number" name="quantity" value="1" min="1" max="<?= $product['amount'] ?>">
    <br>
   <button type="submit" class="btn green">הוסף לעגלה ➕</button>
</form>


 

            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
