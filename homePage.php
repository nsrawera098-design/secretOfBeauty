<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>דף הבית</title>

    <!-- Google Translate -->
    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'he',
            includedLanguages: 'he,en,ar',
            layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL
        }, 'google_translate_element');
    }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- עיצוב כללי -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', 'Poppins', sans-serif; }
        body { margin: 0; font-family: 'Varela Round', sans-serif; direction: rtl; background: #fff; text-align: center; }

        /* כפתור המבורגר */
        .menu-toggle { position: fixed; top: 20px; right: 20px; z-index: 1101; cursor: pointer; width: 30px; height: 22px; display: flex; flex-direction: column; justify-content: space-between; }
        .menu-toggle span { height: 4px; background: #e91e63; border-radius: 4px; }

        /* תפריט צד */
        nav { background-color: pink; width: 220px; height: 100vh; position: fixed; right: -250px; top: 0; display: flex; flex-direction: column; align-items: center; padding-top: 20px; color: white; z-index: 1100; transition: right 0.3s ease; }
        nav.active { right: 0; }
        nav a { color: white; text-decoration: none; margin: 10px 0; display: block; text-align: center; width: 100%; padding: 8px 0; }
        nav img { width: 130px; height: 130px; border-radius: 50%; margin-bottom: 10px; border: 2px solid white; object-fit: cover; }

        .dropdown { width: 100%; text-align: center; position: relative; }
        .dropdown-content { display: none; flex-direction: column; background-color: pink; padding: 10px 0; }
        .dropdown:hover .dropdown-content { display: flex; }
        .dropdown-content a { font-size: 14px; }

        #overlay { position: fixed; top: 0; right: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); z-index: 1000; display: none; }
        #overlay.active { display: block; }

        .hero { background-image: url('photos/co1.webp'); background-size: cover; background-position: center; padding: 120px 20px; color: white; position: relative; }
        .hero::after { content: ''; background: rgba(0, 0, 0, 0.4); position: absolute; inset: 0; }
        .hero-content { position: relative; z-index: 1; }
        .hero h1 { font-size: 48px; margin-bottom: 10px; }
        .hero p { font-size: 18px; margin-bottom: 30px; }
        .hero-buttons .btn { margin: 0 10px; padding: 12px 25px; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn.pink { background-color: #ec407a; color: white; border: none; }
        .btn.outline { background: transparent; border: 2px solid white; color: white; }

        .categories { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px; padding: 30px; }
        .category { height: 250px; background-size: cover; background-position: center; position: relative; border-radius: 10px; overflow: hidden; }
        .category .overlay { background-color: rgba(0,0,0,0.4); color: white; height: 100%; width: 100%; position: absolute; top: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .category h3 { font-size: 22px; margin-bottom: 10px; }
        .category a { color: white; border: 1px solid white; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-size: 14px; }

        .welcome { margin-top: 30px; font-size: 18px; }
        .actions { margin: 30px 0; }
        .actions .btn { margin: 10px; padding: 12px 20px; background-color: #f78fb3; color: white; border: none; border-radius: 25px; font-size: 15px; text-decoration: none; }
    </style>
</head>
<body>

<!-- כפתור תפריט -->
<div class="menu-toggle" onclick="toggleMenu()">
    <span></span><span></span><span></span>
</div>

<!-- תפריט צד -->
<nav id="sideNav">
    <img src="uploads/profile.png" alt="Profile">
    <a href="profile.php">עדכון פרופיל</a>
    <a href="homepage.php">🏠 דף הבית</a>
    
    <div class="dropdown">
        <a href="#">🛍️ מוצרים</a>
        <div class="dropdown-content">
            <a href="products_hair.php">💇🏻‍♀️✄מוצרי שיער</a>
            <a href="pro_cosmetics.php">💆🏻‍♀️קוסמטיקה</a>
            <!-- נשאר מפנה לקטגוריות ציפורניים -->
            <a href="nails-categories.php">ציפורניים</a>
        </div>
    </div>
    <a href="cart.php">🛒 עגלה</a>
    <a href="appointment_history.php">📅 היסטוריית תורים</a>
    <a href="orderHistory.php">🛍️ היסטוריית רכישות</a>

    <div class="dropdown">
        <a href="#">⚙️ הגדרות</a>
        <div class="dropdown-content">
            <a href="#" onclick="increaseFontSize()">🔠 הגדלת טקסט</a>
            <a href="#" onclick="decreaseFontSize()">🔡 הקטנת טקסט</a>
            <a href="chatAI.php">💬 צ'אט עם נציג</a>
        </div>
    </div>
    <a href="logout.php">⛔ Logout</a>
</nav>

<!-- רקע כהה מאחור -->
<div id="overlay" onclick="toggleMenu()"></div>

<!-- סקריפטים -->
<script>
    function toggleMenu() {
        const nav = document.getElementById("sideNav");
        const overlay = document.getElementById("overlay");
        nav.classList.toggle("active");
        overlay.classList.toggle("active");
    }
    function increaseFontSize() {
        const body = document.body;
        const currentSize = parseFloat(window.getComputedStyle(body).fontSize);
        body.style.fontSize = (currentSize + 2) + "px";
    }
    function decreaseFontSize() {
        const body = document.body;
        const currentSize = parseFloat(window.getComputedStyle(body).fontSize);
        if (currentSize > 10) body.style.fontSize = (currentSize - 2) + "px";
    }
</script>

<!-- תוכן הדף -->
<div class="hero">
  <div class="hero-content">
    <h1>Welcome to Our Beauty Project</h1>
    <p>This project is a beauty website that includes user management, products, appointments, employees, and diagnostics.</p>
    <h2>Project Team</h2>
    <p>shimaa , rayan</p>
    <div class="hero-buttons">
      <a href="contact.php" class="btn pink">מידע נוסף,צור קשר 📧</a>
      <a href="location.php" class="btn outline">קביעת תור</a>
    </div>
  </div>
</div>

<div class="categories">
  <div class="category" style="background-image: url('photos/co22.jpg')">
    <div class="overlay">
      <h3>beauty cosmetics</h3>
      <a href="pro_cosmetics.php">Read more</a>
    </div>
  </div>

  <div class="category" style="background-image: url('photos/ha2.png')">
    <div class="overlay">
      <h3>Hair</h3>
      <a href="products_hair.php">Read more</a>
    </div>
  </div>

  <div class="category" style="background-image: url('photos/na3.png')">
    <div class="overlay">
      <h3>Nails</h3>
      <!-- כאן התיקון: מפנה לדף הקטגוריות של Nails -->
      <a href="nails-categories.php">Read more</a>
    </div>
  </div>

  <div class="category" style="background-image: url('photos/alo0.png')">
    <div class="overlay">
      <h3>Time For You</h3>
      <a href="TimeForYou.php">Read more</a>
    </div>
  </div>
</div>

<!-- ברוכה הבאה -->
<div class="welcome">
  <p>שלום <strong><?php echo $_SESSION['username']; ?></strong> 👋 ברוכה הבאה!</p>
</div>

<!-- כפתורים -->
<div class="actions">
  <a href="feedback.php" class="btn">🗣️ שיתוף חוויה</a>
</div>

</body>
</html>
