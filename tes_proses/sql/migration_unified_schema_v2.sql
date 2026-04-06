-- Migration v2: Upgrade from separate type-specific attempts to unified schema
-- This safely replaces old schema with new unified approach

-- ==================== DROP OLD TABLES (if they exist) ====================
-- Drop child tables first (those with foreign key to parent), then parent tables
-- This avoids "foreign key constraint fails" errors

-- Drop old result tables first (they reference the old attempt tables)
DROP TABLE IF EXISTS iq_attempt_results;
DROP TABLE IF EXISTS iq_attempt_answers;
DROP TABLE IF EXISTS papi_attempt_answers;
DROP TABLE IF EXISTS papi_attempt_results;
DROP TABLE IF EXISTS msdt_attempt_answers;
DROP TABLE IF EXISTS msdt_attempt_results;

-- Now drop old parent attempt tables
DROP TABLE IF EXISTS iq_test_attempts;
DROP TABLE IF EXISTS papi_test_attempts;
DROP TABLE IF EXISTS msdt_test_attempts;

-- ==================== CREATE UNIFIED TEST ATTEMPTS ====================

-- Single unified table for all test attempts (IQ, PAPI, MSDT)
CREATE TABLE IF NOT EXISTS test_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(30) NOT NULL,
    test_type ENUM('iq', 'papi', 'msdt') NOT NULL,
    attempt_number INT DEFAULT 1,
    tanggal_mulai DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_selesai DATETIME,
    alasan_tes TEXT,
    status ENUM('running','finished','incomplete') DEFAULT 'running',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nip) REFERENCES users(nip) ON DELETE CASCADE,
    INDEX idx_nip (nip),
    INDEX idx_test_type (test_type),
    INDEX idx_status (status),
    UNIQUE KEY unique_attempt (nip, test_type, attempt_number)
);

-- ==================== IQ TEST SPECIFIC ====================

-- IQ answers per attempt (Create if not exists)
CREATE TABLE IF NOT EXISTS iq_attempt_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    question_id INT NOT NULL,
    jawaban_user VARCHAR(100),
    waktu_jawab DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES iq_questions(id) ON DELETE CASCADE,
    INDEX idx_attempt (attempt_id),
    INDEX idx_user_nip (user_nip),
    UNIQUE KEY unique_attempt_answer (attempt_id, question_id)
);

-- IQ results per attempt (Create if not exists)
CREATE TABLE IF NOT EXISTS iq_attempt_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    se INT DEFAULT 0,
    wa INT DEFAULT 0,
    an INT DEFAULT 0,
    ge INT DEFAULT 0,
    ra INT DEFAULT 0,
    zr INT DEFAULT 0,
    fa INT DEFAULT 0,
    wu INT DEFAULT 0,
    me INT DEFAULT 0,
    skor_total INT DEFAULT 0,
    tanggal_hitung DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE,
    UNIQUE KEY unique_attempt_results (attempt_id),
    INDEX idx_user_nip (user_nip)
);

-- ==================== PAPI TEST SPECIFIC ====================

-- PAPI answers per attempt (Create if not exists)
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

-- PAPI results per attempt (Create if not exists)
CREATE TABLE IF NOT EXISTS papi_attempt_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    -- Roles (10 dimensi)
    G TINYINT DEFAULT 0, L TINYINT DEFAULT 0, I TINYINT DEFAULT 0, 
    T TINYINT DEFAULT 0, V TINYINT DEFAULT 0, S TINYINT DEFAULT 0, 
    R TINYINT DEFAULT 0, D TINYINT DEFAULT 0, C TINYINT DEFAULT 0, E TINYINT DEFAULT 0,
    -- Needs (10 dimensi)
    N TINYINT DEFAULT 0, A TINYINT DEFAULT 0, P TINYINT DEFAULT 0, 
    X TINYINT DEFAULT 0, B TINYINT DEFAULT 0, O TINYINT DEFAULT 0, 
    K TINYINT DEFAULT 0, F TINYINT DEFAULT 0, W TINYINT DEFAULT 0, Z TINYINT DEFAULT 0,
    tanggal_hitung DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE,
    UNIQUE KEY unique_attempt_results (attempt_id),
    INDEX idx_user_nip (user_nip)
);

-- ==================== MSDT TEST SPECIFIC ====================

-- MSDT answers per attempt (Create if not exists)
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

-- MSDT results per attempt (Create if not exists)
CREATE TABLE IF NOT EXISTS msdt_attempt_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    -- 8 Dimensi Gaya
    Ds INT,
    Mi INT,
    Au INT,
    Co INT,
    Bu INT,
    Dv INT,
    Ba INT,
    E_dim INT,
    -- 4 Composites
    TO_score INT,
    RO_score INT,
    E_score INT,
    O_score INT,
    -- Dominant Model
    dominant_model VARCHAR(50),
    tanggal_hitung DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE,
    UNIQUE KEY unique_attempt_results (attempt_id),
    INDEX idx_user_nip (user_nip)
);

-- ==================== MIGRATION COMPLETE ====================
-- New unified schema ready for all test types!
-- Old type-specific attempts tables have been removed.
