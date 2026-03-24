CREATE TABLE iq_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    label CHAR(1) NOT NULL,
    opsi_text VARCHAR(255),
    gambar_opsi VARCHAR(255) DEFAULT NULL,

    FOREIGN KEY (question_id) REFERENCES iq_questions(id)
    ON DELETE CASCADE
);