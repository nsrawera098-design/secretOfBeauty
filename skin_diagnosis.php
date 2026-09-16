<?php 
session_start();
include('navbar.php');
include('connectToDB.php');

// בדיקת התחברות
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// בדיקה אם המשתמש כבר מילא אבחון
$query = "SELECT has_diagnosed_skin FROM user WHERE email = '$email'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("שגיאה בשאילתת המשתמש: " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($result);

$alreadyDiagnosed = ($user && $user['has_diagnosed_skin'] == 1);

// שליחה של האבחון
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $age = intval($_POST['age']);
    $skin_type = mysqli_real_escape_string($conn, $_POST['skin_type']);
    $skin_issues = isset($_POST['skin_issues']) ? implode(',', $_POST['skin_issues']) : '';
    $skin_allergies = mysqli_real_escape_string($conn, $_POST['skin_allergies']);
    $issues = mysqli_real_escape_string($conn, $_POST['issues']);
    $goal = mysqli_real_escape_string($conn, $_POST['goal']);
    $additional_info = mysqli_real_escape_string($conn, $_POST['additional_info']);
    $created_at = date('Y-m-d H:i:s');

    // טיפול בהעלאת תמונה
    $face_image = null;
    if (isset($_FILES['face_image']) && $_FILES['face_image']['error'] == 0) {
        $target_dir = "uploads/face_images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $face_image_name = basename($_FILES['face_image']['name']);
        $face_image = $target_dir . time() . "_" . $face_image_name;
        move_uploaded_file($_FILES['face_image']['tmp_name'], $face_image);
    }

    if ($alreadyDiagnosed) {
        // עדכון אבחון קיים
        $updateQuery = "UPDATE skin_diagnosis SET 
            full_name='$full_name', age=$age, skin_type='$skin_type', skin_issues='$skin_issues', 
            skin_allergies='$skin_allergies', issues='$issues', goal='$goal', 
            face_image='$face_image', additional_info='$additional_info', created_at='$created_at'
            WHERE email='$email'";
        $updateResult = mysqli_query($conn, $updateQuery);
        if (!$updateResult) {
            die("שגיאה בעדכון האבחון: " . mysqli_error($conn));
        }
    } else {
        // הוספת אבחון חדש
        $insertQuery = "INSERT INTO skin_diagnosis 
            (full_name, email, age, skin_type, skin_issues, skin_allergies, issues, goal, face_image, additional_info, created_at) 
            VALUES 
            ('$full_name', '$email', $age, '$skin_type', '$skin_issues', '$skin_allergies', '$issues', '$goal', '$face_image', '$additional_info', '$created_at')";
        $insertResult = mysqli_query($conn, $insertQuery);
        if (!$insertResult) {
            die("שגיאה בהכנסת האבחון: " . mysqli_error($conn));
        }

        // עדכון טבלת המשתמשים
        $updateUserQuery = "UPDATE user SET has_diagnosed_skin = 1 WHERE email = '$email'";
        mysqli_query($conn, $updateUserQuery);
    }

    if (mysqli_query($conn, $insertQuery)) {
        header("Location: skin_result.php?email=" . urlencode($email));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>אבחון עור</title>
<style>
body {
    background: url('photos/sskkin8.png') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Varela Round', sans-serif;
    padding: 20px;
    color: #333;
    direction: rtl;
    min-height: 100vh;
}
.form-container {
    max-width: 650px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.9); /* לבן שקוף */
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}


header {
    text-align: center;
    margin-bottom: 40px;
}

h1 {
    font-size: 36px;
    color: #b23a8e;
    margin-bottom: 10px;
}

