<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("connectToDB.php");

$profileImage = 'uploads/profile.png';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $res = mysqli_query($conn, "SELECT profile_image FROM user WHERE email = '$email'");
    
    if ($res && mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
        $imagePath = "uploads/" . $data['profile_image'];

        if (!empty($data['profile_image']) && file_exists($imagePath)) {
            $profileImage = $imagePath;
        }
    }
}
?>

<link rel="stylesheet" href="navbar.css">

<!-- תפריט צד -->
<div class="menu-toggle" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
</div>

<nav id="sideNav">
    <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile">
    <a href="profile.php">עדכון פרופיל</a>
    <a href="homepage.php">🏠 דף הבית</a>

    <div class="dropdown">
        <a href="#">🛍️ מוצרים</a>
        <div class="dropdown-content">
            <a href="products_hair.php">💇🏻‍♀️✄מוצרי שיער</a>
            <a href="pro_cosmetics.php">💆🏻‍♀️קוסמטיקה</a>
            <a href="products_nails.php">💄ציפורניים</a>
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

<div id="overlay" onclick="toggleMenu()"></div>

<script>
function toggleMenu() {
    document.getElementById('sideNav').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('show');
}
</script>