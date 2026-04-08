-- Railway-safe migration for unified test attempts - 2026-04-07
-- Purpose:
-- 1) Create unified attempt tables if they do not exist
-- 2) Add missing columns/indexes to existing legacy tables safely
-- 3) Avoid SQL features that may fail on Railway/MySQL variants

START TRANSACTION;

-- =========================================================
-- A. CORE TABLES
-- =========================================================

CREATE TABLE IF NOT EXISTS test_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(30) NOT NULL,
    test_type ENUM('iq', 'papi', 'msdt') NOT NULL,
    attempt_number INT DEFAULT 1,
    tanggal_mulai DATETIME DEFAULT CURRENT_TIMESTAMP,
    tanggal_selesai DATETIME NULL,
    alasan_tes TEXT NULL,
    status ENUM('running', 'finished', 'incomplete') DEFAULT 'running',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nip (nip),
    INDEX idx_test_type (test_type),
    INDEX idx_status (status),
    UNIQUE KEY unique_attempt (nip, test_type, attempt_number)
);

CREATE TABLE IF NOT EXISTS iq_attempt_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    question_id INT NOT NULL,
    jawaban_user VARCHAR(100),
    waktu_jawab DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_iq_attempt_answer (attempt_id, question_id),
    INDEX idx_iq_attempt (attempt_id),
    INDEX idx_iq_user_nip (user_nip),
    CONSTRAINT fk_iq_answers_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_iq_answers_question FOREIGN KEY (question_id) REFERENCES iq_questions(id) ON DELETE CASCADE
);

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
    UNIQUE KEY unique_iq_attempt_result (attempt_id),
    INDEX idx_iq_result_user_nip (user_nip),
    CONSTRAINT fk_iq_results_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS papi_attempt_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    question_no INT NOT NULL,
    jawaban_user CHAR(1) NOT NULL,
    mapped_dimension VARCHAR(2),
    waktu_jawab DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_papi_attempt_answer (attempt_id, question_no),
    INDEX idx_papi_attempt (attempt_id),
    INDEX idx_papi_user_nip (user_nip),
    CONSTRAINT fk_papi_answers_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS papi_attempt_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    G TINYINT DEFAULT 0,
    L TINYINT DEFAULT 0,
    I TINYINT DEFAULT 0,
    T TINYINT DEFAULT 0,
    V TINYINT DEFAULT 0,
    S TINYINT DEFAULT 0,
    R TINYINT DEFAULT 0,
    D TINYINT DEFAULT 0,
    C TINYINT DEFAULT 0,
    E TINYINT DEFAULT 0,
    N TINYINT DEFAULT 0,
    A TINYINT DEFAULT 0,
    P TINYINT DEFAULT 0,
    X TINYINT DEFAULT 0,
    B TINYINT DEFAULT 0,
    O TINYINT DEFAULT 0,
    K TINYINT DEFAULT 0,
    F TINYINT DEFAULT 0,
    W TINYINT DEFAULT 0,
    Z TINYINT DEFAULT 0,
    tanggal_hitung DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_papi_attempt_result (attempt_id),
    INDEX idx_papi_result_user_nip (user_nip),
    CONSTRAINT fk_papi_results_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS msdt_attempt_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    question_no INT NOT NULL,
    jawaban_user CHAR(1) NOT NULL,
    waktu_jawab DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_msdt_attempt_answer (attempt_id, question_no),
    INDEX idx_msdt_attempt (attempt_id),
    INDEX idx_msdt_user_nip (user_nip),
    CONSTRAINT fk_msdt_answers_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS msdt_attempt_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    Ds INT DEFAULT 0,
    Mi INT DEFAULT 0,
    Au INT DEFAULT 0,
    Co INT DEFAULT 0,
    Bu INT DEFAULT 0,
    Dv INT DEFAULT 0,
    Ba INT DEFAULT 0,
    E_dim INT DEFAULT 0,
    TO_score INT DEFAULT 0,
    RO_score INT DEFAULT 0,
    E_score INT DEFAULT 0,
    O_score INT DEFAULT 0,
    dominant_model VARCHAR(50),
    tanggal_hitung DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_msdt_attempt_result (attempt_id),
    INDEX idx_msdt_result_user_nip (user_nip),
    CONSTRAINT fk_msdt_results_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE
);

