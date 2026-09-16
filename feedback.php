<?php
session_start();
include('navbar.php');

$message = "";

// מחיקת פידבק על ידי מנהל
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_feedback']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $indexToDelete = (int)$_POST['delete_feedback'];
    if (file_exists("feedbacks.txt")) {
        $allFeedbacks = file("feedbacks.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (isset($allFeedbacks[$indexToDelete])) {
            unset($allFeedbacks[$indexToDelete]);
            file_put_contents("feedbacks.txt", implode("\n", $allFeedbacks) . "\n");
            $message = "🗑️ הפידבק נמחק בהצלחה.";
        }
    }
}

// הוספת פידבק
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['feedback'])) {
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $feedback = trim($_POST['feedback']);

        if (!empty($feedback)) {
            $entry = date("Y-m-d H:i") . " | $username: " . $feedback . "\n";
            file_put_contents("feedbacks.txt", $entry, FILE_APPEND);
            $message = "✅ תודה על השיתוף!";
        } else {
            $message = "⚠️ אנא מלא/י את השדה.";
        }
    } else {
        $message = "⚠️ עליך להיות מחובר/ת כדי לשלוח חוויה.";
    }
}

// טען את כל הפידבקים (מסודרים מהחדש לישן)
$feedbacks = [];
if (file_exists("feedbacks.txt")) {
    $feedbacks = array_reverse(file("feedbacks.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>שיתוף חוויה</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fef0f5;
            padding: 40px;
            direction: rtl;
            text-align: center;
            color: #4a2c2a;
        }

        h2 {
            font-size: 28px;
            color: #d63384;
            margin-bottom: 20px;
        }

        form {
            background: #fff0f5;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            margin: 0 auto 30px;
            box-shadow: 0 8px 20px rgba(255, 182, 193, 0.4);
        }

        textarea {
            width: 100%;
            height: 120px;
            padding: 12px;
            font-size: 16px;
            border-radius: 12px;
            border: 1px solid #f7a9a8;
            resize: none;
        }

        button, .delete-btn {
            margin-top: 10px;
            padding: 8px 20px;
            font-size: 14px;
            border-radius: 25px;
            cursor: pointer;
        }

        button {
            background-color: #ff94c2;
            border: none;
            color: white;
            font-weight: bold;
        }

        button:hover {
            background-color: #f77aa2;
        }

        .delete-btn {
            background-color: #ffb3b3;
            border: none;
            color: #700;
            font-weight: bold;
        }

        .delete-btn:hover {
            background-color: #ff8f8f;
        }

        .message {
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }

        .home-btn {
            display: inline-block;
            margin-bottom: 30px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #ffe4ec;
            color: #d63384;
            text-decoration: none;
            border-radius: 25px;
            border: 2px solid #f77aa2;
            transition: 0.3s ease;
            font-weight: bold;
        }

        .home-btn:hover {
            background-color: #fddde6;
            color: #a1225d;
            transform: scale(1.05);
        }

        .feedback-list {
            background: #fff;
            padding: 20px;
            border-radius: 20px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 6px 18px rgba(220, 120, 140, 0.2);
        }

        .feedback-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ffb6c1;
            text-align: right;
        }

        .feedback-item:last-child {
            border-bottom: none;
        }

        .feedback-item strong {
            color: #a1225d;
        }

        .feedback-date {
            font-size: 13px;
            color: #888;
        }
    </style>
</head>
<body>
    <h2>🗣️ שתפו אותנו בחוויה שלכם</h2>

    <form method="POST">
        <label for="feedback">מה דעתך על האתר?</label><br>
        <textarea name="feedback" id="feedback" required></textarea><br>
        <button type="submit">שלח/י</button>
    </form>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <a href="homePage.php" class="home-btn">🏠 חזרה לדף הבית</a>

    <?php if (!empty($feedbacks)): ?>
        <div class="feedback-list">
            <h3>💬 חוויות קודמות</h3>
            <?php 
            foreach ($feedbacks as $i => $line): 
                if (preg_match('/^(.+?) \| (.+?): (.+)$/', $line, $matches)) {
                    $date = $matches[1];
                    $user = $matches[2];
                    $text = $matches[3];
                    echo "<div class='feedback-item'>
                            <div class='feedback-date'>$date</div>
                            <strong>$user</strong><br>
                            <span>$text</span>";
                    
                    // כפתור מחיקה רק למנהל
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        echo "<form method='POST' style='margin-top:5px;'>
                                <input type='hidden' name='delete_feedback' value='" . (count($feedbacks) - 1 - $i) . "' />
                                <button type='submit' class='delete-btn'>🗑️ מחק</button>
                              </form>";
                    }

                    echo "</div>";
                }
            endforeach;
            ?>
        </div>
    <?php endif; ?>
</body>
</html>
