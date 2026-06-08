CREATE TABLE IF NOT EXISTS users (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(100) NOT NULL DEFAULT '',
    email               VARCHAR(100) NOT NULL UNIQUE,
    password            VARCHAR(255) NOT NULL,
    role                ENUM('admin', 'editor', 'user') NOT NULL DEFAULT 'user',
    student_id          INT UNSIGNED NULL,
    email_verified      TINYINT(1) NOT NULL DEFAULT 0,
    verify_token        VARCHAR(64) NULL,
    verify_token_expires DATETIME NULL,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
