CREATE TABLE iq_user_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_nip VARCHAR(30) NOT NULL,
    question_id INT NOT NULL,
    jawaban_user VARCHAR(100),
    waktu_jawab DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_question (user_nip, question_id),
    
    FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES iq_questions(id) ON DELETE CASCADE
);