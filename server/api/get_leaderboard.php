<?php
include 'db.php';

$game  = param_game();
$limit = isset($_GET['limit']) ? max(1, min(intval($_GET['limit']), 100)) : 10;
$runs  = isset($_GET['runs']) && $_GET['runs'] === '1';

try {
    if ($runs) {
        // Arcade style: top individual runs — the same initials can hold
        // several ranks, so the board fills up even with one player.
        $stmt = $pdo->prepare(
            "SELECT userid, score FROM scores WHERE game = :game
             ORDER BY score DESC, userid ASC LIMIT $limit"
        );
    } else {
        // Default: best score per player, ranked.
        $stmt = $pdo->prepare(
            "SELECT userid, MAX(score) as score FROM scores WHERE game = :game
             GROUP BY userid ORDER BY score DESC, userid ASC LIMIT $limit"
        );
    }
    $stmt->execute([':game' => $game]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'game' => $game,
        'leaderboard' => array_map(function ($r, $i) {
            return ['rank' => $i + 1, 'userid' => $r['userid'], 'score' => intval($r['score'])];
        }, $rows, array_keys($rows))
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error retrieving leaderboard']);
}
?>
