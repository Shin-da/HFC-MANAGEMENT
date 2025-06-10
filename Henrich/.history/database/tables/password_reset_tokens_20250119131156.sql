CREATE TABLE IF NOT EXISTS password_reset_tokens (
    token_id INT PRIMARY KEY AUTO_INCREMENT,
    uid INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expiry DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uid) REFERENCES user(uid)
);

CREATE UNIQUE INDEX idx_token ON password_reset_tokens(token);
CREATE INDEX idx_expiry ON password_reset_tokens(expiry);
CREATE INDEX idx_user_token ON password_reset_tokens(uid, token);
