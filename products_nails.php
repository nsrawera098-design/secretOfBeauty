<?php
require_once 'connectToDB.php';
include('navbar.php');
include_once 'PROstock.php';
checkStockAndSendEmail();

if (!isset($_SESSION)) { session_start(); }

// תת-קטגוריה מה-URL (או הכל)
$subcat = isset($_GET['subcat']) ? $_GET['subcat'] : '';

// בסיס השאילתה
$select_sql = "SELECT * FROM Products_nails";

// סינון לפי תת-קטגוריה
if ($subcat !== '') {
    $safeSubcat = mysqli_real_escape_string($conn, $subcat);
    $select_sql .= " WHERE category = '$safeSubcat'";
}

// מיון
$select_sql .= " ORDER BY price DESC";

$result = $conn->query($select_sql);
if ($result === FALSE) { die("SQL Error: " . $conn->error); }
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>Products – Nails</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f7fb;
      --card:#ffffff;
      --ink:#1f2328;
      --muted:#69707a;
      --brand:#ff7fb1;
      --brand-2:#ffa6c9;
      --ok:#28a745;
      --danger:#e53935;
      --radius:16px;
      --shadow:0 10px 24px rgba(0,0,0,.10);
      --shadow-sm:0 6px 16px rgba(0,0,0,.08);
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:'Poppins',system-ui,Arial,sans-serif;
      background:var(--bg);
      color:var(--ink);
    }
    .wrap{max-width:1200px;margin:0 auto;padding:22px}
    /* כותרת ודפדוף */
    .page-head{
      display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
      margin:6px 0 12px;
    }
    .title{
      display:flex;align-items:center;gap:10px;flex-wrap:wrap;
    }
    .title h1{font-size:28px;margin:0}
    .badge{
      padding:6px 10px;border-radius:999px;background:#fff;border:1px solid #eaecef;color:var(--muted);font-size:14px
    }
    .actions{display:flex;gap:10px;flex-wrap:wrap}
    .btn{
      display:inline-block;padding:10px 14px;border-radius:10px;text-decoration:none;font-weight:600;
      background:var(--brand);color:#fff;box-shadow:var(--shadow-sm);transition:.2s;
    }
    .btn:hover{transform:translateY(-1px);background:var(--brand-2)}
    .btn.ghost{background:#fff;color:var(--ink);border:1px solid #e9e9ef}
    .btn.ghost:hover{background:#fafafa}

    /* גריד מוצרים */
    .grid{
      display:grid;
      grid-template-columns:repeat(4,1fr);
      gap:18px;
      margin-top:16px
    }
    @media (max-width:1100px){ .grid{grid-template-columns:repeat(3,1fr);} }
    @media (max-width:800px){ .grid{grid-template-columns:repeat(2,1fr);} }
    @media (max-width:520px){ .grid{grid-template-columns:1fr;} }

    .card{
      background:var(--card);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      overflow:hidden;
      display:flex;flex-direction:column;
      min-height:380px;
    }
    .thumb{
      position:relative;background:#fff;
      height:190px;                /* <<< גובה תמונה קבוע */
      display:flex;align-items:center;justify-content:center;
      border-bottom:1px solid #f0f1f4;
    }
    .thumb img{
      max-width:100%;
      max-height:100%;
      object-fit:contain;          /* <<< לא "נמתח" את התמונה */
      display:block;
    }
    .price-tag{
      position:absolute;left:12px;top:12px;
      background:rgba(0,0,0,.6);color:#fff;border-radius:10px;padding:6px 8px;font-weight:600;font-size:14px;
    }
    .content{padding:14px 14px 0;display:flex;flex-direction:column;gap:8px;flex:1}
    .name{font-size:16px;font-weight:700;margin:0;line-height:1.35}
    .muted{color:var(--muted);font-size:13px}

    .stock{margin-top:auto;padding:0 14px 14px;font-size:14px}
    .stock.ok{color:var(--ok)}
    .stock.bad{color:var(--danger);font-weight:700}

    .cart{
      display:flex;align-items:center;gap:8px;margin:0 14px 16px;
    }
    .qty{
      width:64px;height:38px;border:1px solid #e2e5ea;border-radius:10px;text-align:center;font-size:14px;
    }
    .add{
      flex:1;height:38px;border:0;border-radius:10px;background:var(--brand);color:#fff;font-weight:700;cursor:pointer;
      box-shadow:var(--shadow-sm);transition:.2s;
    }
    .add:hover{transform:translateY(-1px);background:var(--brand-2)}
    .empty{padding:40px;text-align:center;color:var(--muted)}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="page-head">
      <div class="title">
        <h1>🛍️ מוצרי Nails <?= $subcat !== '' ? '– ' . htmlspecialchars($subcat) : '' ?></h1>
        <?php if ($subcat !== ''): ?>
          <span class="badge">סינון לפי: <?= htmlspecialchars($subcat) ?></span>
        <?php endif; ?>
      </div>
      <div class="actions">
        <a class="btn" href="nails-categories.php">⬅ חזרה לקטגוריות</a>
        <a class="btn ghost" href="products_nails.php">הצג הכל</a>
        <a class="btn ghost" href="cart.php">🛒 עגלת קניות</a>
      </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="grid">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="card">
            <div class="thumb">
              <img src="<?= htmlspecialchars($row['image']) ?>"
                   alt="<?= htmlspecialchars($row['productN_name']) ?>">
              <div class="price-tag">₪<?= htmlspecialchars($row['price']) ?></div>
            </div>

            <div class="content">
              <h3 class="name"><?= htmlspecialchars($row['productN_name']) ?></h3>
              <div class="muted"><?= $subcat ? 'קטגוריה: ' . htmlspecialchars($subcat) : '&nbsp;' ?></div>
            </div>

            <?php if ($row['quantity'] > 0): ?>
              <div class="stock ok">כמות במלאי: <?= (int)$row['quantity'] ?></div>
              <form class="cart" method="get" action="addToCart.php">
                <input type="hidden" name="product_id" value="<?= (int)$row['productN_id'] ?>">
                <input type="hidden" name="product_type" value="nails">
                <input type="number" class="qty" id="quantity_<?= (int)$row['productN_id'] ?>"
                       name="quantity" value="1" min="1" max="<?= (int)$row['quantity'] ?>">
                <button type="submit" class="add">➕ הוסף לעגלה</button>
              </form>
            <?php else: ?>
              <div class="stock bad">❌ אזל מהמלאי</div>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="empty">🚫 לא נמצאו מוצרים בקטגוריה המבוקשת.</p>
    <?php endif; ?>
  </div>
</body>
</html>
