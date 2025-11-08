<?php
header('Content-Type: application/json');

// nạp kết nối
require_once __DIR__ . '/../config/db.php';

echo json_encode([
    "success" => true,
    "message" => "DB connected successfully"
]);
