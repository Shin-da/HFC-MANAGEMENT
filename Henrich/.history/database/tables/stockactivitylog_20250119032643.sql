CREATE TABLE IF NOT EXISTS stockactivitylog (
    logid INT AUTO_INCREMENT PRIMARY KEY,
    batchid VARCHAR(50),
    dateofarrival DATE,
    dateencoded DATETIME DEFAULT CURRENT_TIMESTAMP,
    encoder VARCHAR(50),
    description TEXT,
    totalNumberOfBoxes INT DEFAULT 0,