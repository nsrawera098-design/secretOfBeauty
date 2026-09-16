<?php
require_once 'connectToDB.php';
header('Content-Type: text/html; charset=utf-8');
$conn->set_charset("utf8");

include('navbar.php');

$error = '';
$business = null;

if (!isset($_POST['business_id'])) {
    $error = "מזהה העסק לא נשלח. חזור לעמוד הקודם.";
} else {
    $business_id = intval($_POST['business_id']);

    $sql = "SELECT * FROM businesses WHERE id = $business_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $business = $result->fetch_assoc();
    } else {
        $error = "העסק לא נמצא.";
    }
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>זימון תור<?php if ($business) echo ' - ' . htmlspecialchars($business['name']); ?></title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin-top: 50px;
            direction: rtl;
        }
        .error {
            color: #b30000;
            background-color: #ffe6e6;
            border: 1px solid #ffcccc;
            padding: 20px;
            margin: 30px auto;
            border-radius: 10px;
            max-width: 500px;
            font-size: 18px;
        }
        form {
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            border: 1px solid #ccc;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, select, textarea, button {
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            width: 100%;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            background-color: #ff85a2;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/he.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php else: ?>

<h2>פרטי לקוח</h2>
<form method="post" action="confirm_appointment.php" onsubmit="return validateForm()">
    <input type="hidden" name="business_id" value="<?php echo $business['id']; ?>">
    <input type="hidden" name="category" value="<?php echo htmlspecialchars($business['category']); ?>">
    <input type="hidden" name="location" value="<?php echo htmlspecialchars($business['location']); ?>">
    <input type="hidden" name="business_name" value="<?php echo htmlspecialchars($business['name']); ?>">

    <label>שם מלא:</label>
    <input type="text" name="full_name" required>

    <label>טלפון:</label>
    <input type="tel" name="phone" required>

    <label>אימייל:</label>
    <input type="email" name="email" required>

    <label>בחר תאריך:</label>
    <input type="text" name="appointment_date" id="datePicker" required>

    <div id="availableTimes"></div>

    <textarea name="comments" placeholder="הערות נוספות (לא חובה)"></textarea>
    <button type="submit">אישור תור</button>
</form>

<script>
function validateForm() {
    const timeSelect = document.querySelector('select[name="appointment_time"]');
    if (!timeSelect || !timeSelect.value) {
        alert("אנא בחר/י שעה לפני המשך.");
        return false;
    }
    return true;
}

flatpickr("#datePicker", {
    locale: "he",
    minDate: "today",
    dateFormat: "Y-m-d",
    onChange: function(selectedDates, dateStr) {
        $('#availableTimes').html('<p>טוען שעות...</p>');
        $.post('get_available_times.php', {
            date: dateStr,
            business_id: "<?php echo $business['id']; ?>"
        }, function(data) {
            $('#availableTimes').html(data);
        });
    }
});
</script>

<?php endif; ?>
</body>
</html>
