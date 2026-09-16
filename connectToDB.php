<?php
// connectToDB.php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'user';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("❌ התחברות נכשלה: " . $conn->connect_error);
}

$conn->set_charset("utf8"); // ✔️ קידוד החיבור
?>