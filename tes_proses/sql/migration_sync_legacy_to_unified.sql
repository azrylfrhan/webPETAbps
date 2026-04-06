-- Migration: Sync legacy test data into unified test history
-- Safe to run once after unified tables exist

START TRANSACTION;

-- 1) Build a temporary list of legacy attempts from old result tables
DROP TEMPORARY TABLE IF EXISTS tmp_legacy_attempts;
CREATE TEMPORARY TABLE tmp_legacy_attempts (
    nip VARCHAR(30) NOT NULL,
    test_type ENUM('iq', 'papi', 'msdt') NOT NULL,
    tanggal_tes DATETIME NOT NULL,
    source_key VARCHAR(60) NOT NULL,
    PRIMARY KEY (source_key),
    INDEX idx_nip_type_date (nip, test_type, tanggal_tes)
);

INSERT INTO tmp_legacy_attempts (nip, test_type, tanggal_tes, source_key)
SELECT r.user_id AS nip, 'iq' AS test_type, COALESCE(r.tanggal, NOW()) AS tanggal_tes, CONCAT('iq-', r.id) AS source_key
FROM iq_results r
WHERE r.user_id IS NOT NULL AND r.user_id <> '';

INSERT INTO tmp_legacy_attempts (nip, test_type, tanggal_tes, source_key)
SELECT p.nip, 'papi' AS test_type, COALESCE(p.tanggal_tes, NOW()) AS tanggal_tes, CONCAT('papi-', p.id) AS source_key
FROM hasil_papi p
WHERE p.nip IS NOT NULL AND p.nip <> '';

INSERT INTO tmp_legacy_attempts (nip, test_type, tanggal_tes, source_key)
SELECT m.nip, 'msdt' AS test_type, COALESCE(m.tanggal_tes, NOW()) AS tanggal_tes, CONCAT('msdt-', m.id) AS source_key
FROM hasil_msdt m
WHERE m.nip IS NOT NULL AND m.nip <> '';

-- Keep only users that exist
DELETE la
FROM tmp_legacy_attempts la
LEFT JOIN users u ON u.nip = la.nip
WHERE u.nip IS NULL;

-- 2) Insert missing attempts into unified table
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
    x.tanggal_tes AS tanggal_mulai,
    x.tanggal_tes AS tanggal_selesai,
    'Migrasi data lama dari hasil tes sebelumnya' AS alasan_tes,
    'finished' AS status
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

-- 3) Sync IQ legacy score into iq_attempt_results (section score unknown -> set 0)
INSERT INTO iq_attempt_results (
    attempt_id, user_nip, se, wa, an, ge, ra, zr, fa, wu, me, skor_total, tanggal_hitung
)
SELECT
    ta.id,
    r.user_id,
    0, 0, 0, 0, 0, 0, 0, 0, 0,
    COALESCE(r.skor, 0),
    COALESCE(r.tanggal, NOW())
FROM iq_results r
JOIN test_attempts ta
    ON ta.nip = r.user_id
   AND ta.test_type = 'iq'
   AND ta.tanggal_mulai = COALESCE(r.tanggal, ta.tanggal_mulai)
LEFT JOIN iq_attempt_results iar ON iar.attempt_id = ta.id
WHERE iar.id IS NULL;

-- 4) Sync PAPI legacy scores
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
LEFT JOIN papi_attempt_results par ON par.attempt_id = ta.id
WHERE par.id IS NULL;

-- 5) Sync MSDT legacy scores
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
LEFT JOIN msdt_attempt_results mar ON mar.attempt_id = ta.id
WHERE mar.id IS NULL;

-- 6) If old riwayat table exists, copy alasan to nearest attempt (when alasan_tes still empty)
SET @has_riwayat := (
    SELECT COUNT(*)
    FROM information_schema.tables
    WHERE table_schema = DATABASE() AND table_name = 'riwayat_alasan_tes'
);

-- Run only if table exists
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
PREPARE stmt_sync_reason FROM @sql_sync_reason;
EXECUTE stmt_sync_reason;
DEALLOCATE PREPARE stmt_sync_reason;

-- 7) Drop old manual history table to avoid duplicate history sections
SET @sql_drop_riwayat := IF(
    @has_riwayat > 0,
    'DROP TABLE riwayat_alasan_tes',
    'SELECT 1'
);
PREPARE stmt_drop_riwayat FROM @sql_drop_riwayat;
EXECUTE stmt_drop_riwayat;
DEALLOCATE PREPARE stmt_drop_riwayat;

COMMIT;
