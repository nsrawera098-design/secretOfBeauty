<?php
session_start();
require_once 'ConnectToDB.php';
include('navbar.php'); 

if (!isset($_SESSION['username'])) {
    die("❌ שגיאה: עליך להיות מחובר כדי לצפות בעגלה.");
}

$username = $_SESSION['username'];

// מחיקת מוצר
if (isset($_GET['delete_product']) && isset($_GET['product_type'])) {
    $product_id = $_GET['delete_product'];
    $product_type = $_GET['product_type'];
    $conn->query("DELETE FROM cart WHERE name = '$username' AND product_id = '$product_id' AND product_type = '$product_type'");
}

// עדכון כמות
if (isset($_GET['update_product'], $_GET['product_id'], $_GET['quantity'], $_GET['product_type'])) {
    $product_id = $_GET['product_id'];
    $product_type = $_GET['product_type'];
    $new_quantity = max(1, intval($_GET['quantity']));
    $conn->query("UPDATE cart SET quantity = '$new_quantity' 
                  WHERE name = '$username' AND product_id = '$product_id' AND product_type = '$product_type'");
}

// פונקציה לשליפת פרטי מוצר
function getProductDetails($conn, $product_id, $product_type) {
    $map = [
        'hair' => ['table' => 'products_hair', 'name_field' => 'product_name', 'id_field' => 'product_id'],
        'cosmetics' => ['table' => 'products_cosmetics', 'name_field' => 'productco_name', 'id_field' => 'productco_id'],
        'nails' => ['table' => 'products_nails', 'name_field' => 'productN_name', 'id_field' => 'productN_id']
    ];
    if (!isset($map[$product_type])) return null;

    $t = $map[$product_type];
    $result = $conn->query("SELECT * FROM {$t['table']} WHERE {$t['id_field']} = '$product_id'");
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        return [
            'name' => $product[$t['name_field']],
            'price' => $product['price'],
            'image' => $product['image'],
            'table' => $t['table'],
            'id_field' => $t['id_field']
        ];
    }
    return null;
}

// אישור קנייה
if (isset($_GET['checkout'])) {
    $payment_method = $_GET['payment_method'] ?? '';

    $cart_sql = "SELECT * FROM cart WHERE name = '$username'";
    $cart_result = $conn->query($cart_sql);

    if ($cart_result && $cart_result->num_rows > 0) {
        while ($cart_row = $cart_result->fetch_assoc()) {
            $product_id = $cart_row['product_id'];
            $product_type = $cart_row['product_type'];
            $qty = $cart_row['quantity'];

            $product = getProductDetails($conn, $product_id, $product_type);
            if ($product) {
                $total_price = $product['price'] * $qty;
                $product_name = $product['name'];
                $product_img = $product['image'];
                $date = date('Y-m-d H:i:s');

                // עדכון מלאי
                $conn->query("UPDATE {$product['table']} 
                              SET quantity = quantity - $qty 
                              WHERE {$product['id_field']} = '$product_id' AND quantity >= $qty");

                // שמירה בטבלת orders
                $conn->query("INSERT INTO orders (username, product_id, product_type, quantity, order_date)
                              VALUES ('$username', '$product_id', '$product_type', '$qty', '$date')");
            }
        }

        // ריקון עגלה
        $conn->query("DELETE FROM cart WHERE name = '$username'");

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
        Swal.fire({
        icon: 'success',
        title: 'הקנייה בוצעה בהצלחה!',
        text: 'הפריטים נשמרו בהיסטוריית ההזמנות.',
        confirmButtonText: 'אישור',
        timer: 4000
       });
      </script>";

    } else {
        echo "<script>alert('🛒 אין פריטים בעגלה לאישור.');</script>";
    }
}



// שליפת מוצרים מהעגלה
$cart_result = $conn->query("SELECT * FROM cart WHERE name = '$username'");
?>


<!-- המשך הדף עם HTML, CSS וטופס התשלום כמו ששלחת קודם ממשיך כרגיל -->


<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <title>עגלת הקניות</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffeef8;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #d63384;
            margin-top: 30px;
        }
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ffd3e0;
        }
        th {
            background-color: #ff8ab3;
            color: white;
        }
        td {
            background-color: #fff6fa;
        }
        img {
            width: 60px;
            border-radius: 8px;
        }
        input[type="number"] {
            width: 50px;
            padding: 5px;
            text-align: center;
        }
        button {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .update-btn { background-color: #ffd54f; }
        .delete-btn { background-color: #ef5350; color: white; }
        .checkout-btn {
            margin-top: 20px;
            background-color: #ec407a;
            color: white;
            padding: 10px 16px;
            font-size: 1.1em;
        }
        #credit_card_form {
         background-color: #fff0f5;
         border: 2px solid #ffb6c1;
         padding: 20px;
         border-radius: 16px;
         width: 60%;
         margin: 0 auto 20px;
         box-shadow: 0 0 10px rgba(255, 192, 203, 0.3);
         font-size: 1em;
         direction: rtl;
         text-align: right;
}

       #credit_card_form label {
        display: block;
        margin-bottom: 16px;
        color: #d63384;
        font-weight: bold;
        font-size: 1.1em;
}

#credit_card_form input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ffc0cb;
    border-radius: 8px;
    font-size: 1em;
    background-color: #fff9fb;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

#credit_card_form input[type="text"]:focus {
    border-color: #ff69b4;
    outline: none;
}

