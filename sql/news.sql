CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country INT NOT NULL,
    category INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    url VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(500),
    description TEXT,
    published_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_url (url),

    INDEX idx_country_category (country, category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
