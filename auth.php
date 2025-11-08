<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode([
        "success" => true,
        "message" => "Auth API đang chạy. Hãy gửi POST với action=login, email, password."
    ]);
    exit;
}

// từ đây trở xuống giữ nguyên như mình viết lúc nãy
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Hãy dùng POST"
    ]);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu email hoặc mật khẩu"
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, email, password, role, active FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            "success" => false,
            "message" => "Email không tồn tại trong hệ thống"
        ]);
        exit;
    }

    if (isset($user['active']) && (int)$user['active'] === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Tài khoản đã bị khóa"
        ]);
        exit;
    }

    $hashInDb = $user['password'];
    $isValid = false;

    if (strpos($hashInDb, '$2y$') === 0) {
        $isValid = password_verify($password, $hashInDb);
    } else if (strlen($hashInDb) === 32 && ctype_xdigit($hashInDb)) {
        $isValid = (md5($password) === $hashInDb);
    } else {
        $isValid = ($password === $hashInDb);
    }

    if (!$isValid) {
        echo json_encode([
            "success" => false,
            "message" => "Mật khẩu không đúng"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Đăng nhập thành công",
        "user" => [
            "id" => (int)$user['id'],
            "name" => $user['name'],
            "email" => $user['email'],
            "role" => $user['role'],
        ]
    ]);
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Không tìm thấy hành động phù hợp"
]);
exit;
