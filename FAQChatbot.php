<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'משתמש';
}
include('navbar.php');

// שאלות נפוצות על מוצרים, טיפולים ושיער
$questions = [
    'מה ההבדל בין מוצרים לקוסמטיקה לשיער?' => 'מוצרי קוסמטיקה מיועדים לעור הפנים והגוף, בעוד שמוצרי שיער כוללים שמפו, מסכות וטיפולים לחיזוק השיער.',
    'האם יש מוצרים טבעיים בחנות?' => 'כן! אנחנו מציעים מגוון מוצרים המבוססים על רכיבים טבעיים, ללא חומרים מזיקים.',
    'איך לבחור מסכת שיער מתאימה?' => 'לבחירה נכונה יש לבדוק את סוג השיער – יבש, צבוע, מתולתל או פגום – ולהתאים מוצר בהתאם.',
    'האם ניתן להזמין תור לטיפול דרך האתר?' => 'בוודאי! ניתן להזמין תור דרך עמוד "זימון תור" ולבחור את סוג הטיפול, המקום והשעה.',
    'האם אתם מבצעים משלוחים?' => 'כן, אנו מבצעים משלוחים לכל הארץ תוך 3-5 ימי עסקים.',
    'מה אפשר לעשות לשיער דליל?' => 'מומלץ להשתמש בסרומים מעוררי צמיחה, לאכול בריא ולעבור טיפולים משקמים אצל מומחה.',
    'האם יש מבצעים או הנחות?' => 'כן, עקוב/י אחר עמוד הבית או ההודעות כדי לראות מבצעים ועדכונים שוטפים.',
];

$response = "";
$selectedQuestion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedQuestion = $_POST['question'] ?? '';
    if (isset($questions[$selectedQuestion])) {
        $response = $questions[$selectedQuestion];
    } else {
        $response = 'סליחה, לא זיהינו את השאלה. נסה לבחור שאלה מהרשימה.';
    }
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
  <meta charset="UTF-8">
  <title>💬 צ'אט אוטומטי למוצרים וטיפולים</title>
  <style>
    body {
      font-family: Arial;
      background-color: #fff0f5;
      direction: rtl;
      padding: 40px;
    }
    .chat-box {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 20px;
      text-align: center;
      color: #d63384;
    }
    .question-btn {
      display: block;
      background-color: #ff69b4;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 10px;
      margin: 10px 0;
      cursor: pointer;
      font-size: 16px;
    }
    .question-btn:hover {
      background-color: #ec407a;
    }
    .response {
      margin-top: 25px;
      background-color: #fff0f6;
      padding: 20px;
      border-radius: 10px;
      font-size: 17px;
      line-height: 1.6;
    }
    .nav-buttons {
      margin-top: 30px;
      text-align: center;
    }
    .nav-buttons a {
      display: inline-block;
      background-color: #fcbad3;
      color: #333;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 10px;
      margin: 10px;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }
    .nav-buttons a:hover {
      background-color: #faa0c7;
    }
  </style>
</head>
<body>
  <div class="chat-box">
    <h2>🤖 שאל שאלה על טיפולים, מוצרים ושיער</h2>

    <?php if ($response): ?>
      <div class="response">
        <strong>את/ה:</strong> <?php echo htmlspecialchars($selectedQuestion); ?><br>
       <strong>הנציג:</strong>
       <?php echo htmlspecialchars($response); ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <?php foreach ($questions as $q => $a): ?>
        <button class="question-btn" type="submit" name="question" value="<?php echo htmlspecialchars($q); ?>">
          <?php echo $q; ?>
        </button>
      <?php endforeach; ?>
    </form>

    <div class="nav-buttons">
      <a href="homepage.php">🏠 חזרה לדף הבית</a>
      <a href="contact.php">✉️ צור קשר</a>
    </div>
  </div>
</body>
</html>
