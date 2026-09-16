<?php
require_once 'connectToDB.php';
include('navbar.php');

$result = $conn->query("SELECT COUNT(*) as count FROM user WHERE signup_date = CURDATE()");
$usersToday = $result ? $result->fetch_assoc()['count'] : 0;
$result = $conn->query("SELECT COUNT(*) as count FROM appointments");
$appointmentsTotal = $result ? $result->fetch_assoc()['count'] : 0;
$popularServices = $conn->query("
    SELECT category, COUNT(*) as total
    FROM appointments
    GROUP BY category
    ORDER BY total DESC
    LIMIT 5
");
$popularTimes = $conn->query("
    SELECT appointment_time, COUNT(*) as total
    FROM appointments
    GROUP BY appointment_time
    ORDER BY total DESC
    LIMIT 5
");
$purchasedProducts = $conn->query("
    SELECT product_id, product_type, COUNT(*) as total
    FROM cart
    GROUP BY product_id, product_type
    ORDER BY total DESC
    LIMIT 5
");
$result = $conn->query("
    SELECT COUNT(DISTINCT email) as total
    FROM appointments
    WHERE DATEDIFF(CURDATE(), created_at) > 30
");
$returningUsers = $result ? $result->fetch_assoc()['total'] : 0;
$appointmentsDetails = $conn->query("
    SELECT full_name, email, phone, category, location, business_name, appointment_date, appointment_time
    FROM appointments
    ORDER BY appointment_date DESC
    LIMIT 10
");
$contactMessages = $conn->query("SELECT reason, COUNT(*) as total FROM contact_messages GROUP BY reason ORDER BY total DESC LIMIT 5");
$availability = $conn->query("
    SELECT appointment_date, COUNT(*) as total 
    FROM appointments 
    WHERE DATE(appointment_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY appointment_date
    ORDER BY appointment_date DESC
");
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>לוח ניתוחים למנהל</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to right, #fff0f5, #ffe6f0); /* ורודים עדינים */
        direction: rtl;
        margin: 0;
        padding: 0;
    }

    .dashboard-container {
        max-width: 1000px;
        margin: 40px auto;
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 20px;
        box-shadow: 0 0 25px rgba(255, 182, 193, 0.3); 
    }
    h2 {
        color: #d63384; 
        font-size: 36px;
        text-align: center;
        margin-bottom: 40px;
        font-weight: bold;
    }
    .section-title {
        color: #a64ca6;
        font-size: 22px;
        margin-top: 30px;
        margin-bottom: 10px;
        border-bottom: 2px solid #f7c6e0;
        padding-bottom: 5px;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    .card {
        background-color: #fff0fa;
        padding: 15px 20px;
        border-radius: 14px;
        margin-bottom: 15px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-3px);
    }

    .highlight {
        color: #ca2c92;
        font-weight: bold;
        background-color: #ffe4f2;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 30px;
        background-color: #f48fb1;
        color: white;
        padding: 10px 25px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.2s;
    }

    .back-link:hover {
        background-color: #ec407a;
        transform: scale(1.05);
    }

    .info-box {
        background-color: #fce4ec;
        padding: 20px;
        border-radius: 14px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        margin-top: 20px;
    }

    .info-box ul li {
        margin: 8px 0;
        color: #6a1b9a;
    }

    .info-box strong {
        color: #8e24aa;
    }
</style>

</head>
<body>
<div class="dashboard-container">
    <h2>📊 לוח ניתוחים למנהל</h2>
    <a href="Adminpage.php" class="back-link">← חזרה לדף הניהול הראשי</a>

    <div class="section-title">סטטיסטיקות כלליות:</div>
    <ul>
        <li>🧍‍♀️ משתמשים חדשים היום: <span class="highlight"><?= $usersToday ?></span></li>
        <li>📅 סך כל התורים: <span class="highlight"><?= $appointmentsTotal ?></span></li>
        <li>🔁 משתמשים שחזרו לאחר תקופה: <span class="highlight"><?= $returningUsers ?></span></li>
    </ul>

    <div class="section-title">⏰ שעות פופולריות לתורים:</div>
    <ul>
        <?php if ($popularTimes && $popularTimes->num_rows > 0): ?>
            <?php while ($row = $popularTimes->fetch_assoc()): ?>
                <li class="card"><?= $row['appointment_time'] ?> - <?= $row['total'] ?> תורים</li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="card">אין נתונים זמינים לשעות.</li>
        <?php endif; ?>
    </ul>

    <div class="section-title">💅 שירותים שהוזמנו הכי הרבה:</div>
    <ul>
        <?php if ($popularServices && $popularServices->num_rows > 0): ?>
            <?php while ($row = $popularServices->fetch_assoc()): ?>
                <li class="card"><?= $row['category'] ?> - <?= $row['total'] ?> פעמים</li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="card">אין שירותים פופולריים כרגע.</li>
        <?php endif; ?>
    </ul>

    <div class="section-title">🛍️ מוצרים פופולריים:</div>
    <ul>
        <?php if ($purchasedProducts && $purchasedProducts->num_rows > 0): ?>
            <?php while ($row = $purchasedProducts->fetch_assoc()): ?>
                <li class="card"><?= $row['product_type'] ?> | מוצר #<?= $row['product_id'] ?> - <?= $row['total'] ?> רכישות</li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="card">אין רכישות מוצרים זמינות.</li>
        <?php endif; ?>
    </ul>

    <div class="section-title">📞 סיבות פופולריות לפניות לשירות לקוחות:</div>
    <ul>
        <?php if ($contactMessages && $contactMessages->num_rows > 0): ?>
            <?php while ($row = $contactMessages->fetch_assoc()): ?>
                <li class="card"><?= $row['reason'] ?> - <?= $row['total'] ?> פניות</li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="card">אין פניות זמינות כרגע.</li>
        <?php endif; ?>
    </ul>

    <div class="section-title">📆 כמות תורים לפי ימים (שבוע אחרון):</div>
    <ul>
        <?php if ($availability && $availability->num_rows > 0): ?>
            <?php while ($row = $availability->fetch_assoc()): ?>
                <li class="card"><?= $row['appointment_date'] ?> - <?= $row['total'] ?> תורים</li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="card">אין נתונים לשבוע האחרון.</li>
        <?php endif; ?>
    </ul>

    <div class="section-title">👤 פרטי תורים אחרונים:</div>
    <ul>
        <?php if ($appointmentsDetails && $appointmentsDetails->num_rows > 0): ?>
            <?php while ($row = $appointmentsDetails->fetch_assoc()): ?>
                <li class="card">
                    <strong><?= $row['full_name'] ?></strong> | <?= $row['email'] ?> | <?= $row['phone'] ?><br>
                    שירות: <?= $row['category'] ?> | עסק: <?= $row['business_name'] ?><br>
                    תאריך: <?= $row['appointment_date'] ?> בשעה <?= $row['appointment_time'] ?> | מיקום: <?= $row['location'] ?>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="card">אין תורים אחרונים להצגה.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>