<?php
session_start();
include("connectToDB.php");
include('navbar.php');

// ודא שמשתמש מחובר
$email = $_SESSION['email'] ?? null;
if (!$email) {
    die("משתמש לא מחובר");
}

// שליפת נתוני האבחון הקיימים
$sql = "SELECT * FROM hair_diagnosis WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "לא נמצאו נתוני אבחון.";
    exit;
}

// עדכון אבחון
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $age = (int)$_POST["age"];
    $hair_type = $_POST["hair_type"];
    $scalp_condition = $_POST["scalp_condition"];
    $goal = $_POST["goal"];
    $current_products = $_POST["current_products"];
    $wash_frequency = $_POST["wash_frequency"];
    $uses_heat_tools = $_POST["uses_heat_tools"];
    $additional_info = $_POST["additional_info"];
    
    // שמור את הנתיב הקיים של התמונה, אם קיים
    $hair_image = $data['hair_image'] ?? '';

    // אם נבחרה תמונה חדשה
    if (!empty($_FILES["hair_image"]["name"])) {
        $target_dir = "uploads/";
        $hair_image = $target_dir . basename($_FILES["hair_image"]["name"]);
        if (!move_uploaded_file($_FILES["hair_image"]["tmp_name"], $hair_image)) {
            die("שגיאה בהעלאת התמונה.");
        }
    }

    $stmt = $conn->prepare("UPDATE hair_diagnosis SET full_name=?, age=?, hair_type=?, scalp_condition=?, goal=?, current_products=?, wash_frequency=?, uses_heat_tools=?, additional_info=?, hair_image=? WHERE email=?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("sisssssssss", $full_name, $age, $hair_type, $scalp_condition, $goal, $current_products, $wash_frequency, $uses_heat_tools, $additional_info, $hair_image, $email);

   if ($stmt->execute()) {
    echo "האבחון עודכן בהצלחה!";
    header("Location: hair_result.php");
    exit();
} else {
    echo "שגיאה בעדכון: " . $stmt->error;
}
}
?>




<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>עריכת אבחון שיער</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f4f8;
            padding: 40px;
            direction: rtl;
            text-align: right;
        }
        form {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input[type="text"], input[type="number"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 12px;
            margin-top: 5px;
        }
        input[type="submit"] {
            background-color: #6c63ff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            margin-top: 20px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #574fd6;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">עריכת אבחון שיער</h2>

<form action="edit_hair_diagnosis.php" method="post" enctype="multipart/form-data">
    <label>שם מלא:</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($data['full_name']) ?>" required>

    <label>גיל:</label>
    <input type="number" name="age" value="<?= htmlspecialchars($data['age']) ?>" required>

    <label>סוג שיער:</label>
    <select name="hair_type" required>
        <option value="">בחרי...</option>
        <option value="יבש" <?= $data['hair_type'] == 'יבש' ? 'selected' : '' ?>>יבש</option>
        <option value="שומני" <?= $data['hair_type'] == 'שומני' ? 'selected' : '' ?>>שומני</option>
        <option value="נורמלי" <?= $data['hair_type'] == 'נורמלי' ? 'selected' : '' ?>>נורמלי</option>
        <option value="פגום" <?= $data['hair_type'] == 'פגום' ? 'selected' : '' ?>>פגום</option>
    </select>

    <label>מצב הקרקפת:</label>
    <select name="scalp_condition" required>
        <option value="">בחרי...</option>
        <option value="בריאה" <?= $data['scalp_condition'] == 'בריאה' ? 'selected' : '' ?>>בריאה</option>
        <option value="רגישה" <?= $data['scalp_condition'] == 'רגישה' ? 'selected' : '' ?>>רגישה</option>
        <option value="קשקשים" <?= $data['scalp_condition'] == 'קשקשים' ? 'selected' : '' ?>>קשקשים</option>
    </select>

    <label>מטרה טיפולית:</label>
    <select name="goal" required>
        <option value="">בחרי...</option>
        <option value="הזנה וחידוש" <?= $data['goal'] == 'הזנה וחידוש' ? 'selected' : '' ?>>הזנה וחידוש</option>
        <option value="שיקום שיער פגום" <?= $data['goal'] == 'שיקום שיער פגום' ? 'selected' : '' ?>>שיקום שיער פגום</option>
        <option value="ברק ורכות" <?= $data['goal'] == 'ברק ורכות' ? 'selected' : '' ?>>ברק ורכות</option>
        <option value="החלקה טבעית" <?= $data['goal'] == 'החלקה טבעית' ? 'selected' : '' ?>>החלקה טבעית</option>
    </select>

    <label>אילו מוצרים את משתמשת כרגע?</label>
    <input type="text" name="current_products" value="<?= htmlspecialchars($data['current_products']) ?>">

    <label>באיזו תדירות את חופפת?</label>
    <input type="text" name="wash_frequency" value="<?= htmlspecialchars($data['wash_frequency']) ?>">

    <label>האם את משתמשת במכשירי חום (פן, מחליק)?</label>
    <select name="uses_heat_tools" required>
        <option value="">בחרי...</option>
        <option value="כן" <?= $data['uses_heat_tools'] == 'כן' ? 'selected' : '' ?>>כן</option>
        <option value="לא" <?= $data['uses_heat_tools'] == 'לא' ? 'selected' : '' ?>>לא</option>
    </select>

    <label>תמונה של השיער שלך (ניתן לעדכן):</label>
    <input type="file" name="hair_image">

    <label>מידע נוסף שתרצי לשתף:</label>
    <textarea name="additional_info"><?= htmlspecialchars($data['additional_info']) ?></textarea>

    <input type="submit" value="עדכני את האבחון">
</form>

</body>
</html>