.form-container {
    max-width: 650px;
    margin: 0 auto;
    background: #fff;
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.form-container h2 {
    margin-bottom: 25px;
    font-size: 24px;
    color: #444;
    text-align: center;
}

.question-group,
.textarea-group {
    margin-bottom: 20px;
    text-align: right;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
}

input[type="text"],
input[type="email"],
input[type="file"],
select,
textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ccc;
    border-radius: 12px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

input[type="text"]:focus,
input[type="email"]:focus,
select:focus,
textarea:focus {
    border-color: #b23a8e;
    outline: none;
}

input[type="radio"],
input[type="checkbox"] {
    margin-left: 8px;
}

.radio-group {
    display: flex;
    gap: 30px;
    align-items: center;
    margin-top: 8px;
    flex-wrap: wrap;
}

textarea {
    resize: none;
}

button {
    background-color: #b23a8e;
    color: white;
    font-size: 18px;
    padding: 14px 25px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #9b2979;
}

.update-link {
    text-align: center;
    margin-top: 20px;
}

.update-link a {
    color: #007bff;
    font-weight: bold;
    text-decoration: none;
}

.update-link a:hover {
    text-decoration: underline;
}

/* תיבת checkbox */
.question-group input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.2);
}

</style>

</head>

<body>
    <header>
        <h1>אבחון לפני טיפול פנים</h1>
    </header>

    <div class="form-container">
        <?php if ($alreadyDiagnosed): ?>
            <h2>כבר מילאת אבחון עור בעבר</h2>
            <div class="update-link">
                <p>אם ברצונך לעדכן את האבחון שלך:</p>
                <a href="update_skin_diagnosis.php">עדכן את האבחון</a>
            </div>
        <?php else: ?>
            <h2>נא למלא את פרטי האבחון</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="question-group">
                    <label for="skin_type">מהו סוג העור שלך?</label>
                    <select name="skin_type" id="skin_type" required>
                        <option value="">בחרי</option>
                        <option value="שומני">שומני</option>
                        <option value="יבש">יבש</option>
                        <option value="מעורב">מעורב</option>
                        <option value="רגיל">רגיל</option>
                    </select>
                </div>

                <div class="question-group">
                    <label>מה מצב עור הפנים שלך בדרך כלל?</label>
                    <select name="skin_condition" required>
                        <option value="">בחרי</option>
                        <option value="מבריק מאוד">מבריק מאוד</option>
                        <option value="נוטה להתקלפות">נוטה להתקלפות</option>
                        <option value="מאוזן">מאוזן</option>
                        <option value="מגורה או אדמומי">מגורה או אדמומי</option>
                    </select>
                </div>

                <div class="question-group">
                    <label>האם את נוטה לפצעונים או נקודות שחורות?</label>
                    <div class="radio-group">
                        <input type="radio" id="acne_yes" name="has_acne" value="כן" required>
                        <label for="acne_yes">כן</label>

                        <input type="radio" id="acne_no" name="has_acne" value="לא" required>
                        <label for="acne_no">לא</label>
                    </div>
                </div>

                <div class="question-group">
                    <label>האם השתמשת בעבר במסכות, סרומים או פילינג?</label>
                    <div class="radio-group">
                        <input type="radio" id="used_products_yes" name="used_products" value="כן" required>
                        <label for="used_products_yes">כן</label>

                        <input type="radio" id="used_products_no" name="used_products" value="לא" required>
                        <label for="used_products_no">לא</label>
                    </div>
                </div>
                <div class="textarea-group">
                    <label for="product_reaction">אם השתמשת, איך הגיב העור שלך למוצרים?</label>
                    <textarea name="product_reaction" id="product_reaction" placeholder="כתבי בקצרה: האם הייתה רגישות? שיפור?"></textarea>
                </div>

                <div class="question-group">
                <label>מה בעיות העור שלך?</label>
<input type="checkbox" name="skin_issues[]" value="אקנה"> אקנה
<input type="checkbox" name="skin_issues[]" value="יובש"> יובש
<input type="checkbox" name="skin_issues[]" value="אדמומיות"> אדמומיות


                <div class="question-group">
                    <label>אלרגיות בעור:</label>
                    <input type="text" name="skin_allergies">
                </div>

                <div class="question-group">
                    <label>מה מפריע לך בעור?</label>
                    <input type="text" name="issues">
                </div>

                <div class="question-group">
                    <label>העלאת תמונת פנים:</label>
                    <input type="file" name="face_image">
                </div>

                <div class="textarea-group">
                    <label>מידע נוסף:</label>
                    <textarea name="additional_info" rows="4"></textarea>
                </div>

                <button type="submit">שליחה</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
