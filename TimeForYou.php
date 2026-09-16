<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <title>Time For You</title>
  <style>
    body {
      font-family: 'Heebo', sans-serif;
      background: linear-gradient(to right, #fce4ec, #f8bbd0);
      color: #333;
      text-align: center;
      padding: 50px;
    }

    h1 {
      font-size: 2.8em;
      color: #880e4f;
      margin-bottom: 30px;
    }

    form, .suggestion-box {
      margin: 30px auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      max-width: 450px;
    }

    label {
      display: block;
      margin-bottom: 12px;
      font-weight: bold;
      font-size: 1.2em;
    }

    select, button {
      padding: 10px;
      width: 100%;
      border-radius: 8px;
      font-size: 1em;
    }

    select {
      border: 2px solid #f06292;
    }

    button {
      margin-top: 20px;
      background-color: #ec407a;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
      font-weight: bold;
    }

    button:hover {
      background-color: #d81b60;
    }

    .order-button {
      margin-top: 20px;
      display: inline-block;
      padding: 10px 20px;
      background-color: #7b1fa2;
      color: #fff;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
    }

    .order-button:hover {
      background-color: #6a1b9a;
    }

    .back-button {
      display: inline-block;
      margin-top: 20px;
      background-color: #ba68c8;
      color: white;
      padding: 10px 22px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    .back-button:hover {
      background-color: #ab47bc;
    }
  </style>
</head>
<body>

<h1>⏳ כמה זמן יש לך ליופי?</h1>

<form method="POST">
  <label for="minutes">בחרי את הזמן הפנוי שלך:</label>
  <select name="minutes" id="minutes">
    <option value="5">5 דקות</option>
    <option value="15">15 דקות</option>
    <option value="30">30 דקות</option>
  </select>
  <button type="submit">קבלי הצעה</button>
</form>

<?php
include 'connectToDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $minutes = intval($_POST['minutes']);

  $stmt = $conn->prepare("SELECT suggestion FROM beauty_suggestions WHERE minutes = ?");
  $stmt->bind_param("i", $minutes);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $row = $result->fetch_assoc()) {
    $suggestion = htmlspecialchars($row['suggestion']);

    // החלף את 1 למזהה העסק האמיתי שלך
    $business_id = 1;

    echo "<div class='suggestion-box'>
            💡 $suggestion
            <br><br>
            <a class='order-button' href='appointment.php?business_id=$business_id&suggestion=" . urlencode($suggestion) . "'>הזמיני תור</a>
          </div>";
  } else {
    echo "<div class='suggestion-box'>לא נמצאה הצעה לזמן זה.</div>";
  }

  $stmt->close();
  $conn->close();
}
?>

<a href='javascript:history.back()' class='back-button'>🔙 חזרה</a>

</body>
</html>
