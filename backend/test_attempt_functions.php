<?php
/**
 * Test Attempt Management Functions
 * Handles creation, retrieval, and management of IQ test attempts and history
 */

/**
 * Check whether a table exists in current database.
 */
function taf_table_exists($conn, $tableName) {
    static $cache = [];
    $key = strtolower((string)$tableName);
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    $safe = mysqli_real_escape_string($conn, $tableName);
    $res = mysqli_query($conn, "SHOW TABLES LIKE '$safe'");
    $exists = $res && mysqli_num_rows($res) > 0;
    $cache[$key] = $exists;
    return $exists;
}

/**
 * Check whether a column exists in a table.
 */
function taf_column_exists($conn, $tableName, $columnName) {
    if (!taf_table_exists($conn, $tableName)) {
        return false;
    }

    $t = mysqli_real_escape_string($conn, $tableName);
    $c = mysqli_real_escape_string($conn, $columnName);
    $res = mysqli_query($conn, "SHOW COLUMNS FROM `$t` LIKE '$c'");
    return $res && mysqli_num_rows($res) > 0;
}

/**
 * Legacy fallback reset when unified attempts table is unavailable.
 */
function taf_legacy_reset($conn, $test_type, $nip) {
    $ok = true;

    if ($test_type === 'iq') {
        if (taf_table_exists($conn, 'iq_test_sessions')) {
            $stmt = $conn->prepare("DELETE FROM iq_test_sessions WHERE nip = ?");
            $stmt->bind_param('s', $nip);
            $ok = $stmt->execute() && $ok;
            $stmt->close();
        }
        if (taf_table_exists($conn, 'iq_user_answers')) {
            $stmt = $conn->prepare("DELETE FROM iq_user_answers WHERE user_nip = ?");
            $stmt->bind_param('s', $nip);
            $ok = $stmt->execute() && $ok;
            $stmt->close();
        }
        if (taf_table_exists($conn, 'iq_user_section_progress')) {
            $stmt = $conn->prepare("DELETE FROM iq_user_section_progress WHERE user_nip = ?");
            $stmt->bind_param('s', $nip);
            $ok = $stmt->execute() && $ok;
            $stmt->close();
        }
        if (taf_table_exists($conn, 'iq_results')) {
            $stmt = $conn->prepare("DELETE FROM iq_results WHERE user_id = ?");
            $stmt->bind_param('s', $nip);
            $ok = $stmt->execute() && $ok;
            $stmt->close();
        }
    } elseif ($test_type === 'msdt') {
        if (taf_table_exists($conn, 'hasil_msdt')) {
            $stmt = $conn->prepare("DELETE FROM hasil_msdt WHERE nip = ?");
            $stmt->bind_param('s', $nip);
            $ok = $stmt->execute() && $ok;
            $stmt->close();
        }
    } elseif ($test_type === 'papi') {
        if (taf_table_exists($conn, 'hasil_papi')) {
            $stmt = $conn->prepare("DELETE FROM hasil_papi WHERE nip = ?");
            $stmt->bind_param('s', $nip);
            $ok = $stmt->execute() && $ok;
            $stmt->close();
        }
    }

    if (taf_table_exists($conn, 'users') && taf_column_exists($conn, 'users', 'status_tes')) {
        $stmt = $conn->prepare("UPDATE users SET status_tes = 'belum' WHERE nip = ?");
        $stmt->bind_param('s', $nip);
        $ok = $stmt->execute() && $ok;
        $stmt->close();
    }

    return $ok;
}

/**
 * Create a new test attempt for an employee (IQ specific, delegates to generic)
 * 
 * @param mysqli $conn Database connection
 * @param string $nip Employee NIP
 * @param string $alasan_tes Reason for test
 * @return int|false Attempt ID on success, false on failure
 */
function createTestAttempt($conn, $nip, $alasan_tes = '') {
    return createTestAttemptGeneric($conn, 'iq', $nip, $alasan_tes);
}

/**
 * Get current/latest active test attempt for a user (IQ specific)
 * 
 * @param mysqli $conn Database connection
 * @param string $nip Employee NIP
 * @return array|null Attempt data, or null if none exists
 */
