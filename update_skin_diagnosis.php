<?php
include('connectToDB.php');
include 'db.php';
session_start();
include('navbar.php');

if (!isset($_SESSION['email'])) {
    echo "משתמש לא מחובר.";
    exit;
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$data = [];

// שליפת האבחון לפי האימייל
$query = "SELECT * FROM skin_diagnosis WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id = $row['id'];
} else {
    echo "אבחון לא נמצא.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $age = $_POST['age'] ?? '';
    $skin_type = $_POST['skin_type'] ?? '';
    $skin_condition = $_POST['skin_condition'] ?? '';
    $has_acne = $_POST['has_acne'] ?? '';
    $used_products = $_POST['used_products'] ?? '';
    $product_reaction = $_POST['product_reaction'] ?? '';
    $skin_issues = isset($_POST['skin_issues']) ? implode(', ', $_POST['skin_issues']) : '';
    $skin_allergies = $_POST['skin_allergies'] ?? '';
    $issues = $_POST['issues'] ?? '';
    $additional_info = $_POST['additional_info'] ?? '';

    // טיפול בתמונה
    $face_image = $row['face_image'];
    if (!empty($_FILES['face_image']['name'])) {
        $target_dir = "uploads/";
        $new_image = $target_dir . basename($_FILES["face_image"]["name"]);
        move_uploaded_file($_FILES["face_image"]["tmp_name"], $new_image);
        $face_image = $new_image;
    }

    $updateQuery = "UPDATE skin_diagnosis SET 
        full_name = '$full_name',
        age = '$age',
        skin_type = '$skin_type',
        skin_condition = '$skin_condition',
        has_acne = '$has_acne',
        used_products = '$used_products',
        product_reaction = '$product_reaction',
        skin_issues = '$skin_issues',
        skin_allergies = '$skin_allergies',
        issues = '$issues',
        face_image = '$face_image',
        additional_info = '$additional_info'
        WHERE email = '$email'";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('האבחון עודכן בהצלחה!'); window.location.href='hair_result.php';</script>";
        exit();
    } else {
        echo "שגיאה בעדכון: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>עדכון אבחון עור</title>
    <style>
        body {
            font-family: 'Varela Round', sans-serif;
            background: #fff0f5;
            color: #333;
            direction: rtl;
            text-align: right;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #d63384;
        }

        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            max-width: 650px;
            margin: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .question-group, .textarea-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .radio-group input {
            margin-left: 6px;
        }

        .radio-group label {
            margin-left: 15px;
        }

        button {
            background-color: #d63384;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: auto;
        }

        button:hover {
            background-color: #c2185b;
        }

        img {
            max-width: 200px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1>עדכון אבחון עור</h1>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="question-group">
                <label>שם מלא:</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($row['full_name'] ?? '') ?>" required>
            </div>

            <div class="question-group">
                <label>גיל:</label>
                <input type="text" name="age" value="<?= htmlspecialchars($row['age'] ?? '') ?>" required>
            </div>

            <div class="question-group">
                <label>סוג עור:</label>
                <select name="skin_type" required>
                    <option value="">בחרי</option>
                    <?php
                    $types = ["שומני", "יבש", "מעורב", "רגיל"];
                    foreach ($types as $type) {
                        $selected = (isset($row['skin_type']) && $row['skin_type'] == $type) ? 'selected' : '';
                        echo "<option value='$type' $selected>$type</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="question-group">
                <label>מצב העור:</label>
                <select name="skin_condition" required>
                    <?php
                    $conditions = ["מבריק מאוד", "נוטה להתקלפות", "מאוזן", "מגורה או אדמומי"];
                    foreach ($conditions as $condition) {
                        $selected = (isset($row['skin_condition']) && $row['skin_condition'] == $condition) ? 'selected' : '';
                        echo "<option value='$condition' $selected>$condition</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="question-group">
                <label>האם את נוטה לפצעונים?</label>
                <div class="radio-group">
                    <input type="radio" name="has_acne" value="כן" <?= (isset($row['has_acne']) && $row['has_acne'] == 'כן') ? 'checked' : '' ?>> כן
                    <input type="radio" name="has_acne" value="לא" <?= (isset($row['has_acne']) && $row['has_acne'] == 'לא') ? 'checked' : '' ?>> לא
                </div>
            </div>

            <div class="question-group">
                <label>האם השתמשת במסכות/סרומים/פילינג?</label>
                <div class="radio-group">
                    <input type="radio" name="used_products" value="כן" <?= (isset($row['used_products']) && $row['used_products'] == 'כן') ? 'checked' : '' ?>> כן
                    <input type="radio" name="used_products" value="לא" <?= (isset($row['used_products']) && $row['used_products'] == 'לא') ? 'checked' : '' ?>> לא
                </div>
            </div>

            <div class="textarea-group">
                <label>תגובה למוצרים:</label>
                <textarea name="product_reaction"><?= htmlspecialchars($row['product_reaction'] ?? '') ?></textarea>
            </div>

            <div class="question-group">
                <label>בעיות עור:</label>
                <?php
                $issues = isset($row['skin_issues']) ? explode(', ', $row['skin_issues']) : [];
                $options = ["אקנה", "יובש", "אדמומיות"];
                foreach ($options as $opt) {
                    $checked = in_array($opt, $issues) ? 'checked' : '';
                    echo "<input type='checkbox' name='skin_issues[]' value='$opt' $checked> $opt ";
                }
                ?>
            </div>

            <div class="question-group">
                <label>אלרגיות בעור:</label>
                <input type="text" name="skin_allergies" value="<?= htmlspecialchars($row['skin_allergies'] ?? '') ?>">
            </div>

            <div class="question-group">
                <label>מה מפריע לך בעור?</label>
                <input type="text" name="issues" value="<?= htmlspecialchars($row['issues'] ?? '') ?>">
            </div>

            <div class="question-group">
                <label>תמונה נוכחית:</label><br>
                <?php if (!empty($row['face_image'])): ?>
                    <img src="<?= htmlspecialchars($row['face_image']) ?>" alt="תמונת פנים">
                <?php else: ?>
                    <p>אין תמונה</p>
                <?php endif; ?>
            </div>

            <div class="question-group">
                <label>בחרי תמונה חדשה (אם תרצי לעדכן):</label>
                <input type="file" name="face_image">
            </div>

            <div class="textarea-group">
                <label>מידע נוסף:</label>
                <textarea name="additional_info" rows="4"><?= htmlspecialchars($row['additional_info'] ?? '') ?></textarea>
            </div>

            <button type="submit">שמור שינויים</button>
        </form>
    </div>
</body>
</html>