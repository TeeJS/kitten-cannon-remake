CREATE TABLE IF NOT EXISTS scores (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    game VARCHAR(32) NOT NULL DEFAULT 'kitten-cannon',
    userid VARCHAR(16) NOT NULL,
    score INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_game_score (game, score),
    KEY idx_game_user (game, userid, score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
