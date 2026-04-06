-- Production migration (Railway) - 2026-04-06
-- Tujuan:
-- 1) Pastikan skema unified riwayat tes tersedia
-- 2) Pastikan tabel hasil/jawaban per attempt tersedia untuk IQ, PAPI, MSDT
-- 3) Sinkronkan data lama (iq_results, hasil_papi, hasil_msdt) ke skema unified
--
-- Sifat migration: additive + idempotent (aman dijalankan ulang)
-- Tidak menghapus tabel legacy.

START TRANSACTION;

-- =========================================================
-- A. CREATE CORE UNIFIED TABLES (IF NOT EXISTS)
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
    UNIQUE KEY unique_attempt (nip, test_type, attempt_number),
    CONSTRAINT fk_test_attempts_user_nip FOREIGN KEY (nip) REFERENCES users(nip) ON DELETE CASCADE
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
    CONSTRAINT fk_iq_answers_user FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE,
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
    CONSTRAINT fk_iq_results_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_iq_results_user FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS papi_attempt_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    user_nip VARCHAR(30) NOT NULL,
    G TINYINT DEFAULT 0, L TINYINT DEFAULT 0, I TINYINT DEFAULT 0, T TINYINT DEFAULT 0,
    V TINYINT DEFAULT 0, S TINYINT DEFAULT 0, R TINYINT DEFAULT 0, D TINYINT DEFAULT 0,
    C TINYINT DEFAULT 0, E TINYINT DEFAULT 0, N TINYINT DEFAULT 0, A TINYINT DEFAULT 0,
    P TINYINT DEFAULT 0, X TINYINT DEFAULT 0, B TINYINT DEFAULT 0, O TINYINT DEFAULT 0,
    K TINYINT DEFAULT 0, F TINYINT DEFAULT 0, W TINYINT DEFAULT 0, Z TINYINT DEFAULT 0,
    tanggal_hitung DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_papi_attempt_result (attempt_id),
    INDEX idx_papi_result_user_nip (user_nip),
    CONSTRAINT fk_papi_results_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_papi_results_user FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE
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
    CONSTRAINT fk_msdt_results_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_msdt_results_user FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE
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
    CONSTRAINT fk_papi_answers_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_papi_answers_user FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE
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
    CONSTRAINT fk_msdt_answers_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_msdt_answers_user FOREIGN KEY (user_nip) REFERENCES users(nip) ON DELETE CASCADE
);

-- =========================================================
-- B. ENSURE COLUMNS/INDEXES ON EXISTING test_attempts TABLE
-- (untuk kasus table sudah ada tapi versi lama)
-- =========================================================

SET @db_name := DATABASE();

