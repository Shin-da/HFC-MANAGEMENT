CREATE TABLE IF NOT EXISTS activity_log (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    uid INT NOT NULL,
    activity VARCHAR(255) NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uid) REFERENCES user(uid)
);

CREATE INDEX idx_activity_uid ON activity_log(uid);
CREATE INDEX idx_activity_type ON activity_log(activity_type);
