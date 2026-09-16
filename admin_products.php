<?php
session_start();
require_once 'connectToDB.php';
include('navbar.php');
$category = $_GET['category'] ?? 'hair';
$tableMap = [
    'hair' => ['table' => 'Products_hair', 'id' => 'product_id', 'name' => 'product_name'],
    'cosmetics' => ['table' => 'Products_cosmetics', 'id' => 'productco_id', 'name' => 'productco_name'],
    'nails' => ['table' => 'Products_nails', 'id' => 'productN_id', 'name' => 'productN_name']
];

if (!isset($tableMap[$category])) {
    die("❌ קטגוריה לא חוקית");
}
$table = $tableMap[$category]['table'];
$id_col = $tableMap[$category]['id'];
$name_col = $tableMap[$category]['name'];
$sql = "SELECT * FROM $table ORDER BY $id_col ASC";
$result = $conn->query($sql);
if (!$result) {
    die("❌ שגיאה בשליפת מוצרים: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>Admain Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            text-align: center;
        }

        header {
            background-color: rgb(206, 167, 213);
            color: white;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        main {
            padding: 20px;
        }

        .category-menu a {
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
            color: <?= $category === 'hair' ? 'blue' : '#333' ?>;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color: pink;
            color: white;
        }

        img {
            width: 50px;
            height: 50px;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .update-btn {
            background-color: pink;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .category-menu {
            margin-bottom: 20px;
        }
        .form-container {
            width: 400px;
            margin: 0 auto 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            text-align: right;
}

.form-container form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.form-container input[type="text"],
.form-container input[type="number"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
}

.form-container button {
    background-color: #da70d6;
    color: white;
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}

.form-container button:hover {
    background-color: #c060c0;
}

.form-container label {
    font-weight: bold;
}

.diagnosis-button {
    display: inline-block;
    background-color: #a75fc1;
    color: white;
    padding: 12px 20px;
    margin: 10px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    transition: background-color 0.3s, transform 0.2s;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.diagnosis-button:hover {
    background-color: #924eb0;
    transform: translateY(-2px);
}

    </style>
</head>
<body>
<header>
    <h1>ניהול מוצרים - <?= $category ?></h1>
</header>

        <a href="Adminpage.php" style="
    display: inline-block;
    margin: 20px;
    padding: 10px 20px;
    background-color: #c755e0;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background-color 0.3s;
" onmouseover="this.style.backgroundColor=' #d977e8'" onmouseout="this.style.backgroundColor=' #c755e0'">
    ← חזרה לדף הניהול הראשי
</a>

<div class="category-menu">
    <a href="?category=hair">שיער</a> |
    <a href="?category=cosmetics">קוסמטיקה</a> |
    <a href="?category=nails">ציפורניים</a>
</div>
<div class="form-container">
    <form method="post" action="">
        <input type="hidden" name="category" value="<?= $category ?>">

        <label>שם מוצר:</label>
        <input type="text" name="product_name" required>

        <label>כמות:</label>
        <input type="number" name="quantity" required>

        <label>מחיר:</label>
        <input type="number" name="price" required>

        <label>קישור לתמונה:</label>
        <input type="text" name="image">

        <button type="submit" name="add_product">➕ הוסף מוצר</button>
    </form>
</div>
<table>
    <tr>
        <th>ID</th>
        <th>שם מוצר</th>
        <th>כמות</th>
        <th>מחיר</th>
        <th>תמונה</th>
        <th>פעולות</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row[$id_col] . '</td>';
            echo '<td>' . $row[$name_col] . '</td>';
            echo '<td>' . $row['quantity'] . '</td>';
            echo '<td>₪' . $row['price'] . '</td>';
            echo '<td><img src="' . $row['image'] . '" alt="תמונה"></td>';
            echo '<td>';
            echo '<div class="button-group">';
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="category" value="' . $category . '">';
            echo '<input type="hidden" name="product_id" value="' . $row[$id_col] . '">';
            echo '<button type="submit" name="delete_product" class="delete-btn">🗑️</button>';
            echo '</form>';
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="category" value="' . $category . '">';
            echo '<input type="hidden" name="product_id" value="' . $row[$id_col] . '">';
            echo '<input type="text" name="product_name" value="' . $row[$name_col] . '" required>';
            echo '<input type="number" name="quantity" value="' . $row['quantity'] . '" required>';
            echo '<input type="number" name="price" value="' . $row['price'] . '" required>';
            echo '<button type="submit" name="update_product" class="update-btn">✏️</button>';
            echo '</form>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6">אין מוצרים להצגה.</td></tr>';
    }
    ?>
</table>
<div style="margin: 30px;">
    <a href="skin_diagnosis_list.php" class="diagnosis-button">📋 הצג רשימת אבחוני עור</a>
    <a href="hair_diagnosis_list.php" class="diagnosis-button">💇‍♀️ הצג רשימת אבחוני שיער</a>
</div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    if (!isset($tableMap[$category])) {
        die("❌ קטגוריה לא חוקית");
    }
    $table = $tableMap[$category]['table'];
    $id_col = $tableMap[$category]['id'];
    $name_col = $tableMap[$category]['name'];
    if (isset($_POST['add_product'])) {
        $product_name = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        if ($quantity < 0) {
            echo "❌ שגיאה: מלאי שלילי לא חוקי.";
        } else {
            $sql = "INSERT INTO $table ($name_col, quantity, price, image)
                    VALUES ('$product_name', '$quantity', '$price', '$image')";
            echo $conn->query($sql) ? "✅ מוצר נוסף!" : "❌ שגיאה: " . $conn->error;
        }
    }

    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $sql = "DELETE FROM $table WHERE $id_col = '$product_id'";
        echo $conn->query($sql) ? "✅ נמחק בהצלחה." : "❌ שגיאה: " . $conn->error;
    }

    if (isset($_POST['update_product'])) {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];

        if ($quantity < 0) {
            echo "❌ כמות לא יכולה להיות שלילית.";
        } else {
            $sql = "UPDATE $table
                    SET $name_col='$product_name', quantity='$quantity', price='$price'
                    WHERE $id_col='$product_id'";
            echo $conn->query($sql) ? "✅ עודכן בהצלחה." : "❌ שגיאה: " . $conn->error;
        }
    }
}



?>