function getCurrentAttempt($conn, $nip) {
    if (!taf_table_exists($conn, 'test_attempts')) {
        return null;
    }

    $stmt = $conn->prepare("
        SELECT id, nip, attempt_number, status, tanggal_mulai, alasan_tes
        FROM test_attempts
        WHERE nip = ? AND test_type = 'iq' AND status IN ('running', 'finished')
        ORDER BY tanggal_mulai DESC
        LIMIT 1
    ");
    $stmt->bind_param('s', $nip);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

/**
 * Get all test attempts for a user with their results (IQ specific, delegates to generic)
 * 
 * @param mysqli $conn Database connection
 * @param string $nip Employee NIP
 * @return array Array of attempts with results
 */
function getAttemptHistory($conn, $nip) {
    return getAttemptHistoryGeneric($conn, 'iq', $nip);
}

/**
 * Complete a test attempt and calculate results (IQ specific, delegates to generic)
 * 
 * @param mysqli $conn Database connection
 * @param int $attempt_id Attempt ID
 * @return bool Success status
 */
function completeAttempt($conn, $attempt_id) {
    return completeAttemptGeneric($conn, 'iq', $attempt_id);
}

/**
 * Calculate test results for an attempt based on answers
 * 
 * @param mysqli $conn Database connection
 * @param int $attempt_id Attempt ID
 * @param string $nip Employee NIP
 * @return bool Success status
 */
function calculateAttemptResults($conn, $attempt_id, $nip) {
    // Section definitions (question_id ranges)
    $sections = [
        'se' => [1, 20],
        'wa' => [21, 40],
        'an' => [41, 60],
        'ge' => [61, 76],
        'ra' => [77, 96],
        'zr' => [97, 116],
        'fa' => [117, 136],
        'wu' => [137, 156],
        'me' => [157, 176]
    ];

    $scores = [];
    $total = 0;

    // Calculate score for each section
    foreach ($sections as $section => $range) {
        $scores[$section] = 0;

        // Get questions for this section
        $stmt = $conn->prepare("
            SELECT id, jawaban_benar
            FROM iq_questions
            WHERE id BETWEEN ? AND ?
        ");
        $stmt->bind_param('ii', $range[0], $range[1]);
        $stmt->execute();
        $questions = [];
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $questions[$row['id']] = $row['jawaban_benar'];
        }
        $stmt->close();

        // Get user answers for this section
        foreach ($questions as $q_id => $answer_key) {
            $stmt = $conn->prepare("
                SELECT jawaban_user
                FROM iq_attempt_answers
                WHERE attempt_id = ? AND question_id = ?
            ");
            $stmt->bind_param('ii', $attempt_id, $q_id);
            $stmt->execute();
            $user_answer_result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user_answer_result) {
                $user_answer = $user_answer_result['jawaban_user'];

                // For GE section (fill-in), check if answer is in the correct answers set
                if ($section === 'ge') {
                    // GE answers are in format like "ANSWER1|ANSWER2|..."
                    $answers_array = array_map('trim', explode('|', $answer_key));
                    $user_answers_array = array_map('trim', explode(',', $user_answer));
                    
                    $matches = 0;
                    foreach ($user_answers_array as $ua) {
                        if (in_array(strtoupper($ua), array_map('strtoupper', $answers_array))) {
                            $matches++;
                        }
                    }
                    // For GE, each correct answer = 1 point, max 2 points per question based on answers
                    if ($matches > 0) {
                        $scores[$section] += min($matches, count($answers_array));
                    }
                } else {
                    // For other sections, simple comparison
                    if (strtoupper(trim($user_answer)) === strtoupper(trim($answer_key))) {
                        $scores[$section]++;
                    }
                }
            }
        }

        $total += $scores[$section];
    }

    // Store results
    $stmt = $conn->prepare("
        INSERT INTO iq_attempt_results 
        (attempt_id, user_nip, se, wa, an, ge, ra, zr, fa, wu, me, skor_total)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        se=?, wa=?, an=?, ge=?, ra=?, zr=?, fa=?, wu=?, me=?, skor_total=?
    ");
    $stmt->bind_param('isiiiiiiiiii' . 'iiiiiiiiiii', 
        $attempt_id, $nip,
        $scores['se'], $scores['wa'], $scores['an'], $scores['ge'], 
        $scores['ra'], $scores['zr'], $scores['fa'], $scores['wu'], $scores['me'], $total,
        // Duplicates
        $scores['se'], $scores['wa'], $scores['an'], $scores['ge'], 
        $scores['ra'], $scores['zr'], $scores['fa'], $scores['wu'], $scores['me'], $total
    );
    
    return $stmt->execute();
}

/**
 * Archive current attempt and create a new one when resetting test (IQ specific, delegates to generic)
 * 
 * @param mysqli $conn Database connection
 * @param string $nip Employee NIP
 * @param string $alasan_tes Reason for reset/re-attempt
 * @return int|false New attempt ID on success, false on failure
 */
function resetTestWithHistory($conn, $nip, $alasan_tes = 'Reset tes oleh admin') {
    return resetTestWithHistoryGeneric($conn, 'iq', $nip, $alasan_tes);
}

/**
 * Get answers for a specific attempt
 * 
 * @param mysqli $conn Database connection
 * @param int $attempt_id Attempt ID
 * @return array Array of answers keyed by question ID
 */
function getAttemptAnswers($conn, $attempt_id) {
    $stmt = $conn->prepare("
        SELECT question_id, jawaban_user
        FROM iq_attempt_answers
        WHERE attempt_id = ?
        ORDER BY question_id ASC
    ");
    $stmt->bind_param('i', $attempt_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $answers = [];
    while ($row = $result->fetch_assoc()) {
        $answers[$row['question_id']] = $row['jawaban_user'];
    }
    $stmt->close();
    return $answers;
}

// ==================== GENERIC FUNCTIONS FOR ALL TEST TYPES ====================

/**
 * Create a test attempt for any test type (IQ, PAPI, MSDT)
 * 
 * @param mysqli $conn Database connection
 * @param string $test_type Test type: 'iq', 'papi', 'msdt'
 * @param string $nip Employee NIP
 * @param string $alasan_tes Reason for test
 * @return int|false Attempt ID on success, false on failure
 */
function createTestAttemptGeneric($conn, $test_type, $nip, $alasan_tes = '') {
    $test_type = strtolower($test_type);
    if (!in_array($test_type, ['iq', 'papi', 'msdt'])) {
        return false;
    }

    if (!taf_table_exists($conn, 'test_attempts')) {
        return false;
    }

    // Get next attempt number for this user and test type
    $query = "SELECT COALESCE(MAX(attempt_number), 0) + 1 as next_attempt FROM test_attempts WHERE nip = ? AND test_type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $nip, $test_type);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $next_attempt = $result['next_attempt'] ?? 1;
    $stmt->close();

    // Create new attempt
    $query = "INSERT INTO test_attempts (nip, test_type, attempt_number, alasan_tes, status) VALUES (?, ?, ?, ?, 'running')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssis', $nip, $test_type, $next_attempt, $alasan_tes);
    
    if ($stmt->execute()) {
        $attempt_id = $stmt->insert_id;
        $stmt->close();
        return $attempt_id;
    }
    $stmt->close();
    return false;
}

/**
 * Get all test attempts for a user (any test type)
 * 
 * @param mysqli $conn Database connection
 * @param string $test_type Test type: 'iq', 'papi', 'msdt'
 * @param string $nip Employee NIP
 * @return array Array of attempts with results
 */
function getAttemptHistoryGeneric($conn, $test_type, $nip) {
    $test_type = strtolower($test_type);
    if (!in_array($test_type, ['iq', 'papi', 'msdt'])) {
        return [];
    }

    if (!taf_table_exists($conn, 'test_attempts')) {
        return [];
    }

    if ($test_type === 'iq') {
        $query = "
            SELECT 
                a.id as attempt_id,
                a.attempt_number,
                a.tanggal_mulai,
                a.tanggal_selesai,
                a.alasan_tes,
                a.status,
                r.se, r.wa, r.an, r.ge, r.ra, r.zr, r.fa, r.wu, r.me,
                r.skor_total
            FROM test_attempts a
            LEFT JOIN iq_attempt_results r ON a.id = r.attempt_id
            WHERE a.nip = ? AND a.test_type = 'iq'
            ORDER BY a.tanggal_mulai DESC
        ";
    } elseif ($test_type === 'papi') {
        $query = "
            SELECT 
                a.id as attempt_id,
                a.attempt_number,
                a.tanggal_mulai,
                a.tanggal_selesai,
                a.alasan_tes,
                a.status,
                r.G, r.L, r.I, r.T, r.V, r.S, r.R, r.D, r.C, r.E,
                r.N, r.A, r.P, r.X, r.B, r.O, r.K, r.F, r.W, r.Z
            FROM test_attempts a
            LEFT JOIN papi_attempt_results r ON a.id = r.attempt_id
            WHERE a.nip = ? AND a.test_type = 'papi'
            ORDER BY a.tanggal_mulai DESC
        ";
    } else { // msdt
        $query = "
            SELECT 
                a.id as attempt_id,
                a.attempt_number,
                a.tanggal_mulai,
                a.tanggal_selesai,
                a.alasan_tes,
                a.status,
                r.Ds, r.Mi, r.Au, r.Co, r.Bu, r.Dv, r.Ba, r.E_dim,
                r.TO_score, r.RO_score, r.E_score, r.O_score,
                r.dominant_model
            FROM test_attempts a
            LEFT JOIN msdt_attempt_results r ON a.id = r.attempt_id
            WHERE a.nip = ? AND a.test_type = 'msdt'
            ORDER BY a.tanggal_mulai DESC
        ";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $nip);
    $stmt->execute();
    $result = $stmt->get_result();
    $attempts = [];
    while ($row = $result->fetch_assoc()) {
        $attempts[] = $row;
    }
    $stmt->close();
    return $attempts;
}

/**
 * Complete a test attempt (generic for all types)
 * 
 * @param mysqli $conn Database connection
 * @param string $test_type Test type: 'iq', 'papi', 'msdt'
 * @param int $attempt_id Attempt ID
 * @return bool Success status
 */
function completeAttemptGeneric($conn, $test_type, $attempt_id) {
    $test_type = strtolower($test_type);
    if (!in_array($test_type, ['iq', 'papi', 'msdt'])) {
        return false;
    }

    if (!taf_table_exists($conn, 'test_attempts')) {
        return false;
    }

    // Get attempt details
    $stmt = $conn->prepare("SELECT nip FROM test_attempts WHERE id = ? AND test_type = ?");
    $stmt->bind_param('is', $attempt_id, $test_type);
    $stmt->execute();
    $attempt = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$attempt) {
        return false;
    }

    $nip = $attempt['nip'];

    // Update attempt status
    $stmt = $conn->prepare("UPDATE test_attempts SET status = 'finished', tanggal_selesai = NOW() WHERE id = ?");
    $stmt->bind_param('i', $attempt_id);
    $success = $stmt->execute();
    $stmt->close();

    if ($success && $test_type === 'iq') {
        // For IQ, update legacy iq_results table
        calculateAttemptResults($conn, $attempt_id, $nip);
        
        $stmt = $conn->prepare("DELETE FROM iq_results WHERE user_id = ?");
        $stmt->bind_param('s', $nip);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("
            SELECT se, wa, an, ge, ra, zr, fa, wu, me, skor_total
            FROM iq_attempt_results WHERE attempt_id = ?
        ");
        $stmt->bind_param('i', $attempt_id);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($results) {
            $total = $results['skor_total'];
            $stmt = $conn->prepare("INSERT INTO iq_results (user_id, skor, tanggal) VALUES (?, ?, NOW())");
            $stmt->bind_param('si', $nip, $total);
            $stmt->execute();
            $stmt->close();
        }
    }

    return $success;
}

/**
 * Reset test with history tracking (generic for all types)
 * 
 * @param mysqli $conn Database connection
 * @param string $test_type Test type: 'iq', 'papi', 'msdt'
 * @param string $nip Employee NIP
 * @param string $alasan_tes Reason for reset
 * @return int|false New attempt ID on success, false on failure
 */
function resetTestWithHistoryGeneric($conn, $test_type, $nip, $alasan_tes = 'Reset tes oleh admin') {
    $test_type = strtolower($test_type);
    if (!in_array($test_type, ['iq', 'papi', 'msdt'])) {
        return false;
    }

    // Railway or old environments may not have unified attempts yet.
    if (!taf_table_exists($conn, 'test_attempts')) {
        return taf_legacy_reset($conn, $test_type, $nip);
    }

    // Mark current running attempt as incomplete
    $stmt = $conn->prepare("UPDATE test_attempts SET status = 'incomplete' WHERE nip = ? AND test_type = ? AND status = 'running'");
    $stmt->bind_param('ss', $nip, $test_type);
    $stmt->execute();
    $stmt->close();

    // Clear session/temp data for IQ
    if ($test_type === 'iq') {
        $stmt = $conn->prepare("DELETE FROM iq_test_sessions WHERE nip = ?");
        $stmt->bind_param('s', $nip);
        $stmt->execute();
        $stmt->close();
    }

    // Create new attempt
    return createTestAttemptGeneric($conn, $test_type, $nip, $alasan_tes);
}
