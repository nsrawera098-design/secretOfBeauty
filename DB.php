<?php

// ==============================
// Database Connection
// ==============================

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "user";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


// ==============================
// USERS TABLE
// ==============================

$tblUser = "
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role TINYINT(1) NOT NULL DEFAULT 0,
    birthdate DATE,
    profile_image VARCHAR(255),
    signup_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// CART TABLE
// ==============================

$tblCart = "
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    product_id INT NOT NULL,
    product_type VARCHAR(20) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,

    CONSTRAINT fk_cart_user
    FOREIGN KEY (username)
    REFERENCES user(username)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// HAIR PRODUCTS
// ==============================

$tblHairProducts = "
CREATE TABLE IF NOT EXISTS Products_hair (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// COSMETICS PRODUCTS
// ==============================

$tblCosmeticsProducts = "
CREATE TABLE IF NOT EXISTS Products_cosmetics (
    productco_id INT AUTO_INCREMENT PRIMARY KEY,
    productco_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// NAIL PRODUCTS
// ==============================

$tblNailsProducts = "
CREATE TABLE IF NOT EXISTS Products_nails (
    productN_id INT AUTO_INCREMENT PRIMARY KEY,
    productN_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// APPOINTMENTS TABLE
// ==============================

$tblAppointments = "
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    category VARCHAR(50),
    location VARCHAR(100),
    business_name VARCHAR(100),
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// ORDERS TABLE
// ==============================

$tblOrders = "
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255),
    product_type VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2),
    image VARCHAR(255),
    payment_method VARCHAR(50),
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_orders_user
    FOREIGN KEY (username)
    REFERENCES user(username)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// CONTACT MESSAGES
// ==============================

$tblMessages = "
CREATE TABLE IF NOT EXISTS tblmessages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(100),
    address VARCHAR(255),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// SKIN DIAGNOSIS
// ==============================

$tblSkinDiagnosis = "
CREATE TABLE IF NOT EXISTS skin_diagnosis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    age INT,
    skin_type VARCHAR(100) NOT NULL,
    skin_issues TEXT,
    skin_allergies TEXT,
    issues TEXT,
    goal TEXT,
    face_image VARCHAR(255),
    additional_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// HAIR DIAGNOSIS
// ==============================

$tblHairDiagnosis = "
CREATE TABLE IF NOT EXISTS hair_diagnosis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    age INT,
    hair_type VARCHAR(100) NOT NULL,
    scalp_condition VARCHAR(100),
    goal TEXT,
    current_products TEXT,
    wash_frequency VARCHAR(100),
    uses_heat_tools VARCHAR(10),
    hair_image VARCHAR(255),
    additional_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// NAILS DIAGNOSIS
// ==============================

$tblNailsDiagnosis = "
CREATE TABLE IF NOT EXISTS nails_diagnosis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    age INT,
    goal VARCHAR(255),
    frequent_polish VARCHAR(50),
    nail_type VARCHAR(100),
    additional_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// EMPLOYEE MANAGER
// ==============================

$tblEmployeeManager = "
CREATE TABLE IF NOT EXISTS employee_manager (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    work_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location VARCHAR(100),

    CONSTRAINT fk_employee_manager_user
    FOREIGN KEY (employee_id)
    REFERENCES user(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// WAITING LIST
// ==============================

$tblWaitingList = "
CREATE TABLE IF NOT EXISTS waiting_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    category VARCHAR(50),
    location VARCHAR(100),
    business_name VARCHAR(100),
    preferred_date DATE NOT NULL,
    preferred_time TIME NOT NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// BEAUTY SUGGESTIONS
// ==============================

$tblBeautySuggestions = "
CREATE TABLE IF NOT EXISTS beauty_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    minutes INT NOT NULL,
    suggestion TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// BUSINESSES TABLE
// ==============================

$tblBusinesses = "
CREATE TABLE IF NOT EXISTS businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// FEEDBACKS TABLE
// ==============================

$tblFeedbacks = "
CREATE TABLE IF NOT EXISTS feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(100),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";


// ==============================
// EXECUTE ALL TABLE CREATIONS
// ==============================

$tables = [
    "Users" => $tblUser,
    "Hair Products" => $tblHairProducts,
    "Cosmetics Products" => $tblCosmeticsProducts,
    "Nails Products" => $tblNailsProducts,
    "Appointments" => $tblAppointments,
    "Cart" => $tblCart,
    "Orders" => $tblOrders,
    "Messages" => $tblMessages,
    "Skin Diagnosis" => $tblSkinDiagnosis,
    "Hair Diagnosis" => $tblHairDiagnosis,
    "Nails Diagnosis" => $tblNailsDiagnosis,
    "Employee Manager" => $tblEmployeeManager,
    "Waiting List" => $tblWaitingList,
    "Beauty Suggestions" => $tblBeautySuggestions,
    "Businesses" => $tblBusinesses,
    "Feedbacks" => $tblFeedbacks
];

foreach ($tables as $tableName => $sql) {

    if ($conn->query($sql) === TRUE) {
        echo $tableName . " table created successfully.<br>";
    } else {
        echo "Error creating " . $tableName . ": " . $conn->error . "<br>";
    }
}

$conn->close();

?>