-- attempt_number
SET @exists := (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'attempt_number'
);
SET @sql := IF(@exists = 0,
    'ALTER TABLE test_attempts ADD COLUMN attempt_number INT DEFAULT 1 AFTER test_type',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- alasan_tes
SET @exists := (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'alasan_tes'
);
SET @sql := IF(@exists = 0,
    'ALTER TABLE test_attempts ADD COLUMN alasan_tes TEXT NULL AFTER tanggal_selesai',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- status
SET @exists := (
    SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = @db_name AND table_name = 'test_attempts' AND column_name = 'status'
);
SET @sql := IF(@exists = 0,
    'ALTER TABLE test_attempts ADD COLUMN status ENUM(''running'',''finished'',''incomplete'') DEFAULT ''running'' AFTER alasan_tes',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- unique key (nip, test_type, attempt_number)
SET @idx_exists := (
    SELECT COUNT(*) FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'test_attempts'
      AND index_name = 'unique_attempt'
);
SET @sql := IF(@idx_exists = 0,
    'ALTER TABLE test_attempts ADD UNIQUE KEY unique_attempt (nip, test_type, attempt_number)',
    'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =========================================================
-- C. SYNC LEGACY RESULTS INTO UNIFIED ATTEMPTS
-- =========================================================

DROP TEMPORARY TABLE IF EXISTS tmp_legacy_attempts;
CREATE TEMPORARY TABLE tmp_legacy_attempts (
    nip VARCHAR(30) NOT NULL,
    test_type ENUM('iq', 'papi', 'msdt') NOT NULL,
    tanggal_tes DATETIME NOT NULL,
    source_key VARCHAR(80) NOT NULL,
    PRIMARY KEY (source_key),
    INDEX idx_nip_type_date (nip, test_type, tanggal_tes)
);

INSERT IGNORE INTO tmp_legacy_attempts (nip, test_type, tanggal_tes, source_key)
SELECT CAST(r.user_id AS CHAR(30)), 'iq', COALESCE(r.tanggal, NOW()), CONCAT('iq-', r.id)
FROM iq_results r
WHERE r.user_id IS NOT NULL AND CAST(r.user_id AS CHAR(30)) <> '';

INSERT IGNORE INTO tmp_legacy_attempts (nip, test_type, tanggal_tes, source_key)
SELECT p.nip, 'papi', COALESCE(p.tanggal_tes, NOW()), CONCAT('papi-', p.id)
FROM hasil_papi p
WHERE p.nip IS NOT NULL AND p.nip <> '';

INSERT IGNORE INTO tmp_legacy_attempts (nip, test_type, tanggal_tes, source_key)
SELECT m.nip, 'msdt', COALESCE(m.tanggal_tes, NOW()), CONCAT('msdt-', m.id)
FROM hasil_msdt m
WHERE m.nip IS NOT NULL AND m.nip <> '';

-- Sisakan hanya NIP user yang valid
DELETE la
FROM tmp_legacy_attempts la
LEFT JOIN users u ON u.nip = la.nip
WHERE u.nip IS NULL;

DROP TEMPORARY TABLE IF EXISTS tmp_to_insert_attempts;
CREATE TEMPORARY TABLE tmp_to_insert_attempts AS
SELECT la.nip, la.test_type, la.tanggal_tes, la.source_key
FROM tmp_legacy_attempts la
LEFT JOIN test_attempts ta
    ON ta.nip = la.nip
   AND ta.test_type = la.test_type
   AND ta.tanggal_mulai = la.tanggal_tes
WHERE ta.id IS NULL;

DROP TEMPORARY TABLE IF EXISTS tmp_existing_max;
CREATE TEMPORARY TABLE tmp_existing_max AS
SELECT nip, test_type, COALESCE(MAX(attempt_number), 0) AS max_attempt
FROM test_attempts
GROUP BY nip, test_type;

INSERT INTO test_attempts (
    nip,
    test_type,
    attempt_number,
    tanggal_mulai,
    tanggal_selesai,
    alasan_tes,
    status
)
SELECT
    x.nip,
    x.test_type,
    COALESCE(em.max_attempt, 0) + x.rn AS attempt_number,
    x.tanggal_tes,
    x.tanggal_tes,
    'Migrasi data lama dari hasil tes sebelumnya',
    'finished'
FROM (
    SELECT
        tti.nip,
        tti.test_type,
        tti.tanggal_tes,
        ROW_NUMBER() OVER (
            PARTITION BY tti.nip, tti.test_type
            ORDER BY tti.tanggal_tes ASC, tti.source_key ASC
        ) AS rn
    FROM tmp_to_insert_attempts tti
) x
LEFT JOIN tmp_existing_max em
    ON em.nip = x.nip
   AND em.test_type = x.test_type;

-- =========================================================
-- D. SYNC LEGACY SCORES INTO ATTEMPT RESULT TABLES
-- =========================================================

-- IQ: detail subtes lama tidak tersedia di iq_results, jadi diset 0 dan skor_total dari nilai legacy
INSERT INTO iq_attempt_results (
    attempt_id, user_nip, se, wa, an, ge, ra, zr, fa, wu, me, skor_total, tanggal_hitung
)
SELECT
    ta.id,
    CAST(r.user_id AS CHAR(30)),
    0, 0, 0, 0, 0, 0, 0, 0, 0,
    COALESCE(r.skor, 0),
    COALESCE(r.tanggal, NOW())
FROM iq_results r
JOIN test_attempts ta
    ON ta.nip = CAST(r.user_id AS CHAR(30))
   AND ta.test_type = 'iq'
   AND ta.tanggal_mulai = COALESCE(r.tanggal, ta.tanggal_mulai)
LEFT JOIN iq_attempt_results ir ON ir.attempt_id = ta.id
WHERE ir.id IS NULL;

INSERT INTO papi_attempt_results (
    attempt_id, user_nip, G, L, I, T, V, S, R, D, C, E, N, A, P, X, B, O, K, F, W, Z, tanggal_hitung
)
SELECT
    ta.id,
    p.nip,
    p.G, p.L, p.I, p.T, p.V, p.S, p.R, p.D, p.C, p.E,
    p.N, p.A, p.P, p.X, p.B, p.O, p.K, p.F, p.W, p.Z,
    COALESCE(p.tanggal_tes, NOW())
FROM hasil_papi p
JOIN test_attempts ta
    ON ta.nip = p.nip
   AND ta.test_type = 'papi'
   AND ta.tanggal_mulai = COALESCE(p.tanggal_tes, ta.tanggal_mulai)
LEFT JOIN papi_attempt_results pr ON pr.attempt_id = ta.id
WHERE pr.id IS NULL;

INSERT INTO msdt_attempt_results (
    attempt_id, user_nip, Ds, Mi, Au, Co, Bu, Dv, Ba, E_dim, TO_score, RO_score, E_score, O_score, dominant_model, tanggal_hitung
)
SELECT
    ta.id,
    m.nip,
    m.Ds, m.Mi, m.Au, m.Co, m.Bu, m.Dv, m.Ba, m.E_dim,
    m.TO_score, m.RO_score, m.E_score, m.O_score,
    m.dominant_model,
    COALESCE(m.tanggal_tes, NOW())
FROM hasil_msdt m
JOIN test_attempts ta
    ON ta.nip = m.nip
   AND ta.test_type = 'msdt'
   AND ta.tanggal_mulai = COALESCE(m.tanggal_tes, ta.tanggal_mulai)
LEFT JOIN msdt_attempt_results mr ON mr.attempt_id = ta.id
WHERE mr.id IS NULL;

-- =========================================================
-- E. OPTIONAL: SYNC ALASAN DARI TABEL LEGACY (JIKA ADA)
-- =========================================================

SET @has_riwayat := (
    SELECT COUNT(*)
    FROM information_schema.tables
    WHERE table_schema = DATABASE() AND table_name = 'riwayat_alasan_tes'
);

SET @sql_sync_reason := IF(
    @has_riwayat > 0,
    'UPDATE test_attempts ta
     JOIN (
         SELECT
             ta2.id AS attempt_id,
             rat.alasan_tes,
             ROW_NUMBER() OVER (
                 PARTITION BY ta2.id
                 ORDER BY ABS(TIMESTAMPDIFF(SECOND, rat.created_at, ta2.tanggal_mulai)) ASC, rat.id ASC
             ) AS rn
         FROM test_attempts ta2
         JOIN riwayat_alasan_tes rat ON rat.nip = ta2.nip
         WHERE (ta2.alasan_tes IS NULL OR ta2.alasan_tes = '''' OR ta2.alasan_tes = ''Migrasi data lama dari hasil tes sebelumnya'')
     ) x ON x.attempt_id = ta.id AND x.rn = 1
     SET ta.alasan_tes = x.alasan_tes',
    'SELECT 1'
);
PREPARE stmt FROM @sql_sync_reason;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;

-- END OF FILE
