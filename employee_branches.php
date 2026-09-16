<?php
session_start();
require_once 'connectToDB.php';
include('navbar.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 2) {
    header("Location: index.php");
    exit();
}

function getCategoryIcon($category) {
    switch (trim($category)) {
        case 'קוסמטיקה': return '🧴';
        case 'עיצוב שיער': return '💇‍♀️';
        case 'ציפורניים': return '💅';
        default: return '🏷️';
    }
}

function getCategoryColor($category) {
    switch (trim($category)) {
        case 'קוסמטיקה': return '#fce4ec'; // ורוד בהיר
        case 'עיצוב שיער': return '#e3f2fd'; // תכלת
        case 'ציפורניים': return '#ede7f6'; // סגול בהיר
        default: return '#f0f0f0'; // אפור עדין
    }
}

$result = $conn->query("SELECT * FROM businesses");
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ניהול לפי סניף</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 40px;
            text-align: center;
        }

        h2 {
            background: #b48ad0;
            color: white;
            padding: 20px 30px;
            border-radius: 15px;
            font-size: 28px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: inline-block;
        }

        .branches {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .branch-box {
            border: 2px solid #ddd;
            border-radius: 15px;
            padding: 20px;
            font-size: 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            width: 240px;
            text-align: center;
        }

        .branch-box:hover {
            transform: scale(1.05);
        }

        .branch-box a {
            color: #6a1b9a;
            text-decoration: none;
            font-weight: bold;
            font-size: 20px;
        }

        .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .info {
            color: #333;
            margin-top: 10px;
            font-size: 15px;
        }
    </style>
</head>
<body>

<h2>ניהול סניפים</h2>

<div class="branches">
    <?php while ($row = $result->fetch_assoc()):
        $category = htmlspecialchars($row['category']);
        $color = getCategoryColor($category);
        ?>
        <div class="branch-box" style="background-color: <?= $color ?>;">
            <div class="icon"><?= getCategoryIcon($category) ?></div>
            <a href="manage_branch.php?business_id=<?= $row['id'] ?>">
                <?= $category ?>
            </a>
            <div class="info">שם הסניף: <?= htmlspecialchars($row['name']) ?></div>
            <div class="info">מיקום: <?= htmlspecialchars($row['location']) ?></div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
