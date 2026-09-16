<?php
include('navbar.php');
$host = "localhost";
$user = "root";
$password = "";
$database = "user";
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("❌ התחברות נכשלה: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if (isset($_POST['category']) && isset($_POST['location'])) {
    $category = $conn->real_escape_string($_POST['category']);
    $location = $conn->real_escape_string($_POST['location']);
} else {
    die("<p>❌ שגיאה: לא התקבלו פרטי קטגוריה או מיקום.</p>");
}

$sql = "SELECT * FROM businesses WHERE location = '$location' AND category = '$category'";
$result = $conn->query($sql);

// מערך של עסקים לפי מיקום
$branches = [
    'כרמיאל' => [
        'קוסמטיקה' => 'מרים'
    ],
    'ירכא' => [
        'עיצוב שיער' => 'מספרת עלי'
    ],
    'חיפה' => [
        'עיצוב שיער' => 'יופי עדן',
        'ציפורניים' => 'סטודיו שירן'
    ]
];

?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>העסקים באזור <?php echo htmlspecialchars($location); ?> בקטגוריה <?php echo htmlspecialchars($category); ?></title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 40px;
        }
        .business {
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        button {
            padding: 10px 20px;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }
        button:hover {
            background-color: #ff4757;
        }

          .info-box {
            margin-top: 30px;
            padding: 15px;
            border: 1px dashed #aaa;
            border-radius: 10px;
            background-color: #fff8f8;
            display: inline-block;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <a href="category.php" style="
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 20px;
    background-color: #ff4757;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
">⬅ חזרה לבחירת קטגוריה</a>

<h2>עסקים באזור <?php echo htmlspecialchars($location); ?> בקטגוריה <?php echo htmlspecialchars($category); ?></h2>

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="business">';
        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
        echo '<p><strong>קטגוריה:</strong> ' . htmlspecialchars($row['category']) . '</p>';
        echo '<p><strong>טלפון:</strong> ' . htmlspecialchars($row['phone']) . '</p>';
        echo '<p><strong>מיקום:</strong> ' . htmlspecialchars($row['location']) . '</p>';
        echo '<p><strong>תיאור:</strong> ' . htmlspecialchars($row['description']) . '</p>';

        // ✅ שינוי ל־business_id במקום business_email
        echo '<form method="post" action="appointment.php">';
        echo '<input type="hidden" name="business_id" value="' . htmlspecialchars($row['id']) . '">';
        echo '<button type="submit">זמן תור</button>';
        echo '</form>';

        echo '</div>';
    }
} else {
    echo '<p>🚫 לא נמצאו עסקים באזור ' . htmlspecialchars($location) . ' בקטגוריה ' . htmlspecialchars($category) . '.</p>';

    // הצגת אילו קטגוריות קיימות בסניף
    if (array_key_exists($location, $branches)) {
        echo '<div class="info-box">';
        echo '<p>📍 בסניף <strong>' . htmlspecialchars($location) . '</strong> קיימים העסקים הבאים:</p>';
        echo '<ul style="list-style: none; padding: 0;">';
        foreach ($branches[$location] as $cat => $biz) {
            echo '<li>• <strong>' . htmlspecialchars($cat) . '</strong>: ' . htmlspecialchars($biz) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}
$conn->close();
?>

</body>
</html>
