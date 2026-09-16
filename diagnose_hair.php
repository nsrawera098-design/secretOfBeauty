<?php
session_start();
include("connectToDB.php");
include("navbar.php");

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// בדוק אם יש כבר אבחון שיער
$check = mysqli_query($conn, "SELECT id FROM hair_diagnosis WHERE email='$email'");
if (mysqli_num_rows($check) > 0) {
    // אם כבר יש אבחון, הפנה לדף התוצאות
    header("Location: hair_result.php");
    exit();
}

function clean_input($conn, $input) {
    return mysqli_real_escape_string($conn, trim($input));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email'];
    $created_at = date('Y-m-d H:i:s');

    $full_name = clean_input($conn, $_POST['full_name']);
    $age = clean_input($conn, $_POST['age']);
    $hair_type = clean_input($conn, $_POST['hair_type']);
    $hair_loss = clean_input($conn, $_POST['hair_loss']);
    $scalp_sensitivity = clean_input($conn, $_POST['scalp_sensitivity']);
    $hair_goal = clean_input($conn, $_POST['hair_goal']);
    $additional_info = clean_input($conn, $_POST['additional_info']);
    $current_products = clean_input($conn, $_POST['current_products'] ?? '');
    $wash_frequency = clean_input($conn, $_POST['wash_frequency'] ?? '');
    $uses_heat_tools = clean_input($conn, $_POST['uses_heat_tools'] ?? '');

    // העלאת תמונה
    $hair_image_path = null;
    if (isset($_FILES['hair_image']) && $_FILES['hair_image']['error'] === 0) {
        $upload_dir = "uploads/hair/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $ext = pathinfo($_FILES["hair_image"]["name"], PATHINFO_EXTENSION);
        $image_name = uniqid() . "." . $ext;
        $target_file = $upload_dir . $image_name;
        if (move_uploaded_file($_FILES["hair_image"]["tmp_name"], $target_file)) {
            $hair_image_path = $target_file;
        }
    }

    // בדיקה אם כבר יש אבחון
    $check = mysqli_query($conn, "SELECT id FROM hair_diagnosis WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        // עדכון
        $query = "UPDATE hair_diagnosis SET full_name='$full_name', age='$age', hair_type='$hair_type', scalp_condition='$scalp_sensitivity', issues='$hair_loss', goal='$hair_goal', additional_info='$additional_info', current_products='$current_products', wash_frequency='$wash_frequency', uses_heat_tools='$uses_heat_tools', created_at='$created_at'";
        if ($hair_image_path) {
            $query .= ", hair_image='" . mysqli_real_escape_string($conn, $hair_image_path) . "'";
        }
        $query .= " WHERE email='$email'";
    } else {
        // הוספה
        $query = "INSERT INTO hair_diagnosis (full_name, email, age, hair_type, scalp_condition, issues, goal, hair_image, additional_info, current_products, wash_frequency, uses_heat_tools, created_at) 
        VALUES ('$full_name', '$email', '$age', '$hair_type', '$scalp_sensitivity', '$hair_loss', '$hair_goal', " . ($hair_image_path ? "'".mysqli_real_escape_string($conn, $hair_image_path)."'" : "NULL") . ", '$additional_info', '$current_products', '$wash_frequency', '$uses_heat_tools', '$created_at')";
    }

    if (mysqli_query($conn, $query)) {
        mysqli_query($conn, "UPDATE user SET has_diagnosed_hair = 1 WHERE email='$email'");
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'האבחון נשמר בהצלחה!',
                text: 'תודה שהקדשת זמן למלא את אבחון השיער 😊',
                confirmButtonText: 'למוצרים המתאימים לי'
            }).then(() => {
                window.location.href = 'hair_result.php';
            });
        </script>";
        exit();
    } else {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'שגיאה!',
                text: 'אירעה שגיאה בשמירת האבחון. נסי שוב מאוחר יותר.',
                confirmButtonText: 'בסדר'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>אבחון שיער</title>
    <style>
 body {
    background:  url('photos/hai5.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    padding: 30px;
    direction: rtl;
}

    .form-container {
        max-width: 700px;
        margin: 0 auto;
        background: #ffffff;
        padding: 35px;
        border-radius: 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid #f8bbd0;
        backdrop-filter: blur(6px);
    }

    h1 {
        text-align: center;
        color: #c2185b;
        font-size: 28px;
        margin-bottom: 25px;
    }

    label {
        font-weight: 600;
        margin-top: 15px;
        display: block;
        color: #6a1b9a;
    }

    input, select, textarea {
        width: 100%;
        padding: 12px;
        margin-top: 5px;
        border-radius: 10px;
        border: 1px solid #e1bee7;
        background-color: #fce4ec;
        margin-bottom: 20px;
        font-size: 15px;
        color: #4a148c;
    }

    input::placeholder, textarea::placeholder {
        color: #9c27b0;
        opacity: 0.6;
    }

    .radio-group {
        display: flex;
        gap: 20px;/* ריווח בין האלמנטים */
        margin-bottom: 20px;
        align-items: center;
    }

    .radio-group label {
        font-weight: normal;
        margin: 0 10px 0 0;
        color: #7b1fa2;
    }
    .radio-option {
    display: flex;
    align-items: center;
    gap: 5px; /* ריווח בין העיגול למילה */
}

.radio-option label {
    margin: 0;
    color: #7b1fa2;
}

    .question-label {
        font-weight: bold;
        color: #6a1b9a;
        display: block;
        margin-bottom: 5px;
    }

     input[type="radio"] {
        accent-color: #ab47bc; /* צבע מותאם לרדיו */
    }

    button {
        background-color: #ec407a;
        color: white;
        padding: 14px;
        width: 100%;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background-color: #ad1457;
    }
</style>

    </style>
</head>
<body>

<div class="form-container">
    <h1>אבחון שיער</h1>
    <form action="" method="POST" enctype="multipart/form-data">

        <label for="hair_type">סוג שיער:</label>
        <select name="hair_type" id="hair_type" required>
            <option value="">בחר</option>
            <option value="שומני">שומני</option>
            <option value="יבש">יבש</option>
            <option value="מתולתל">מתולתל</option>
            <option value="חלק">חלק</option>
            <option value="צבוע">צבוע</option>
        </select>

<label class="question-label">נשירת שיער:</label>
<div class="radio-group">
    <span class="radio-option">
        <input type="radio" id="hair_loss_yes" name="hair_loss" value="כן" required>
        <label for="hair_loss_yes">כן</label>
    </span>

    <span class="radio-option">
        <input type="radio" id="hair_loss_no" name="hair_loss" value="לא" required>
        <label for="hair_loss_no">לא</label>
    </span>
</div>

<label class="question-label">רגישות בקרקפת/קשקשים:</label>
<div class="radio-group">
    <span class="radio-option">
        <input type="radio" id="scalp_yes" name="scalp_sensitivity" value="כן" required>
        <label for="scalp_yes">כן</label>
    </span>

    <span class="radio-option">
        <input type="radio" id="scalp_no" name="scalp_sensitivity" value="לא" required>
        <label for="scalp_no">לא</label>
    </span>
</div>


        <label for="hair_goal">מה תרצי להשיג בטיפול שיער?</label>
        <textarea name="hair_goal" id="hair_goal" rows="3" placeholder="לדוג': נפח, ברק, ריכוך..."></textarea>

        <label for="current_products">באילו מוצרים את משתמשת כיום?</label>
        <input type="text" name="current_products" id="current_products">

        <label for="wash_frequency">באיזו תדירות את חופפת?</label>
        <input type="text" name="wash_frequency" id="wash_frequency">

        <label for="uses_heat_tools">האם את משתמשת בכלי חום (פן/מחליק)?</label>
        <input type="text" name="uses_heat_tools" id="uses_heat_tools">

        <label for="additional_info">מידע נוסף שחשוב לנו לדעת?</label>
        <textarea name="additional_info" id="additional_info" rows="3"></textarea>

        <label for="hair_image">תמונת שיער (אופציונלי):</label>
        <input type="file" name="hair_image" id="hair_image" accept="image/*">

        <button type="submit">שלחי אבחון</button>
    </form>
</div>

</body>
</html>
