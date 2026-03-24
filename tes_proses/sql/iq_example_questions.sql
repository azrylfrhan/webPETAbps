CREATE TABLE iq_example_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    pertanyaan TEXT NOT NULL,
    jawaban_benar VARCHAR(100),

    FOREIGN KEY (section_id) REFERENCES iq_sections(id)
    ON DELETE CASCADE
);
