CREATE DATABASE IF NOT EXISTS nimbus CHARACTER SET utf8mb4;

USE nimbus;

CREATE TABLE chunks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    source      VARCHAR(255),
    title       VARCHAR(255),
    chunk_index INT,
    content     TEXT,
    embedding   JSON,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