-- =========================================================
-- B. SAFE ALTERS FOR EXISTING TABLES
-- =========================================================

SET @db_name := DATABASE();

-- test_attempts.attempt_number
SET @exists := (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'attempt_number'
);
SET @sql := IF(@exists = 0,
    'ALTER TABLE test_attempts ADD COLUMN attempt_number INT DEFAULT 1 AFTER test_type',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- test_attempts.alasan_tes
SET @exists := (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'alasan_tes'
);
SET @sql := IF(@exists = 0,
    'ALTER TABLE test_attempts ADD COLUMN alasan_tes TEXT NULL AFTER tanggal_selesai',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- test_attempts.status
SET @exists := (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'status'
);
SET @sql := IF(@exists = 0,
    'ALTER TABLE test_attempts ADD COLUMN status ENUM(''running'',''finished'',''incomplete'') DEFAULT ''running'' AFTER alasan_tes',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- unique_attempt key
SET @idx_exists := (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND index_name = 'unique_attempt'
);
SET @sql := IF(@idx_exists = 0,
    'ALTER TABLE test_attempts ADD UNIQUE KEY unique_attempt (nip, test_type, attempt_number)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =========================================================
-- C. OPTIONAL LEGACY SNAPSHOT INTO UNIFIED TABLES
--      (intentionally omitted here to keep the migration SQL-safe on Railway)
-- =========================================================

-- =========================================================
-- D. OPTIONAL FK TO users(nip) IF COMPATIBLE
--    Railway can have users.nip definition that differs by length/collation.
--    To avoid hard failure, add FK only when column type/collation match.
-- =========================================================

SET @users_nip_coltype := (
    SELECT COLUMN_TYPE
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'users' AND column_name = 'nip'
    LIMIT 1
);

SET @users_nip_charset := (
    SELECT COALESCE(CHARACTER_SET_NAME, '')
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'users' AND column_name = 'nip'
    LIMIT 1
);

SET @users_nip_collation := (
    SELECT COALESCE(COLLATION_NAME, '')
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'users' AND column_name = 'nip'
    LIMIT 1
);

SET @attempt_nip_coltype := (
    SELECT COLUMN_TYPE
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'nip'
    LIMIT 1
);

SET @attempt_nip_charset := (
    SELECT COALESCE(CHARACTER_SET_NAME, '')
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'nip'
    LIMIT 1
);

SET @attempt_nip_collation := (
    SELECT COALESCE(COLLATION_NAME, '')
    FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'nip'
    LIMIT 1
);

SET @fk_exists := (
    SELECT COUNT(*)
    FROM information_schema.table_constraints
    WHERE table_schema = @db_name
      AND table_name = 'test_attempts'
      AND constraint_name = 'fk_test_attempts_user_nip'
      AND constraint_type = 'FOREIGN KEY'
);

SET @can_add_fk := (
    CASE
        WHEN @fk_exists > 0 THEN 0
        WHEN @users_nip_coltype IS NULL OR @attempt_nip_coltype IS NULL THEN 0
        WHEN @users_nip_coltype <> @attempt_nip_coltype THEN 0
        WHEN @users_nip_charset <> @attempt_nip_charset THEN 0
        WHEN @users_nip_collation <> @attempt_nip_collation THEN 0
        ELSE 1
    END
);

SET @sql := IF(@can_add_fk = 1,
    'ALTER TABLE test_attempts ADD CONSTRAINT fk_test_attempts_user_nip FOREIGN KEY (nip) REFERENCES users(nip) ON DELETE CASCADE',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;
