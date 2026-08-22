<?php
include 'db.php';

$game   = param_game();
// The kitten-cannon game sends 'userId'; the original PHP read 'userid'. Accept both.
$userid = clean_userid($_POST['userId'] ?? $_POST['userid'] ?? null);
$score  = isset($_POST['score']) ? intval($_POST['score']) : 0;

if (!$userid) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

$score = max(0, min($score, 100000));

try {
    $stmt = $pdo->prepare("INSERT INTO scores (game, userid, score, created_at) VALUES (:game, :userid, :score, NOW())");
    $stmt->execute([':game' => $game, ':userid' => $userid, ':score' => $score]);
    echo json_encode(['success' => true, 'message' => 'Score saved successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error saving score']);
}
?>
