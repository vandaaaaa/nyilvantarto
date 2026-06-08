
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS name                VARCHAR(100) NOT NULL DEFAULT '' AFTER id,
    ADD COLUMN IF NOT EXISTS email_verified      TINYINT(1) NOT NULL DEFAULT 0 AFTER student_id,
    ADD COLUMN IF NOT EXISTS verify_token        VARCHAR(64) NULL AFTER email_verified,
    ADD COLUMN IF NOT EXISTS verify_token_expires DATETIME NULL AFTER verify_token;

ALTER TABLE users MODIFY role VARCHAR(20) NOT NULL DEFAULT 'user';

UPDATE users SET role = 'editor' WHERE role = 'tanar';
UPDATE users SET role = 'user'   WHERE role = 'diak';

ALTER TABLE users MODIFY role ENUM('admin', 'editor', 'user') NOT NULL DEFAULT 'user';

UPDATE users SET email_verified = 1 WHERE email_verified = 0;

UPDATE users SET name = email WHERE name = '' OR name IS NULL;
