<?php
include 'db.php';

$game = param_game();

try {
    $currentScore = isset($_GET['score']) ? intval($_GET['score']) : 0;

    $stmt = $pdo->prepare("SELECT MAX(score) as highScore FROM scores WHERE game = :game");
    $stmt->execute([':game' => $game]);
    $highScore = $stmt->fetch(PDO::FETCH_ASSOC)['highScore'] ?? '0';

    $percentile = 0;
    $totalScores = 0;

    if ($currentScore > 0) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as lowerScores FROM scores WHERE game = :game AND score < :score");
        $stmt->execute([':game' => $game, ':score' => $currentScore]);
        $lowerScores = $stmt->fetch(PDO::FETCH_ASSOC)['lowerScores'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as totalScores FROM scores WHERE game = :game");
        $stmt->execute([':game' => $game]);
        $totalScores = $stmt->fetch(PDO::FETCH_ASSOC)['totalScores'];

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
