<?php
include 'db.php';

$game   = param_game();
$userId = clean_userid($_GET['userId'] ?? $_GET['userid'] ?? null);

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT MAX(score) as highScore FROM scores WHERE game = :game AND userid = :userId");
    $stmt->execute([':game' => $game, ':userId' => $userId]);
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
