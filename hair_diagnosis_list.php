<?php
include 'db.php';
session_start();
include('navbar.php');

// סינון לפי תאריך
$where = [];
if (!empty($_GET['start_date'])) {
    $start = $_GET['start_date'] . ' 00:00:00';
    $where[] = "created_at >= '$start'";
}
if (!empty($_GET['end_date'])) {
    $end = $_GET['end_date'] . ' 23:59:59';
    $where[] = "created_at <= '$end'";
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// שאילתה עם מיון לפי תאריך יורד
$query = "SELECT * FROM hair_diagnosis $where_sql ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>רשימת אבחוני שיער</title>
    <style>
        body { font-family: Arial; direction: rtl; padding: 20px; background-color: #f9f9f9; }
        h2 { color: #333; }
        form { margin-bottom: 20px; }
        label { margin-left: 10px; }
        input[type="date"] { padding: 5px; margin-left: 5px; }
        button { padding: 6px 12px; background-color: #ff69b4; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #ff1493; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #ff69b4; color: white; }
        img { max-width: 100px; height: auto; }

.back-button {
    display: inline-block;
    margin-bottom: 20px;
    padding: 8px 16px;
    background-color: #ffb6c1; /* ורוד עדין */
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    border: none;
    border-radius: 25px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: background-color 0.3s ease;
}
.back-button:hover {
    background-color: #ff69b4; /* ורוד בוהק יותר */
}


    </style>
</head>
<body>

<a href="admin_products.php" class="back-button">חזרה לניהול מוצרים</a>

<h2>רשימת אבחוני שיער</h2>

<!-- טופס סינון לפי תאריך -->
<form method="GET">
    <label for="start_date">מתאריך:</label>
    <input type="date" name="start_date" value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '' ?>">

    <label for="end_date">עד תאריך:</label>
    <input type="date" name="end_date" value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '' ?>">

    <button type="submit">סנן</button>
</form>

<table>
   <thead>
<tr>
    <th>#</th>
    <th>שם מלא</th>
    <th>אימייל</th>
    <th>גיל</th>
    <th>סוג שיער</th>
    <th>מצב הקרקפת</th>
    <th>מטרות</th>
    <th>מוצרים נוכחיים</th>
    <th>תדירות חפיפה</th>
    <th>משתמשת בכלים חמים?</th>
    <th>תמונה</th>
    <th>תאריך אבחון</th>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['full_name']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['age']); ?></td>
            <td><?= htmlspecialchars($row['hair_type']); ?></td>
            <td><?= htmlspecialchars($row['scalp_condition']); ?></td>
            <td><?= htmlspecialchars($row['goal']); ?></td>
            <td><?= htmlspecialchars($row['current_products']); ?></td>
            <td><?= htmlspecialchars($row['wash_frequency']); ?></td>
            <td><?= htmlspecialchars($row['uses_heat_tools']); ?></td>
            <td>
                <?php if (!empty($row['hair_image'])): ?>
                    <img src="<?= htmlspecialchars($row['hair_image']); ?>" alt="תמונה">
                <?php else: ?>
                    אין תמונה
                <?php endif; ?>
            </td>
            <td>
                <?= date('d/m/Y H:i', strtotime($row['created_at'])); ?>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="12">אין אבחונים במערכת בטווח התאריכים שבחרת.</td>
    </tr>
<?php endif; ?>
</tbody>
</table>

</body>
</html>
