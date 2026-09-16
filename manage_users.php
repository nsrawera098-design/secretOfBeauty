<?php
// חיבור למסד הנתונים
require_once 'connectToDB.php';
include('navbar.php');



if (isset($_POST['delete_user'])) {
    $id = intval($_POST['user_id']);
    
    // בדיקה שהתפקיד אינו מנהל
    $check_query = mysqli_query($conn, "SELECT role FROM user WHERE id = $id");
    $user_data = mysqli_fetch_assoc($check_query);
    
    if ($user_data['role'] != 1) {
        mysqli_query($conn, "DELETE FROM user WHERE id = $id");
    }
}

// הוספת משתמש
if (isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role_text = $_POST['role'];
    $role = ($role_text == 'employee') ? 2 : 0;
    $password = $_POST['password'];

    $query = "INSERT INTO user (username, email, password, role) 
              VALUES ('$username', '$email', '$password', '$role')";

    if (mysqli_query($conn, $query)) {
        $new_id = mysqli_insert_id($conn);
        echo "<script>alert('המשתמש נוסף בהצלחה. ID שלו הוא: $new_id');</script>";
    } else {
        echo "<script>alert('שגיאה בהוספת המשתמש: " . mysqli_error($conn) . "');</script>";
    }
}

// מחיקת משתמש
if (isset($_POST['delete_user'])) {
    $id = intval($_POST['user_id']);
    mysqli_query($conn, "DELETE FROM user WHERE id = $id");
}

// חיפוש וסינון
$filter_role = $_GET['filter_role'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM user WHERE role != 1"; // אל תכלול מנהלים
if ($filter_role === 'employee') {
    $query .= " AND role = 2";
} elseif ($filter_role === 'user') {
    $query .= " AND role = 0";
}

if ($search !== '') {
    $query .= " AND (username LIKE '%$search%' OR email LIKE '%$search%')";
}

$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>ניהול עובדים ולקוחות</title>
    <style>
        body {
            background-color: #f5f5fa;
            font-family: Arial;
            direction: rtl;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 0 12px #ccc;
        }
        h2 {
            text-align: center;
            color: #9147b4;
        }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            background-color: #d977e8;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            margin-top: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c755e0;
        }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        th, td {
            border-bottom: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f9c2d1;
        }
        .filter-form {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .filter-form input, .filter-form select {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>👥 ניהול עובדים ולקוחות</h2>

        <a href="manage_feedbacks.php" style="
    display: inline-block;
    margin: 15px 0 30px 0;
    padding: 12px 25px;
    background-color: #9147b4;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background-color 0.3s;
" onmouseover="this.style.backgroundColor='#b262d9'" onmouseout="this.style.backgroundColor='#9147b4'">
    📝 ניהול פידבקים
</a>

        <a href="Adminpage.php" style="
    display: inline-block;
    margin: 20px;
    padding: 10px 20px;
    background-color: #c755e0;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background-color 0.3s;
" onmouseover="this.style.backgroundColor=' #d977e8'" onmouseout="this.style.backgroundColor=' #c755e0'">
    ← חזרה לדף הניהול הראשי
</a>


        <!-- טופס הוספה -->
        <form method="POST">
            <h3>➕ הוספת משתמש חדש</h3>
            <input type="text" name="id" placeholder="ID (נקבע אוטומטית)" disabled>
            <input type="text" name="username" placeholder="שם מלא" required>
            <input type="email" name="email" placeholder="אימייל" required>
            <input type="password" name="password" placeholder="סיסמה" required>
            <select name="role" required>
                <option value="">בחר תפקיד</option>
                <option value="employee">עובד (2)</option>
                <option value="user">לקוח (0)</option>
            </select>
            <button type="submit" name="add_user">➕ הוסף משתמש</button>
        </form>

        <!-- טופס חיפוש וסינון -->
        <form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="חיפוש לפי שם או מייל" value="<?= htmlspecialchars($search) ?>">
            <select name="filter_role">
                <option value="">הצג הכל</option>
                <option value="employee" <?= $filter_role === 'employee' ? 'selected' : '' ?>>רק עובדים</option>
                <option value="user" <?= $filter_role === 'user' ? 'selected' : '' ?>>רק לקוחות</option>
            </select>
            <button type="submit">🔍 סנן</button>
        </form>

        <!-- טבלת משתמשים -->
        <table>
            <tr>
                <th>ID</th>
                <th>שם</th>
                <th>אימייל</th>
                <th>תפקיד</th>
                <th>פעולה</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['role'] == 2 ? 'עובד' : 'לקוח' ?></td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete_user" onclick="return confirm('האם את/ה בטוח/ה שברצונך למחוק?')">🗑️</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
