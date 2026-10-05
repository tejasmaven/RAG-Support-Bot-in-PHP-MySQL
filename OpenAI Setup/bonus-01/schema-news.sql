CREATE DATABASE IF NOT EXISTS nimbus CHARACTER SET utf8mb4;

USE nimbus;

CREATE TABLE news_chunks (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    article_url  VARCHAR(700),
    title        VARCHAR(500),
    source_name  VARCHAR(255),
    published_at DATETIME NULL,
    chunk_index  INT,
    content      TEXT,
    embedding    JSON,
    fetched_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uniq_article_chunk (article_url(255), chunk_index),
    FULLTEXT INDEX idx_ft_content (content),
    INDEX idx_published (published_at)
) CHARACTER SET utf8mb4;
