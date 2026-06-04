CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    news_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- 外部キー制約（参照整合性）
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,

    -- 重複登録の防止
    UNIQUE KEY unique_user_news (user_id, news_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