/* רספונסיביות לנייד */
@media (max-width: 768px) {
    #credit_card_form {
        width: 90%;
        padding: 15px;
        font-size: 1em;
    }

    #credit_card_form label {
        font-size: 1em;
    }

    #credit_card_form input[type="text"] {
        font-size: 0.95em;
    }
}
.payment-methods {
  display: flex;
  gap: 20px;
  margin-top: 15px;
  direction: rtl;
  justify-content: center;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 15px;
  border: 2px solid #ccc;
  border-radius: 25px;
  cursor: pointer;
  transition: 0.3s ease;
  font-family: 'Arial', sans-serif;
  font-weight: bold;
  background-color: #f9f9f9;
}

.payment-option:hover {
  border-color: #7a7a7a;
  background-color: #f0f0f0;
}

.payment-option input[type="radio"] {
  appearance: none;
  -webkit-appearance: none;
  width: 18px;
  height: 18px;
  border: 2px solid #999;
  border-radius: 50%;
  outline: none;
  cursor: pointer;
  position: relative;
  background-color: white;
}

.payment-option input[type="radio"]:checked::before {
  content: "";
  width: 10px;
  height: 10px;
  background-color: #4CAF50;
  border-radius: 50%;
  position: absolute;
  top: 3px;
  left: 3px;
}


    </style>
</head>
<body>
    <h1>🛒 עגלת הקניות שלך</h1>
    <div class="container">
        <table>
            <tr>
                <th>תמונה</th>
                <th>שם</th>
                <th>כמות</th>
                <th>מחיר</th>
                <th>סה"כ</th>
                <th>פעולות</th>
            </tr>
            <?php
            $total_cart = 0;
            if ($cart_result && $cart_result->num_rows > 0) {
                while ($item = $cart_result->fetch_assoc()) {
                    $product_id = $item['product_id'];
                    $product_type = $item['product_type'];
                    $quantity = $item['quantity'];
                    $product = getProductDetails($conn, $product_id, $product_type);

                    if ($product) {
                        $total = $product['price'] * $quantity;
                        $total_cart += $total;
                        echo "<tr>
                                <td><img src='{$product['image']}'></td>
                                <td>{$product['name']}</td>
                                <td>
                                    <form method='get' style='display:inline-block;'>
                                        <input type='hidden' name='product_id' value='$product_id'>
                                        <input type='hidden' name='product_type' value='$product_type'>
                                        <input type='number' name='quantity' value='$quantity' min='1'>
                                        <button type='submit' name='update_product' class='update-btn'>עדכן</button>
                                    </form>
                                </td>
                                <td>₪{$product['price']}</td>
                                <td>₪{$total}</td>
                                <td>
                                    <form method='get' style='display:inline-block;'>
                                        <input type='hidden' name='product_type' value='$product_type'>
                                        <button type='submit' name='delete_product' value='$product_id' class='delete-btn'>מחק</button>
                                    </form>
                                </td>
                              </tr>";
                    } else {
                        echo "<tr><td colspan='6'>⚠️ מוצר לא נמצא ($product_id)</td></tr>";
                    }
                }

                echo "<tr>
                        <td colspan='4' style='text-align:right;'><strong>סה\"כ לתשלום:</strong></td>
                        <td colspan='2'><strong>₪" . number_format($total_cart, 2) . "</strong></td>
                      </tr>";
            } else {
                echo "<tr><td colspan='6'>🕳️ העגלה שלך ריקה.</td></tr>";
            }
            ?>
        </table>
             
        <form method="get" id="paymentForm" style="text-align: center;" onsubmit="return validateForm();">
    <h3>בחר אמצעי תשלום:</h3>
    <div class="payment-methods">
  <label class="payment-option">
    <input type="radio" name="payment_method" value="paypal">
    <span>PayPal</span>
  </label>
  
  <label class="payment-option">
  <input type="radio" name="payment_method" value="credit_card">
    <span>ויזה / כרטיס אשראי</span>
  </label>
