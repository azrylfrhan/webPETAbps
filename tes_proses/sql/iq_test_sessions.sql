CREATE TABLE iq_test_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(30),
    section INT,
    question INT,
    start_time DATETIME,
    status ENUM('running','finished') DEFAULT 'running',
    FOREIGN KEY (nip) REFERENCES users(nip)
);