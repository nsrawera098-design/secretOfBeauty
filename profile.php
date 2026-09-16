<?php
session_start();
include("connectToDB.php");

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$query = "SELECT * FROM user WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// העלאת תמונת פרופיל
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['profile_image']['tmp_name'];
    $fileName = basename($_FILES['profile_image']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        $uniqueName = uniqid() . '.' . $fileExt;
        $targetPath = "uploads/" . $uniqueName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            if (!empty($row['profile_image']) && file_exists("uploads/" . $row['profile_image'])) {
                unlink("uploads/" . $row['profile_image']);
            }

            mysqli_query($conn, "UPDATE user SET profile_image = '$uniqueName' WHERE email = '$email'");
            $row['profile_image'] = $uniqueName;
        }
    }
}

// מחיקת תמונה
if (isset($_POST['delete_image'])) {
    if (!empty($row['profile_image'])) {
        $imagePath = "uploads/" . $row['profile_image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        mysqli_query($conn, "UPDATE user SET profile_image = '' WHERE email = '$email'");
        $row['profile_image'] = '';
    }
}

// עדכון שדות
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['field'])) {
    $field = $_POST['field'];
    if (isset($_POST[$field])) {
        $value = mysqli_real_escape_string($conn, $_POST[$field]);
        mysqli_query($conn, "UPDATE user SET $field = '$value' WHERE email = '$email'");
        $row[$field] = $value;

        // אם המשתמש שינה את כתובת האימייל, נעדכן גם את session
        if ($field === 'email') {
            $_SESSION['email'] = $value;
            $email = $value;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>הפרופיל שלי</title>
    <style>
    body {
        direction: rtl;
        font-family: Arial;
        background-color: #f2f2f2;
    }

    .profile-container {
        background-color: #fff;
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 20px;
        text-align: right;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"],
    .form-group input[type="date"] {
        padding: 10px;
        width: 70%;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 15px;
    }

    .form-group input[type="file"] {
        display: none;
    }

    .custom-file-upload {
        display: inline-block;
        padding: 10px 18px;
        background-color: #f8b6c1;
        color: white;
        border-radius: 25px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin-top: 5px;
    }

    .custom-file-upload:hover {
        background-color: #f48ca3;
        transform: scale(1.05);
    }

    .form-group button {
        padding: 10px 18px;
        background-color: #f8b6c1;
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin-right: 10px;
    }

    .form-group button:hover {
        background-color: #f48ca3;
        transform: scale(1.05);
    }

    img.profile-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f8b6c1;
        margin-bottom: 15px;
    }

    .home-button {
        display: block;
        width: fit-content;
        margin: 30px auto 0;
        padding: 12px 30px;
        background-color: #f8c8dc;
        color: #333;
        border: none;
        border-radius: 30px;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .home-button:hover {
        background-color: #f5a5c0;
        color: white;
        transform: scale(1.05);
    }
    /* כפתור העלאת תמונה */
.custom-file-upload {
    display: inline-block;
    padding: 12px 25px;
    background-color: #f8c8dc;
    color: #333;
    border-radius: 30px;
    cursor: pointer;
    font-size: 15px;
    transition: all 0.3s ease;
    margin: 10px 5px;
    text-align: center;
    text-decoration: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.custom-file-upload:hover {
    background-color: #f5a5c0;
    color: white;
    transform: scale(1.05);
}

/* כפתור מחיקת תמונה */
.delete-image-button {
    display: inline-block;
    padding: 12px 25px;
    background-color: #f8c8dc;
    color: #333;
    border-radius: 30px;
    cursor: pointer;
    font-size: 15px;
    transition: all 0.3s ease;
    margin: 10px 5px;
    text-align: center;
    text-decoration: none;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.delete-image-button:hover {
    background-color: #f5a5c0;
    color: white;
    transform: scale(1.05);
}


</style>


</head>
<body>

<?php include("navbar.php"); ?>

<div class="profile-container">
    <h2 style="text-align: center;">הפרופיל שלי</h2>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2>🖼️ תמונת פרופיל</h2>

        <?php if (!empty($row['profile_image']) && file_exists("uploads/" . $row['profile_image'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($row['profile_image']); ?>" class="profile-image" alt="Profile Image">
        <?php else: ?>
            <img src="photos/profile.png" class="profile-image" alt="Default Avatar">
        <?php endif; ?>

        <form action="profile.php" method="post" enctype="multipart/form-data" style="margin-top: 10px;">
            <input type="file" name="profile_image" required>
            <button type="submit">📤 העלאת תמונה</button>
        </form>

        <?php if (!empty($row['profile_image'])): ?>
            <form action="profile.php" method="post" style="margin-top: 10px;">
                <button type="submit" name="delete_image" style="background-color: red;">🗑️ מחיקת תמונה</button>
            </form>
        <?php endif; ?>
    </div>

    <form method="post">
        <div class="form-group">
            <label>👤 שם משתמש:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>">
            <button type="submit" name="field" value="username">שמור</button>
        </div>

        <div class="form-group">
            <label>📧 אימייל:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
            <button type="submit" name="field" value="email">שמור</button>
        </div>

        <div class="form-group">
            <label>🔒 סיסמה:</label>
            <input type="password" name="password" value="<?php echo htmlspecialchars($row['password']); ?>">
            <button type="submit" name="field" value="password">שמור</button>
        </div>

        <div class="form-group">
            <label>🎂 תאריך לידה:</label>
            <input type="date" name="birthdate" value="<?php echo htmlspecialchars($row['birthdate'] ?? ''); ?>">
            <button type="submit" name="field" value="birthdate">שמור</button>
        </div>
    </form>
    <a href="homePage.php" class="home-button">🏠 חזרה לדף הבית</a>

</div>

</body>
</html>
