CREATE TABLE iq_example_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    example_question_id INT NOT NULL,
    label CHAR(1) NOT NULL,
    opsi_text VARCHAR(255),

    FOREIGN KEY (example_question_id) REFERENCES iq_example_questions(id)
    ON DELETE CASCADE
);