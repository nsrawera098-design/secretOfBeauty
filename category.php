<?php
// בדיקה אם location נשלח כראוי מהטופס, אחרת נחזיר את המשתמש ל-location.php
if (!isset($_POST['location'])) {
    header("Location: location.php");
    exit;
}
$location = $_POST['location'];
?>

<?php include('navbar.php'); ?> 

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>בחירת קטגוריה</title>
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

        .form-container {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
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
            background-color: #ff85a2;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #ffc0cb;
        }

        #map {
            width: 100%;
            max-width: 700px;
            height: 400px;
            margin: 40px auto;
            border-radius: 15px;
            border: 2px solid #ccc;
        }

        footer {
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>

    <header>
        <h1>בחר קטגוריה לשירות</h1>
    </header>

    <div class="form-container">
        <h2>בחר קטגוריה בעיר <?php echo htmlspecialchars($location); ?></h2>
        <form action="business.php" method="POST">
            <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
            <select name="category" required>
                <option value="">בחר קטגוריה</option>
                <option value="עיצוב שיער">עיצוב שיער</option>
                <option value="ציפורניים">ציפורניים</option>
                <option value="קוסמטיקה">קוסמטיקה</option>
            </select>
            <button type="submit">הצג עסקים</button>
        </form>
    </div>

    <!-- 🗺️ כותרת למפה -->
    <!-- 🗺️ כותרת מותאמת אישית למפה -->
<h2 style="margin-top: 40px; color: #444;">
    <?php echo "🧭 זהו מיקום הסניף שלנו בעיר " . htmlspecialchars($location) . " שבחרת:"; ?>
</h2>



    <!-- מפת Google -->
    <div id="map"></div>

    <footer>
        <p>&copy; Secret Beauty</p>
    </footer>

    <script>
        const locationName = "<?php echo htmlspecialchars($location); ?>";

        const cityCoords = {
            "חיפה": {lat: 32.7940, lng: 34.9896},
            "ירכא": {lat: 32.9531, lng: 35.2479},
            "כרמיאל": {lat: 32.9201, lng: 35.2900}
        };

        const branches = {
            "חיפה": [
                {name: "סניף מרכז חיפה", lat: 32.7945, lng: 34.9900},
                {name: "סניף נווה שאנן", lat: 32.7820, lng: 35.0120}
            ],
            "ירכא": [
                {name: "סניף קניון ירכא", lat: 32.9540, lng: 35.2485}
            ],
            "כרמיאל": [
                {name: "סניף לב העיר", lat: 32.9205, lng: 35.2920}
            ]
        };

        function initMap() {
            const city = cityCoords[locationName] || cityCoords["חיפה"];
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: city
            });

            if (branches[locationName]) {
                branches[locationName].forEach(place => {
                    new google.maps.Marker({
                        position: {lat: place.lat, lng: place.lng},
                        map,
                        title: place.name
                    });
                });
            }
        }
    </script>

    <!-- ✅ כאן נמצא ה-API KEY שלך למפות -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBuGMFaWxQro1jDXflfBEzZH8NmSNflwGM&callback=initMap" async defer></script>
</body>
</html>
