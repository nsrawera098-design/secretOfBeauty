<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include("connectToDB.php");

$email = $_SESSION['email'];
$sql = "SELECT * FROM hair_diagnosis WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "לא נמצאו נתוני אבחון שיער.";
    exit();
}

$data = $result->fetch_assoc();

// לוגיקה של המלצה לפי הנתונים
$hair_type = $data['hair_type'];
$scalp_condition = $data['scalp_condition'];
$goal = $data['goal'];

$recommended_product = "לא הצלחנו לזהות מוצר מתאים";

if ($hair_type == "יבש" && $goal == "הזנה וחידוש") {
    $recommended_product = "מסכת שיער להזנה עמוקה עם שמן ארגן";
} elseif ($hair_type == "פגום" && $goal == "שיקום שיער פגום") {
    $recommended_product = "סרום קרטין לשיקום מיידי";
} elseif ($scalp_condition == "קשקשים") {
    $recommended_product = "שמפו טיפולי נגד קשקשים טבעי";
} elseif ($goal == "החלקה טבעית") {
    $recommended_product = "קרם החלקה טבעי ללא פרבנים";
} elseif ($hair_type == "שומני" && $goal == "ברק ורכות") {
    $recommended_product = "שמפו מאזן לשיער שומני עם תמציות הדרים";
}

$image = $data['hair_image'];
$name = $data['full_name'];
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>המלצות לפי אבחון שיער</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            padding: 30px;
            direction: rtl;
            text-align: center;
        }
        .result-box {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #444;
        }
        .product {
            background-color: #e4e8fc;
            color: #333;
            padding: 15px;
            border-radius: 12px;
            margin-top: 20px;
            font-size: 18px;
        }
        img {
            margin-top: 20px;
            max-width: 100%;
            border-radius: 10px;
        }
        .button {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background-color: #6c63ff;
            color: white;
            text-decoration: none;
            border-radius: 10px;
        }
        .button:hover {
            background-color: #584ff0;
        }
    </style>
</head>
<body>
    <div class="result-box">
        <h2>ההמלצה האישית שלך, <?= htmlspecialchars($name) ?> 💇‍♀️</h2>
        <p>בהתאם לאבחון שלך, אנחנו ממליצים על:</p>
        <div class="product"><?= $recommended_product ?></div>

        <?php if (!empty($image)): ?>
            <img src="<?= htmlspecialchars($image) ?>" alt="תמונת שיער">
        <?php endif; ?>

        <a href="edit_hair_diagnosis.php" class="button">רוצה לעדכן את האבחון? ✏️</a>
    </div>
</body>
</html>
