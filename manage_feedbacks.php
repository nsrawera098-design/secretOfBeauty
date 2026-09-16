<?php
session_start();
include('navbar.php');

// בדיקת הרשאות
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    die("אין לך הרשאה לגשת לעמוד זה.");
}


$message = "";

// מחיקה
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_feedback'])) {
    $indexToDelete = (int)$_POST['delete_feedback'];

    if (file_exists("feedbacks.txt")) {
        $allFeedbacks = file("feedbacks.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (isset($allFeedbacks[$indexToDelete])) {
            unset($allFeedbacks[$indexToDelete]);
            file_put_contents("feedbacks.txt", implode("\n", $allFeedbacks) . "\n");
            $message = "✅ הפידבק נמחק בהצלחה.";
        }
    }
}

// קריאה לכל הפידבקים
$feedbacks = [];
if (file_exists("feedbacks.txt")) {
    $feedbacks = array_reverse(file("feedbacks.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ניהול פידבקים</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff0f5;
            padding: 40px;
            text-align: center;
        }

        h2 {
            color: #d63384;
        }

        .message {
            margin: 15px auto;
            font-weight: bold;
            color: green;
        }

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 8px 20px rgba(220, 120, 140, 0.2);
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #f3c2cc;
        }

        th {
            background-color: #ffc0cb;
            color: #4a2c2a;
        }

        .delete-btn {
            background-color: #ffb3b3;
            border: none;
            padding: 6px 12px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            color: #700;
        }

        .delete-btn:hover {
            background-color: #ff8f8f;
        }

        a.home-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ffe4ec;
            border: 2px solid #f77aa2;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            color: #d63384;
        }

        a.home-btn:hover {
            background-color: #fddde6;
        }
    </style>
</head>
<body>

<h2>🛠️ ניהול פידבקים</h2>

<?php if ($message): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<?php if (!empty($feedbacks)): ?>
    <table>
        <tr>
            <th>תאריך</th>
            <th>משתמש</th>
            <th>פידבק</th>
            <th>פעולה</th>
        </tr>
        <?php foreach ($feedbacks as $i => $line): ?>
            <?php if (preg_match('/^(.+?) \| (.+?): (.+)$/', $line, $matches)): ?>
                <tr>
                    <td><?= htmlspecialchars($matches[1]) ?></td>
                    <td><?= htmlspecialchars($matches[2]) ?></td>
                    <td><?= htmlspecialchars($matches[3]) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="delete_feedback" value="<?= count($feedbacks) - 1 - $i ?>">
                            <button type="submit" class="delete-btn">🗑️ מחק</button>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>אין פידבקים להצגה.</p>
<?php endif; ?>

<a href="adminpage.php" class="home-btn">חזרה לדף מנהל</a>

</body>
</html>