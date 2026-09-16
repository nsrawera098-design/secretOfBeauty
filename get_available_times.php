<?php
require_once 'connectToDB.php';
header('Content-Type: text/html; charset=utf-8');
$conn->set_charset("utf8");

if (!isset($_POST['date']) || !isset($_POST['business_id'])) {
    echo "נתונים חסרים.";
    exit;
}

$business_id = intval($_POST['business_id']);
$date = mysqli_real_escape_string($conn, $_POST['date']);

// שליפת שם העסק לפי מזהה
$businessQuery = "SELECT name FROM businesses WHERE id = $business_id";
$businessResult = $conn->query($businessQuery);

if (!$businessResult || $businessResult->num_rows === 0) {
    echo "לא נמצא עסק עם מזהה זה.";
    exit;
}

$business_name = $businessResult->fetch_assoc()['name'];
$business_name_safe = mysqli_real_escape_string($conn, $business_name);

// שליפת שעות תפוסות
$query = "SELECT appointment_time FROM appointments 
          WHERE business_name = '$business_name_safe' 
          AND appointment_date = '$date'";
$result = $conn->query($query);

$booked_times = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $booked_times[] = $row['appointment_time'];
    }
}

// יצירת כל חצאי השעה מ-09:00 עד 17:00
$all_times = [];
$start = new DateTime('09:00');
$end = new DateTime('17:00');
$interval = new DateInterval('PT30M');

echo '<label>בחר שעה:</label>';
echo '<select name="appointment_time" required>';
echo '<option value="">בחר שעה</option>';

for ($time = clone $start; $time <= $end; $time->add($interval)) {
    $formatted = $time->format('H:i:s');
    $label = $time->format('H:i');
    if (in_array($formatted, $booked_times)) {
        echo "<option value=\"$formatted\">$label (תפוס)</option>";
    } else {
        echo "<option value=\"$formatted\">$label</option>";
    }
}

echo '</select>';
?>
