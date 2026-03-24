CREATE TABLE iq_fill_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    jawaban VARCHAR(255),
    nilai INT,
    FOREIGN KEY (question_id) REFERENCES iq_questions(id) 
    ON DELETE CASCADE
);