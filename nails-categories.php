<?php
// nails-categories.php
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nails – קטגוריות</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg: #f7f7f9;
      --card-bg:#ffffff;
      --ink:#222;
      --muted:#666;
      --brand:#ff7fb1;         /* ורוד עדין */
      --brand-2:#ffa3c6;       /* ורוד בהיר יותר */
      --radius:18px;
      --shadow:0 8px 22px rgba(0,0,0,.12);
      --shadow-sm:0 4px 12px rgba(0,0,0,.10);
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      background:var(--bg);
      color:var(--ink);
      font-family:'Poppins',sans-serif;
      text-align:center;
      padding:24px;
    }
    .wrap{max-width:1200px;margin:0 auto}
    h1{
      font-size:34px;
      margin:8px 0 18px;
      font-weight:700;
    }
    .sub{
      color:var(--muted);
      margin-bottom:22px;
    }
    .actions{
      margin-bottom:18px;
      display:flex;gap:10px;justify-content:center;flex-wrap:wrap;
    }
    .btn{
      display:inline-block;
      padding:10px 16px;
      background:var(--brand);
      color:#fff;text-decoration:none;border-radius:10px;
      box-shadow:var(--shadow-sm);
      transition:.25s;
    }
    .btn:hover{transform:translateY(-1px);background:var(--brand-2)}

    .grid{
      display:grid;
      grid-template-columns:repeat(4,1fr);
      gap:18px;
    }
    @media (max-width:1000px){ .grid{grid-template-columns:repeat(2,1fr);} }
    @media (max-width:560px){ .grid{grid-template-columns:1fr;} }

    .card{
      position:relative;
      display:block;
      background:var(--card-bg);
      border-radius:var(--radius);
      overflow:hidden;
      box-shadow:var(--shadow);
      text-decoration:none;
      color:#fff;
      min-height:240px;
      isolation:isolate;
    }
    .card img{
      width:100%;height:100%;object-fit:cover;display:block;
      position:absolute;inset:0;z-index:1;filter:brightness(.7);
      transition:transform .4s ease;
    }
    .card::after{                 /* וינייט עדין */
      content:"";position:absolute;inset:0;z-index:2;
      background:linear-gradient(180deg,rgba(0,0,0,.00),rgba(0,0,0,.35) 40%, rgba(0,0,0,.55));
    }
    .card:hover img{transform:scale(1.05)}
    .card-content{
      position:relative;z-index:3;
      height:100%;
      display:flex;flex-direction:column;align-items:center;justify-content:center;
      padding:20px;gap:12px;text-align:center;
    }
    .card h3{
      font-size:28px;font-weight:700;margin:0;text-shadow:0 2px 10px rgba(0,0,0,.35);
      letter-spacing:.3px;
    }
    .ghost{
      display:inline-block;border:2px solid #fff;border-radius:999px;
      padding:10px 18px;background:transparent;color:#fff;text-decoration:none;
      font-weight:600;transition:.25s;backdrop-filter:blur(2px);
    }
    .ghost:hover{background:rgba(255,255,255,.12)}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>בחרי קטגוריה ב-Nails</h1>
    <p class="sub">לחצי על קטגוריה כדי לראות מוצרים תואמים</p>

    <div class="actions">
      <a class="btn" href="products_nails.php">הצג הכול</a>
      <a class="btn" href="homePage.php">חזרה לדף הבית</a>
      <a class="btn" href="cart.php">🛒 עגלת קניות</a>
    </div>

    <div class="grid">
      <!-- לק -->
      <a class="card" href="products_nails.php?subcat=polish" aria-label="לק">
        <img src="photos/nail-polish.jpg" alt="לק לציפורניים">
        <div class="card-content">
          <h3>לק</h3>
          <span class="ghost">Read more</span>
        </div>
      </a>

      <!-- ג׳ל -->
      <a class="card" href="products_nails.php?subcat=gel" aria-label="ג׳ל">
        <img src="photos/nail-gel.jpg" alt="ג׳ל לציפורניים">
        <div class="card-content">
          <h3>ג׳ל</h3>
          <span class="ghost">Read more</span>
        </div>
      </a>

      <!-- בנייה -->
      <a class="card" href="products_nails.php?subcat=build" aria-label="בנייה">
        <img src="photos/nail-build.jpg" alt="בנייה וחיזוק">
        <div class="card-content">
          <h3>בנייה</h3>
          <span class="ghost">Read more</span>
        </div>
      </a>

      <!-- כלים ואביזרים -->
      <a class="card" href="products_nails.php?subcat=tools" aria-label="כלים ואביזרים">
        <img src="photos/nail-tools.jpg" alt="כלים ואביזרים לציפורניים">
        <div class="card-content">
          <h3>כלים ואביזרים</h3>
          <span class="ghost">Read more</span>
        </div>
      </a>
    </div>
  </div>
</body>
</html>
