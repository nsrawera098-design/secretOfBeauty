<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user";

$conn = new mysqli($servername, $username, $password,$dbname);
if($conn->connect_error){
    die("Connection failed: " .$conn->connect_error);

}
$tblUser = "create table user (
    id INT(6) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL,
    email VARCHAR(30),
    role TINYINT(1) NOT NULL DEFAULT 0,
    birthdate DATE,
    profile_image VARCHAR(255)
)";

$tblCart="create table cart (
    cart.id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    product_id INT NOT NULL,
    product_type VARCHAR(20) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (name) REFERENCES tblUser(name)
)";
    $tblHairProducts = "create table Products_hair (
        product_id INT AUTO_INCREMENT PRIMARY KEY,
        product_name VARCHAR(50) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL
    )";

    $tblcosmeticsProducts = "create table Products_cosmetics (
        productco_id INT AUTO_INCREMENT PRIMARY KEY,
        productco_name VARCHAR(50) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL
    )"; 

    $tblnailsrProducts = "create table Products_nails (
        productN_id INT AUTO_INCREMENT PRIMARY KEY,
        productN_name VARCHAR(50) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL
    )";

    $tblappointments = "create table appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    category VARCHAR(50), -- לדוגמה: שיער, פנים, ציפורניים
    location VARCHAR(100), -- לדוגמה: תל אביב, חיפה
    business_name VARCHAR(100), -- שם העסק שנבחר
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$tblorder = "CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255),
    product_type VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2),
    image VARCHAR(255),
    payment_method VARCHAR(50),
    order_date DATETIME NOT NULL
)";

$tblmessage="CREATE TABLE tblmessages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(100),
    address VARCHAR(255),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$tblskin_diagnosis = "CREATE TABLE skin_diagnosis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    skin_type VARCHAR(100) NOT NULL,
    skin_issues TEXT,
    skin_allergies TEXT,
    issues TEXT,
    goal TEXT,
    face_image VARCHAR(255),
    additional_info TEXT,
    created_at DATETIME NOT NULL
)";

$tblhair_diagnosis = "CREATE TABLE hair_diagnosis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    hair_type VARCHAR(100) NOT NULL,
    scalp_condition VARCHAR(100),
    goal TEXT,
    current_products TEXT,
    wash_frequency VARCHAR(100),
    uses_heat_tools VARCHAR(10),
    hair_image VARCHAR(255),
    additional_info TEXT,
    created_at DATETIME NOT NULL
)";


$tblemployee_manager = "CREATE TABLE employee_manager (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    work_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location VARCHAR(100),
    FOREIGN KEY (employee_id) REFERENCES user(id)
)"; // ✅ סגירת המחרוזת כאן


  $tblwaitinglist= "CREATE TABLE waiting_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    category VARCHAR(50),
    location VARCHAR(100),
    business_name VARCHAR(100), -- שם העסק כפי שמופיע ב-appointments
    preferred_date DATE NOT NULL,
    preferred_time TIME NOT NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";


?>
