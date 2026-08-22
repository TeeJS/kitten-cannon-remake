<?php
include 'db.php';

$userId = $_GET['userId'] ?? $_GET['userid'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT MAX(score) as highScore FROM scores WHERE userid = :userId");
    $stmt->execute([':userId' => substr(trim($userId), 0, 64)]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'personalHighScore' => $result['highScore'] ?? 0
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
