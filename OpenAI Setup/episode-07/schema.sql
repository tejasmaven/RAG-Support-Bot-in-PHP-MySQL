CREATE DATABASE IF NOT EXISTS nimbus CHARACTER SET utf8mb4;

USE nimbus;

CREATE TABLE chunks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    source      VARCHAR(255),
    title       VARCHAR(255),
    doc_type    VARCHAR(50) NULL,
    chunk_index INT,
    content     TEXT,
    embedding   JSON,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_source (source),
    INDEX idx_doc_type (doc_type),
    FULLTEXT INDEX idx_ft_content (content)
);
