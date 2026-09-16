<?php
session_start();// תמיד בתחילת הקובץ, לפני כל הדפסה

// כאן אפשר להוסיף קוד PHP נוסף אם צריך
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>הרשמה</title>
    <style>
        @import url('photos/aa.png');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            direction: rtl;
            font-family: 'Varela Round', sans-serif;
            background: linear-gradient(to right, #ff758c, #ff7eb3);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            display: flex;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 950px;
            width: 100%;
        }

        .image-side {
            flex: 1;
            background-color: #ffeef5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .image-side img {
            width: 90%;
            max-width: 400px;
        }

        .form-side {
            flex: 1;
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-side h1 {
            margin-bottom: 25px;
            font-size: 32px;
        }

        .form-side label {
            margin-top: 10px;
            display: block;
            font-size: 15px;
        }

        .form-side input {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            background: #fff;
            color: #333;
        }

        .form-side button {
           display: inline-block;
            margin-top: 20px;
            background-color: #ff4757;
            color: white;
            padding: 10px 175px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .form-side button:hover {
            background: #ff75afcf;
            transform: scale(1.05);
        }

        .form-side a {
            display: inline-block;
            margin-top: 20px;
            background-color: #ff4757;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .form-side a:hover {
            background-color: #ff75afcf;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .image-side {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-side">
            <img src="photos/aa.png" alt="מוצרי קוסמטיקה">
        </div>
        <div class="form-side">
            <h1>הרשמה</h1>
            <form method="post">
                <label for="id">תעודת זהות</label>
                <input type="text" id="id" name="id" placeholder="הקלד תעודת זהות" required>

                <label for="username">שם משתמש</label>
                <input type="text" id="username" name="username" placeholder="בחר שם משתמש" required>

                <label for="email">אימייל</label>
                <input type="email" id="email" name="email" placeholder="הכנס כתובת אימייל" required>

                <label for="password">סיסמה</label>
                <input type="password" id="password" name="password" placeholder="בחר סיסמה" required>

                <button type="submit" name="btn">צור חשבון</button>
            </form>
            <a href="index.php">חזרה לדף התחברות</a>
        </div>
    </div>
</body>
</html>


<?php
require_once 'ConnectToDB.php';
require_once 'User.php';

$flag = 0;

if (isset($_POST['btn'])) {
    if (
        isset($_POST["username"]) &&
        isset($_POST["password"]) &&
        isset($_POST["email"]) &&
        isset($_POST["id"]) 
    ) {
        $id = $_POST["id"];
        $us = $_POST["username"];
        $em = $_POST["email"];
        $p = $_POST["password"];
        

        $role = 0;
        if (isset($_POST["role"])) {
            $role = $_POST["role"];
        }

        $user = new User($id, $us, $em, $p);

        if ($user->isUserExist($conn)) {
            echo "<script>alert('❌ This user already exists in the database');</script>";
        } else {
            $sql = "INSERT INTO user (id, username, email, password, role)
                    VALUES ('$id', '$us', '$em', '$p', '$role')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('✅ Registration successful!'); window.location.href = 'index.php';</script>";
                exit();
            } else {
                echo "<script>alert('❌ שגיאה בהרשמה: " . $conn->error . "');</script>";
            }
        }
    }
}


?>
