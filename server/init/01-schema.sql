CREATE TABLE IF NOT EXISTS scores (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    userid VARCHAR(64) NOT NULL,
    score INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_userid_score (userid, score),
    KEY idx_score (score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
