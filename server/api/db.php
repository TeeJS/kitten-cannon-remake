<?php
// Shared bootstrap: CORS headers, JSON content type, PDO connection.
// All config comes from environment variables (set in docker-compose.yml / .env).

header('Access-Control-Allow-Origin: ' . (getenv('CORS_ALLOW_ORIGIN') ?: '*'));
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'kittencannon';
$user = getenv('DB_USER') ?: 'kittencannon';
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Game slug from GET/POST; defaults so the original kitten-cannon client keeps working.
function param_game() {
    $g = strtolower(trim($_POST['game'] ?? $_GET['game'] ?? 'kitten-cannon'));
    if (!preg_match('/^[a-z0-9][a-z0-9_-]{0,31}$/', $g)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid game id']);
        exit;
    }
    return $g;
}

// Player id (e.g. 3-letter initials). Uppercased; 1-16 chars A-Z 0-9 _ -.
function clean_userid($raw) {
    $u = strtoupper(trim($raw ?? ''));
    return preg_match('/^[A-Z0-9][A-Z0-9_-]{0,15}$/', $u) ? $u : null;
}
?>
