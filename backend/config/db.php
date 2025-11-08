<?php
// backend/config/db.php

$host = "localhost";
$db_name = "maintenance_app";  // đúng tên bạn tạo ở phpMyAdmin
$username = "root";
$password = ""; // XAMPP thường để rỗng

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    // bật chế độ báo lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection error: " . $exception->getMessage()
    ]);
    exit;
}
