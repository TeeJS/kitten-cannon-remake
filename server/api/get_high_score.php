<?php
include 'db.php';

try {
    $currentScore = isset($_GET['score']) ? intval($_GET['score']) : 0;

    $highScoreResult = $pdo->query("SELECT MAX(score) as highScore FROM scores")->fetch(PDO::FETCH_ASSOC);
    $highScore = $highScoreResult['highScore'] ?? '0';

    $percentile = 0;
    $totalScores = 0;

    if ($currentScore > 0) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as lowerScores FROM scores WHERE score < :score");
        $stmt->execute([':score' => $currentScore]);
        $lowerScores = $stmt->fetch(PDO::FETCH_ASSOC)['lowerScores'];

        $totalScores = $pdo->query("SELECT COUNT(*) as totalScores FROM scores")->fetch(PDO::FETCH_ASSOC)['totalScores'];

        if ($totalScores > 0) {
            $percentile = round(($lowerScores / $totalScores) * 100);
            if ($currentScore >= $highScore) {
                $percentile = 100;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'highScore' => $highScore,
        'percentile' => $percentile,
        'totalScores' => $totalScores
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error retrieving scores']);
}
?>
