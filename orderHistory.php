<?php
session_start();
require_once 'ConnectToDB.php';
include('navbar.php');

if (!isset($_SESSION['username'])) {
    die("❌ שגיאה: עליך להתחבר תחילה.");
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM orders WHERE username = '$username' ORDER BY order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>היסטוריית ההזמנות שלך 📄</title>
    <style>
        body {
            background-color: #ffeaf1;
            font-family: Arial, sans-serif;
            direction: rtl;
            text-align: center;
        }
        h2 {
            margin-top: 40px;
            color: #d63384;
        }
        table {
            margin: 30px auto;
            width: 95%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px #ffc0cb;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ffb3c6;
        }
        th {
            background-color: #ff8ab3;
            color: white;
        }
        td {
            background-color: #fff5f9;
        }
    </style>
</head>
<body>

<h2>📄 היסטוריית ההזמנות שלך</h2>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>מזהה הזמנה</th>
            <th>מזהה מוצר</th>
            <th>סוג מוצר</th>
            <th>כמות</th>
            <th>תאריך הזמנה</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['product_id'] ?></td>
                <td><?= $row['product_type'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['order_date'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>אין הזמנות להצגה.</p>
<?php endif; ?>

</body>
</html>
