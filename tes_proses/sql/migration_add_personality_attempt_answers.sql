-- Migration: add raw answer tables for PAPI and MSDT attempts
-- Run this on existing unified schema database

CREATE TABLE IF NOT EXISTS papi_attempt_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    question_no INT NOT NULL,
    jawaban_user CHAR(1) NOT NULL,
    mapped_dimension VARCHAR(2),
    waktu_jawab DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE,
    INDEX idx_attempt (attempt_id),
    INDEX idx_user_nip (user_nip),
    UNIQUE KEY unique_attempt_answer (attempt_id, question_no)
);

CREATE TABLE IF NOT EXISTS msdt_attempt_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    question_no INT NOT NULL,
    jawaban_user CHAR(1) NOT NULL,
    waktu_jawab DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE,
    INDEX idx_attempt (attempt_id),
    INDEX idx_user_nip (user_nip),
    UNIQUE KEY unique_attempt_answer (attempt_id, question_no)
);
