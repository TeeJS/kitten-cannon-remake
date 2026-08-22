<?php
include 'db.php';

// The game sends 'userId'; the original PHP read 'userid'. Accept both.
$userid = $_POST['userId'] ?? $_POST['userid'] ?? null;
$score  = isset($_POST['score']) ? intval($_POST['score']) : 0;

if (!$userid) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

$userid = substr(trim($userid), 0, 64);
$score  = max(0, min($score, 100000));

try {
    $stmt = $pdo->prepare("INSERT INTO scores (userid, score, created_at) VALUES (:userid, :score, NOW())");
    $stmt->execute([':userid' => $userid, ':score' => $score]);
    echo json_encode(['success' => true, 'message' => 'Score saved successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error saving score']);
}
?>
