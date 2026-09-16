<?php
session_start();
include("connectToDB.php");

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$successMessage = "";

function getCurrentUserData($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$row = getCurrentUserData($conn, $username);

if (isset($_POST['delete_image'])) {
    if (!empty($row['profile_image']) && file_exists("uploads/" . $row['profile_image'])) {
        unlink("uploads/" . $row['profile_image']);
    }
    $stmt = $conn->prepare("UPDATE user SET profile_image = '' WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $successMessage = "🗑️ תמונת הפרופיל נמחקה בהצלחה.";
    $row = getCurrentUserData($conn, $username);
}

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['profile_image']['tmp_name'];
    $fileName = basename($_FILES['profile_image']['name']);
    $targetPath = "uploads/" . $fileName;

    if (!is_dir("uploads")) mkdir("uploads", 0777, true);

    if (move_uploaded_file($fileTmp, $targetPath)) {
        $stmt = $conn->prepare("UPDATE user SET profile_image = ? WHERE username = ?");
        $stmt->bind_param("ss", $fileName, $username);
        $stmt->execute();
        $successMessage = "📸 התמונה עודכנה בהצלחה!";
        $row = getCurrentUserData($conn, $username);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['field'])) {
    $field = $_POST['field'];
    $value = trim($_POST[$field] ?? '');

    if ($field === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $successMessage = "❌ כתובת אימייל לא תקינה.";
    } elseif (in_array($field, ['username', 'email', 'password', 'birthdate'])) {
        $stmt = $conn->prepare("UPDATE user SET $field = ? WHERE username = ?");
        $stmt->bind_param("ss", $value, $username);
        if ($stmt->execute()) {
            $successMessage = "✅ השדה '$field' עודכן בהצלחה.";
            $row = getCurrentUserData($conn, $username);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>הפרופיל שלי</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e1f0fa;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007acc;
        }
        .profile-image {
            display: block;
            margin: 0 auto 15px;
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #007bff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #444;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="date"] {
            width: calc(100% - 90px);
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        button {
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            background: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background: #dc3545;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .password-wrapper {
            position: relative;
            width: 100%;
        }
        .password-wrapper input {
            width: 100%;
            padding-right: 35px;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
        }
        input[type="file"] {
            display: none;
        }
        .custom-file-upload {
            padding: 10px 16px;
            background-color: #007bff;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            display: inline-block;
            font-weight: bold;
        }
        .home-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .home-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>הפרופיל שלי</h2>

    <?php if ($successMessage): ?>
        <div class="success-message"><?= $successMessage ?></div>
    <?php endif; ?>

    <div style="text-align: center;">
        <h3>תמונת פרופיל 📷</h3>
        <?php if (!empty($row['profile_image']) && file_exists("uploads/" . $row['profile_image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['profile_image']) ?>?t=<?= time() ?>" class="profile-image">
        <?php else: ?>
            <img src="https://www.w3schools.com/howto/img_avatar.png" class="profile-image" alt="Default Avatar">
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" style="margin-top: 10px;">
            <label for="profile_image" class="custom-file-upload">בחרי תמונה</label>
            <input type="file" name="profile_image" id="profile_image" required>
            <button type="submit">📄 העלאה</button>
        </form>

        <form method="post" style="margin-top: 5px;">
            <input type="hidden" name="delete_image" value="1">
            <button class="delete-btn" type="submit">🗑️ מחיקת תמונה</button>
        </form>
    </div>

    <form method="post">
        <div class="form-group">
            <label>👤 שם משתמש:</label>
            <div class="form-row">
                <input type="text" name="username" value="<?= htmlspecialchars($row['username'] ?? '') ?>">
                <button type="submit" name="field" value="username">שמור</button>
            </div>
        </div>

        <div class="form-group">
            <label>📧 אימייל:</label>
            <div class="form-row">
                <input type="email" name="email" value="<?= htmlspecialchars($row['email'] ?? '') ?>">
                <button type="submit" name="field" value="email">שמור</button>
            </div>
        </div>

        <div class="form-group">
            <label>🔒 סיסמה:</label>
            <div class="form-row password-wrapper">
                <input type="password" id="password" name="password" value="<?= htmlspecialchars($row['password'] ?? '') ?>">
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
                <button type="submit" name="field" value="password">שמור</button>
            </div>
        </div>

        <div class="form-group">
            <label>🎂 תאריך לידה:</label>
            <div class="form-row">
                <input type="date" name="birthdate" value="<?= htmlspecialchars($row['birthdate'] ?? '') ?>">
                <button type="submit" name="field" value="birthdate">שמור</button>
            </div>
        </div>
    </form>

    <a href="homepage.php" class="home-link">⬅️ חזרה לדף הבית</a>
</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}
</script>
</body>
</html>