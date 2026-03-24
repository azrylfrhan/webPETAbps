CREATE TABLE iq_user_section_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    section_id INT NOT NULL,
    waktu_mulai DATETIME,
    waktu_selesai DATETIME,
    status ENUM('belum','sedang','selesai') DEFAULT 'belum',

    FOREIGN KEY (section_id) REFERENCES iq_sections(id)
    ON DELETE CASCADE
);