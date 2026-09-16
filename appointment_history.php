<?php
session_start();
require_once 'connectToDB.php';
include 'navbar.php';

if (!isset($_SESSION['email'])) {
    echo "<p>🔒 עליך להתחבר כדי לצפות בהיסטוריית תורים.</p>";
    exit();
}

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT * FROM appointments WHERE email = ? ORDER BY appointment_date DESC, appointment_time DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>📅 היסטוריית תורים</title>
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Varela Round', sans-serif;
            background: url('photos/back3.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            color: #fff;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            min-height: 100vh;
            padding: 50px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 30px;
            color: #ffccff;
            text-shadow: 1px 1px 5px black;
        }

        .appointment {
            width: 90%;
            max-width: 700px;
            border-radius: 20px;
            padding: 30px;
            margin: 20px 0;
            text-align: right;
            direction: rtl;
            background: linear-gradient(145deg, rgba(255, 240, 255, 0.95), rgba(255, 220, 245, 0.95));
            color: #333;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease forwards;
            backdrop-filter: blur(6px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .appointment strong {
            color: #c2185b;
            font-weight: bold;
        }

        .appointment::before {
            content: "📌";
            font-size: 28px;
            position: absolute;
            top: -15px;
            left: -15px;
            background: #fff0f5;
            padding: 12px;
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
        }

        form.cancel-form {
            margin-top: 20px;
            text-align: left;
        }

        button.cancel-btn {
            background: linear-gradient(to right, #d63384, #ad1457);
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 15px;
            font-size: 14px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        button.cancel-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(214, 51, 132, 0.5);
        }

        .past-appointment {
            color: gray;
            font-style: italic;
            margin-top: 10px;
        }

        p {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            border-radius: 12px;
            color: #222;
            max-width: 500px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <h2>📅 היסטוריית תורים</h2>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='appointment'>";
                echo "<strong>🏢 תור ל:</strong> " . $row['business_name'] . "<br>";
                echo "<strong>🗂️ קטגוריה:</strong> " . $row['category'] . "<br>";
                echo "<strong>📍 מיקום:</strong> " . $row['location'] . "<br>";
                echo "<strong>📅 תאריך:</strong> " . $row['appointment_date'] . "<br>";
                echo "<strong>🕒 שעה:</strong> " . $row['appointment_time'] . "<br>";
                if (!empty($row['comments'])) {
                    echo "<strong>📝 הערות:</strong> " . $row['comments'] . "<br>";
                }

                $appointment_datetime = strtotime($row['appointment_date'] . ' ' . $row['appointment_time']);
                $now = time();

                if ($appointment_datetime > $now) {
                    echo "<form class='cancel-form' method='POST' action='cancel_appointment.php' onsubmit='return confirm(\"האם את בטוחה שברצונך לבטל את התור?\");'>";
                    echo "<input type='hidden' name='appointment_id' value='" . $row['id'] . "'>";
                    echo "<input type='hidden' name='business_name' value='" . $row['business_name'] . "'>";
                    echo "<input type='hidden' name='appointment_date' value='" . $row['appointment_date'] . "'>";
                    echo "<input type='hidden' name='appointment_time' value='" . $row['appointment_time'] . "'>";
                    echo "<button type='submit' class='cancel-btn'>❌ בטל תור</button>";
                    echo "</form>";
                } else {
                    echo "<p class='past-appointment'>✔️ תור שבוצע</p>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>לא נמצאו תורים קודמים.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
