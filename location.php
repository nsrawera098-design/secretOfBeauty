<?php 
include('navbar.php');
?>

<!DOCTYPE html>
<html lang="he">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>בחירת אזור</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            color: #333;
            text-align: center;
            padding: 40px 20px;
            direction: rtl;
        }

        .container {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #222;
        }

        select, button {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            width: 100%;
        }

        button {
            background-color: #ff6b6b;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #ff4757;
        }
    </style>
</head>
<body>



    <header>
        <h1>בחר את אזור השירות</h1>
    </header>

    <div class="container">
        <h2>בחר אזור</h2>
        <form action="category.php" method="POST">
            <select name="location" required>
                <option value="חיפה">חיפה</option>
                <option value="ירכא">ירכא</option>
                <option value="כרמיאל">כרמיאל</option>
            </select>
            <button type="submit">הבא</button>
        </form>
    </div>

    <footer>
        <p>&copy; Secret Beauty</p>
    </footer>

</body>
</html>