</div>


    <!-- לא טופס, אלא div עם שדות אשראי -->
    <div id="credit_card_form" style="display:none;">
        <label>
            🧾 תעודת זהות:
            <input type="text" name="id_number" placeholder="הכנסי ת.ז">
        </label>
        <label>
            💳 מספר כרטיס:
            <input type="text" name="card_number" placeholder="הכנסי מספר כרטיס">
        </label>
        <label>
            📆 תוקף (MM/YY):
            <input type="text" name="exp_date" placeholder="למשל 08/26">
        </label>
        <label>
            🔐 קוד CVV:
            <input type="text" name="cvv" placeholder="3 ספרות בגב הכרטיס">
        </label>
    </div>

    <div id="paypal_button_container" style="display:none; margin-bottom: 15px;">
        <button type="submit" name="checkout" class="checkout-btn">🟡 המשך עם PayPal</button>
    </div>

    <button type="submit" name="checkout" class="checkout-btn">✅ אישור קנייה</button>

</form>

    </div>


    <script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
  const paymentSelected = document.querySelector('input[name="payment_method"]:checked');
  if (!paymentSelected) {
    e.preventDefault(); // עצור את שליחת הטופס
    alert('אנא בחר אמצעי תשלום לפני אישור הקנייה.');
  }
});
</script>

    <script>
    document.querySelectorAll('input[name="payment_method"]').forEach((elem) => {
        elem.addEventListener("change", function () {
            const paypalBtn = document.getElementById("paypal_button_container");
            const creditCardForm = document.getElementById("credit_card_form");
            

            if (this.value === "paypal") {
                paypalBtn.style.display = "block";
                creditCardForm.style.display = "none";
            } else if (this.value === "credit_card") {
                creditCardForm.style.display = "block";
                paypalBtn.style.display = "none";
            }
        });
    });

    function validateForm() {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        if (method === "credit_card") {
            const id = document.querySelector('[name="id_number"]').value.trim();
            const card = document.querySelector('[name="card_number"]').value.trim();
            const exp = document.querySelector('[name="exp_date"]').value.trim();
            const cvv = document.querySelector('[name="cvv"]').value.trim();

            // ת"ז – לפחות 5 ספרות
            if (id.length < 5) {
                alert("🧾 יש להזין ת.ז תקינה (לפחות 5 ספרות)");
                return false;
            }

            // מספר כרטיס – לפחות 12 ספרות
            if (!/^\d{12,19}$/.test(card)) {
                alert("💳 מספר כרטיס לא תקין (12-19 ספרות)");
                return false;
            }

            // תוקף – בפורמט MM/YY
            if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(exp)) {
                alert("📆 פורמט תוקף לא תקין (למשל 08/26)");
                return false;
            }

            // CVV – 3 או 4 ספרות
            if (!/^\d{3,4}$/.test(cvv)) {
                alert("🔐 קוד CVV לא תקין");
                return false;
            }
        }
        return true;
    }

    
</script>

</body>
</html>
<h2 style="text-align:center; color:#d63384;">🧾 היסטוריית רכישות</h2>
<?php
$user_email = $_SESSION['email'];
$sql = "SELECT * FROM orders WHERE email = '$user_email' ORDER BY order_date DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table style='width:90%; margin:auto; border:1px solid #ffd3e0;'>
            <tr>
                <th>תאריך</th>
                <th>מוצר</th>
                <th>קטגוריה</th>
                <th>כמות</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['order_date']}</td>
                <td>{$row['product_id']}</td>
                <td>{$row['product_type']}</td>
                <td>{$row['quantity']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>לא נמצאו הזמנות קודמות.</p>";
}
?>
