<?php 
include '../backend/auth_check.php'; 
require_once '../backend/config.php';
require_once '../backend/test_attempt_functions.php';

$akses_filter = $_GET['akses'] ?? 'all';
if (!in_array($akses_filter, ['all', 'active', 'inactive'], true)) {
    $akses_filter = 'all';
}

$search_query = trim($_GET['q'] ?? '');
$search_query = preg_replace('/\s+/', ' ', $search_query);

function statusPegawaiRedirectSuffix(string $akses_filter): string
{
    return $akses_filter !== 'all' ? '&akses=' . urlencode($akses_filter) : '';
}

$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// --- IMPORT CSV / XLSX ---
if (isset($_POST['import_csv'])) {
    $file     = $_FILES['csv_file']['tmp_name'];
    $filename = $_FILES['csv_file']['name'];
    $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($_FILES['csv_file']['size'] > 0) {
        $rows_data = [];

        if ($ext === 'csv') {
            // Baca CSV
            $handle = fopen($file, "r");
            fgetcsv($handle, 1000, ","); // skip header
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rows_data[] = $data;
            }
            fclose($handle);

        } elseif ($ext === 'xlsx' || $ext === 'xls') {
            $zip = new ZipArchive();
            if ($zip->open($file) === TRUE) {
                $shared_xml = $zip->getFromName('xl/sharedStrings.xml');
                $sheet_xml  = $zip->getFromName('xl/worksheets/sheet1.xml');
                $zip->close();

                // Parse shared strings (tipe t="s" = index ke sini)
                $strings = [];
                if ($shared_xml) {
                    preg_match_all('/<si>(.*?)<\/si>/s', $shared_xml, $si_matches);
                    foreach ($si_matches[1] as $si) {
                        preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $si, $t_matches);
                        $strings[] = html_entity_decode(implode('', $t_matches[1]));
                    }
                }

                if ($sheet_xml) {
                    // Konversi kolom letter ke index (A=0, B=1, dst)
                    $col2idx = function($col) {
                        $idx = 0;
                        foreach (str_split(strtoupper($col)) as $ch) {
                            $idx = $idx * 26 + (ord($ch) - 64);
                        }
                        return $idx - 1;
                    };

                    preg_match_all('/<row\b[^>]*>(.*?)<\/row>/s', $sheet_xml, $row_matches);
                    $first_row = true;
                    foreach ($row_matches[1] as $row_xml) {
                        if ($first_row) { $first_row = false; continue; } // skip header

                        // Parse tiap cell: <c r="A1" t="s"><v>0</v></c>
                        preg_match_all('/<c\b([^>]*)>(.*?)<\/c>/s', $row_xml, $cell_matches, PREG_SET_ORDER);
                        $row_arr = [];
                        foreach ($cell_matches as $cm) {
                            $attrs    = $cm[1];
                            $inner    = $cm[2];

                            // Ambil referensi kolom (misal "A1" → "A")
                            preg_match('/r="([A-Z]+)\d+"/i', $attrs, $ref_m);
                            if (!$ref_m) continue;
                            $col_idx = $col2idx($ref_m[1]);

                            // Ambil nilai
                            preg_match('/<v>(.*?)<\/v>/s', $inner, $v_m);
                            if (!$v_m) continue;
                            $raw = $v_m[1];

                            // Cek tipe: t="s" = shared string index
                            if (preg_match('/\bt="s"/', $attrs)) {
                                $val = $strings[(int)$raw] ?? '';
                            } else {
                                // Numerik atau tanggal — gunakan langsung
                                $val = $raw;
                            }

                            $row_arr[$col_idx] = $val;
                        }

                        if (!empty($row_arr)) {
                            ksort($row_arr);
                            $rows_data[] = array_values($row_arr);
                        }
                    }
                }
            }
        }

        $count = 0;
        foreach ($rows_data as $data) {
            $nip     = mysqli_real_escape_string($conn, trim($data[1] ?? ''));
            $nama    = mysqli_real_escape_string($conn, trim($data[2] ?? ''));
            $jabatan = mysqli_real_escape_string($conn, trim($data[3] ?? ''));
            $satker  = mysqli_real_escape_string($conn, trim($data[4] ?? ''));
            $pangkat = mysqli_real_escape_string($conn, trim($data[5] ?? ''));
            if (!$nip) continue;
            $hashed = password_hash(substr($nip, 0, 6), PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (nip,nama,jabatan,satuan_kerja,pangkat_golongan,password,is_active,role)
                    VALUES ('$nip','$nama','$jabatan','$satker','$pangkat','$hashed',0,'peserta')
                    ON DUPLICATE KEY UPDATE nama='$nama',jabatan='$jabatan',satuan_kerja='$satker',pangkat_golongan='$pangkat'";
            if (mysqli_query($conn, $sql)) $count++;
        }

        $suffix = statusPegawaiRedirectSuffix($_POST['akses_filter'] ?? 'all');
        header("Location: status_pegawai.php?import=success&count=$count$suffix");
        exit;
    }
}

// --- BULK ACTION ---
if (isset($_POST['bulk_action']) && !empty($_POST['selected_nip'])) {
    $nips     = $_POST['selected_nip'];
    $action   = $_POST['action_type'];
    $nip_list = implode("','", array_map(fn($n) => mysqli_real_escape_string($conn, $n), $nips));
    switch ($action) {
        case 'aktifkan':    mysqli_query($conn, "UPDATE users SET is_active=1, status_tes='belum' WHERE nip IN ('$nip_list')"); break;
        case 'nonaktifkan': mysqli_query($conn, "UPDATE users SET is_active=0 WHERE nip IN ('$nip_list')"); break;
        case 'reset_iq':
            // Use new history-aware reset function
            $alasan = '';
            foreach ($nips as $nip) {
                $nip = mysqli_real_escape_string($conn, trim($nip));
                resetTestWithHistoryGeneric($conn, 'iq', $nip, $alasan);
            }
            break;
        case 'reset_papi':
            // Use new history-aware reset function for PAPI
            $alasan = '';
            foreach ($nips as $nip) {
                $nip = mysqli_real_escape_string($conn, trim($nip));
                resetTestWithHistoryGeneric($conn, 'papi', $nip, $alasan);
            }
            break;
        case 'reset_msdt':
            // Use new history-aware reset function for MSDT
            $alasan = '';
            foreach ($nips as $nip) {
                $nip = mysqli_real_escape_string($conn, trim($nip));
                resetTestWithHistoryGeneric($conn, 'msdt', $nip, $alasan);
            }
            break;
    }
    $suffix = statusPegawaiRedirectSuffix($_POST['akses_filter'] ?? 'all');
    header("Location: status_pegawai.php?bulk=success$suffix");
    exit;
}

// --- QUERY ---
$where_akses = '';
if ($akses_filter === 'active') {
    $where_akses = ' AND u.is_active = 1';
} elseif ($akses_filter === 'inactive') {
    $where_akses = ' AND u.is_active = 0';
}

$where_search = '';
if ($search_query !== '') {
    $q_esc = mysqli_real_escape_string($conn, $search_query);
    $where_search = " AND (\n"
        . "u.nip LIKE '%$q_esc%' OR "
        . "u.nama LIKE '%$q_esc%' OR "
        . "COALESCE(u.jabatan, '') LIKE '%$q_esc%' OR "
        . "COALESCE(u.satuan_kerja, '') LIKE '%$q_esc%' OR "
        . "COALESCE(u.pangkat_golongan, '') LIKE '%$q_esc%'\n"
        . ")";
}

// Hitung total data untuk pagination
$countResult = mysqli_query($conn, "
    SELECT COUNT(*) AS total_data
    FROM users u
    WHERE u.role = 'peserta'
    $where_akses
    $where_search
");
$countRow = mysqli_fetch_assoc($countResult);
$total = (int)($countRow['total_data'] ?? 0);

$total_pages = max(1, (int)ceil($total / $per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $per_page;

// Hitung summary untuk seluruh data terfilter (bukan hanya halaman aktif)
$summaryResult = mysqli_query($conn, "
    SELECT
        SUM(CASE WHEN u.is_active = 1 THEN 1 ELSE 0 END) AS aktif,
        SUM(CASE WHEN iq.status = 'finished' THEN 1 ELSE 0 END) AS iq_selesai,
        SUM(CASE WHEN h1.nip IS NOT NULL THEN 1 ELSE 0 END) AS msdt_selesai,
        SUM(CASE WHEN h2.nip IS NOT NULL THEN 1 ELSE 0 END) AS papi_selesai
    FROM users u
    LEFT JOIN hasil_msdt h1 ON u.nip = h1.nip
    LEFT JOIN hasil_papi h2 ON u.nip = h2.nip
    LEFT JOIN iq_test_sessions iq ON u.nip = iq.nip
    WHERE u.role = 'peserta'
    $where_akses
    $where_search
");
$summary = mysqli_fetch_assoc($summaryResult) ?: [];
$aktif = (int)($summary['aktif'] ?? 0);
$iq_selesai = (int)($summary['iq_selesai'] ?? 0);
$msdt_selesai = (int)($summary['msdt_selesai'] ?? 0);
$papi_selesai = (int)($summary['papi_selesai'] ?? 0);

// Ambil data per halaman
$result = mysqli_query($conn, "
    SELECT 
        u.nip, u.nama, u.jabatan, u.satuan_kerja,
        COALESCE(u.pangkat_golongan, '-') AS pangkat,
        u.is_active,
        h1.nip  AS sudah_msdt,
        h2.nip  AS sudah_papi,
        iq.status AS status_iq
    FROM users u
    LEFT JOIN hasil_msdt h1 ON u.nip = h1.nip
    LEFT JOIN hasil_papi h2 ON u.nip = h2.nip
    LEFT JOIN iq_test_sessions iq ON u.nip = iq.nip
    WHERE u.role = 'peserta'
    $where_akses
    $where_search
    ORDER BY u.nama ASC
    LIMIT $per_page OFFSET $offset
");

$rows  = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}

$rows_count = count($rows);
$page_start_row = $total > 0 ? ($offset + 1) : 0;
$page_end_row = $total > 0 ? ($offset + $rows_count) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Status Tes Pegawai | Admin PETA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif']},colors:{navy:{DEFAULT:'#0F1E3C'}}}}}</script>
    <style>
        body{font-family:'Plus Jakarta Sans',sans-serif;}
        ::-webkit-scrollbar{width:5px;height:5px;}
        ::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:99px;}
        .badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;white-space:nowrap;}
        .badge-green{background:#d1fae5;color:#065f46;}
        .badge-red{background:#fee2e2;color:#991b1b;}
        .badge-blue{background:#dbeafe;color:#1e40af;}
        .badge-gray{background:#f1f5f9;color:#64748b;}
        .badge-amber{background:#fef3c7;color:#92400e;}
        #action-bar{transition:all .2s ease;}
    </style>
</head>
<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">

    <!-- Header -->
    <div class="mb-7">
        <h1 class="text-2xl font-extrabold text-navy tracking-tight">Data Pegawai</h1>
        <p class="text-slate-500 text-sm mt-1">Pantau dan kelola seluruh pegawai tes PETA.</p>
    </div>

    <!-- ALERTS -->
    <?php if (isset($_GET['import'])): ?>
    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-5 text-sm font-semibold">
        ✅ <?= (int)$_GET['count'] ?> data pegawai berhasil diimpor.
    </div>
    <?php endif; ?>
    <?php if (isset($_GET['bulk']) || (isset($_GET['status']) && $_GET['status']==='reset_berhasil')): ?>
    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-5 text-sm font-semibold">
        ✅ Aksi berhasil diterapkan.
    </div>
    <?php endif; ?>

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-5 gap-4 mb-6">
        <?php
        $cards = [
            ['Total Pegawai',   $total,        'bg-white',        'text-navy',      '👥'],
            ['Akun Aktif',      $aktif,        'bg-emerald-50',   'text-emerald-700','✓'],
            ['Tes IQ Selesai',   $iq_selesai,   'bg-blue-50',      'text-blue-700',  '①'],
            ['Tes Kprib. Bag.1 Selesai',    $msdt_selesai, 'bg-purple-50',    'text-purple-700','②'],
            ['Tes Kprib. Bag.2 Selesai',    $papi_selesai, 'bg-amber-50',     'text-amber-700', '②'],
        ];
        foreach ($cards as [$label, $val, $bg, $color, $icon]): ?>
        <div class="<?= $bg ?> rounded-xl border border-slate-100 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-slate-400 mb-1"><?= $label ?></p>
            <p class="text-2xl font-black <?= $color ?>"><?= $val ?></p>
            <p class="text-xs text-slate-300 mt-1">dari <?= $total ?> pegawai</p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- IMPORT CSV -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-navy">Import Data Pegawai (CSV)</p>
                <p class="text-xs text-slate-400 mt-0.5">Format: <span class="font-mono bg-slate-50 px-1.5 py-0.5 rounded border text-[11px]">No, NIP, Nama, Jabatan, Satker, Pangkat</span> — File: <strong>.xlsx</strong> atau .csv</p>
            </div>
            <button onclick="document.getElementById('ip').classList.toggle('hidden')"
                class="text-xs bg-navy text-white px-4 py-2 rounded-lg font-bold hover:opacity-90 transition">
                + Import CSV
            </button>
        </div>
        <div id="ip" class="hidden mt-4 pt-4 border-t border-slate-100">
            <div class="mb-3">
                <a href="../formatregisPETA.xlsx" download
                    class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-100 transition">
                    ⬇ Download Template Import
                </a>
            </div>
            <form method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                <input type="hidden" name="akses_filter" value="<?= htmlspecialchars($akses_filter) ?>">
                <input type="file" name="csv_file" accept=".csv,.xlsx,.xls" required
                    class="text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-semibold hover:file:bg-slate-200 cursor-pointer">
                <button type="submit" name="import_csv"
                    class="bg-navy text-white px-4 py-1.5 rounded-lg text-sm font-bold hover:opacity-90 transition">Unggah</button>
            </form>
        </div>
    </div>

    <!-- FILTER STATUS AKSES -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 mb-5">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-sm font-bold text-navy">Filter Status Akses</p>
                <p class="text-xs text-slate-400 mt-0.5">Tampilkan pegawai berdasarkan status akun.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="status_pegawai.php?<?= http_build_query(array_filter(['page' => 1, 'q' => $search_query])) ?>"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border <?= $akses_filter === 'all' ? 'bg-navy text-white border-navy' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                    Semua
                </a>
                <a href="status_pegawai.php?<?= http_build_query(array_filter(['akses' => 'active', 'page' => 1, 'q' => $search_query])) ?>"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border <?= $akses_filter === 'active' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                    Aktif
                </a>
                <a href="status_pegawai.php?<?= http_build_query(array_filter(['akses' => 'inactive', 'page' => 1, 'q' => $search_query])) ?>"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border <?= $akses_filter === 'inactive' ? 'bg-slate-700 text-white border-slate-700' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                    Non-aktif
                </a>
            </div>
        </div>
    </div>

    <!-- TABEL -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">

        <!-- Toolbar -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 gap-3 flex-wrap">
            <h3 class="text-sm font-bold text-navy">Daftar Pegawai <span class="text-slate-400 font-normal">(<?= $total ?>)</span></h3>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- Action bar -->
                <div id="action-bar" class="hidden items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                    <span class="text-xs font-bold text-navy mr-1" id="sel-count">0 dipilih</span>
                    <span class="w-px h-4 bg-slate-300 inline-block"></span>
                    <button type="button" onclick="doBulk('aktifkan')" class="badge badge-green cursor-pointer hover:opacity-80">✓ Aktifkan</button>
                    <button type="button" onclick="doBulk('nonaktifkan')" class="badge badge-gray cursor-pointer hover:opacity-80">✗ Non-aktif</button>
                    <button type="button" onclick="doBulk('reset_iq')" class="badge badge-blue cursor-pointer hover:opacity-80">↺ Reset IQ</button>
                    <button type="button" onclick="doBulk('reset_msdt')" class="badge cursor-pointer hover:opacity-80" style="background:#ede9fe;color:#5b21b6">↺ Reset Kprib.1</button>
                    <button type="button" onclick="doBulk('reset_papi')" class="badge badge-amber cursor-pointer hover:opacity-80">↺ Reset Kprib.2</button>
                    <button type="button" onclick="clearAll()" class="text-slate-400 hover:text-slate-600 text-sm ml-1">✕</button>
                </div>

                <!-- Search (server-side) -->
                <form method="GET" class="relative flex items-center gap-2">
                    <?php if ($akses_filter !== 'all'): ?>
                        <input type="hidden" name="akses" value="<?= htmlspecialchars($akses_filter) ?>">
                    <?php endif; ?>
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔍</span>
                    <input id="q" name="q" type="text" placeholder="Cari pegawai"
                        value="<?= htmlspecialchars($search_query) ?>"
                        class="pl-8 pr-7 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white w-72 placeholder-slate-400 transition-all">
                    <button type="submit" class="px-3 py-2 text-xs rounded-lg bg-navy text-white font-semibold hover:opacity-90">Cari</button>
                    <?php if ($search_query !== ''): ?>
                        <button type="button" onclick="clearSearch()" id="clrQ"
                            class="absolute right-[86px] top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500">✕</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Table -->
        <form id="bf" method="POST">
            <input type="hidden" name="bulk_action" value="1">
            <input type="hidden" name="action_type" id="at">
            <input type="hidden" name="akses_filter" value="<?= htmlspecialchars($akses_filter) ?>">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-4 py-3 text-left w-8">
                                <input type="checkbox" id="sa" class="rounded cursor-pointer accent-navy">
                            </th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider w-8">No</th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nama / NIP</th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jabatan</th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Unit Kerja</th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pangkat</th>
                            <th class="px-3 py-3 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">Akses</th>
                            <th class="px-3 py-3 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tes IQ</th>
                            <th class="px-3 py-3 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kprib. Bag.1</th>
                            <th class="px-3 py-3 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kprib. Bag.2</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $i => $p): 
                        $row_number = $offset + $i + 1;
                        $search_data = strtolower(implode(' ', [
                            $p['nip'],
                            $p['nama'],
                            $p['jabatan'] ?? '',
                            $p['satuan_kerja'] ?? '',
                            $p['pangkat'] ?? '',
                            $p['is_active'] ? 'aktif' : 'non-aktif',
                            $p['status_iq'] === 'finished' ? 'selesai' : ($p['status_iq'] === 'running' ? 'sedang' : 'belum'),
                            $p['sudah_msdt'] ? 'selesai' : 'belum',
                            $p['sudah_papi'] ? 'selesai' : 'belum',
                        ]));
                    ?>
                        <tr class="row border-b border-slate-50 hover:bg-slate-50/70 transition-colors"
                            data-s="<?= htmlspecialchars($search_data) ?>">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="selected_nip[]" value="<?= $p['nip'] ?>"
                                    class="cb rounded cursor-pointer accent-navy">
                            </td>
                            <td class="px-3 py-3 text-slate-400 text-xs"><?= $row_number ?></td>

                            <!-- Nama -->
                            <td class="px-3 py-3">
                                <a href="detail_pegawai.php?nip=<?= $p['nip'] ?>" class="group block">
                                    <p class="font-semibold text-slate-800 group-hover:text-blue-600 transition-colors text-sm leading-tight">
                                        <?= htmlspecialchars($p['nama']) ?>
                                    </p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5"><?= $p['nip'] ?></p>
                                </a>
                            </td>

                            <!-- Jabatan -->
                            <td class="px-3 py-3 text-slate-500 text-xs leading-tight max-w-[140px]">
                                <?= htmlspecialchars($p['jabatan'] ?? '-') ?>
                            </td>

                            <!-- Unit Kerja -->
                            <td class="px-3 py-3 text-slate-500 text-xs leading-tight max-w-[120px]">
                                <?= htmlspecialchars($p['satuan_kerja'] ?? '-') ?>
                            </td>

                            <!-- Pangkat -->
                            <td class="px-3 py-3 text-slate-500 text-xs">
                                <?= htmlspecialchars($p['pangkat']) ?>
                            </td>

                            <!-- Akses -->
                            <td class="px-3 py-3 text-center">
                                <?php if ($p['is_active']): ?>
                                    <span class="badge badge-green">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-gray">Non-aktif</span>
                                <?php endif; ?>
                            </td>

                            <!-- Tes 1 IQ -->
                            <td class="px-3 py-3 text-center">
                                <?php if ($p['status_iq']==='finished'): ?>
                                    <span class="badge badge-green">✓</span>
                                <?php elseif ($p['status_iq']==='running'): ?>
                                    <span class="badge badge-blue">⟳</span>
                                <?php else: ?>
                                    <span class="badge badge-red">✗</span>
                                <?php endif; ?>
                            </td>

                            <!-- MSDT -->
                            <td class="px-3 py-3 text-center">
                                <span class="badge <?= $p['sudah_msdt'] ? 'badge-green' : 'badge-red' ?>">
                                    <?= $p['sudah_msdt'] ? '✓' : '✗' ?>
                                </span>
                            </td>

                            <!-- PAPI -->
                            <td class="px-3 py-3 text-center">
                                <span class="badge <?= $p['sudah_papi'] ? 'badge-green' : 'badge-red' ?>">
                                    <?= $p['sudah_papi'] ? '✓' : '✗' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                        <tr id="er" class="<?= !empty($rows) ? 'hidden' : '' ?>">
                            <td colspan="10" class="text-center py-16 text-slate-400">
                                <p class="text-3xl mb-2">🔍</p>
                                <p class="text-sm">Tidak ada pegawai yang cocok.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Footer info -->
        <div class="px-6 py-3 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            <p class="text-xs text-slate-400" id="info-row">
                Menampilkan <?= $page_start_row ?>-<?= $page_end_row ?> dari <?= $total ?> pegawai (Halaman <?= $page ?> / <?= $total_pages ?>)
            </p>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <span class="flex items-center gap-1"><span class="badge badge-green">✓</span> Selesai</span>
                    <span class="flex items-center gap-1"><span class="badge badge-blue">⟳</span> Sedang</span>
                    <span class="flex items-center gap-1"><span class="badge badge-red">✗</span> Belum</span>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="flex items-center gap-1">
                    <?php
                        $prev_page = max(1, $page - 1);
                        $next_page = min($total_pages, $page + 1);
                        $base_qs = [];
                        if ($akses_filter !== 'all') {
                            $base_qs['akses'] = $akses_filter;
                        }
                        if ($search_query !== '') {
                            $base_qs['q'] = $search_query;
                        }
                    ?>
                    <a href="status_pegawai.php?<?= http_build_query(array_merge($base_qs, ['page' => $prev_page])) ?>"
                       class="px-2.5 py-1.5 rounded-md border text-xs font-semibold <?= $page <= 1 ? 'pointer-events-none opacity-40 bg-slate-50 text-slate-400 border-slate-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                        Prev
                    </a>

                    <?php
                        $window = 2;
                        $start_page = max(1, $page - $window);
                        $end_page = min($total_pages, $page + $window);
                        for ($p = $start_page; $p <= $end_page; $p++):
                    ?>
                        <a href="status_pegawai.php?<?= http_build_query(array_merge($base_qs, ['page' => $p])) ?>"
                           class="px-2.5 py-1.5 rounded-md border text-xs font-bold <?= $p === $page ? 'bg-navy text-white border-navy' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>

                    <a href="status_pegawai.php?<?= http_build_query(array_merge($base_qs, ['page' => $next_page])) ?>"
                       class="px-2.5 py-1.5 rounded-md border text-xs font-semibold <?= $page >= $total_pages ? 'pointer-events-none opacity-40 bg-slate-50 text-slate-400 border-slate-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                        Next
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<script>
// CHECKBOX
function getVisibleCheckboxes() {
    return Array.from(document.querySelectorAll('.row:not(.hidden) .cb'));
}

function syncSelectAllState() {
    const sa = document.getElementById('sa');
    const visible = getVisibleCheckboxes();
    if (visible.length === 0) {
        sa.checked = false;
        sa.indeterminate = false;
        return;
    }

    const checkedCount = visible.filter(c => c.checked).length;
    sa.checked = checkedCount === visible.length;
    sa.indeterminate = checkedCount > 0 && checkedCount < visible.length;
}

document.getElementById('sa').addEventListener('change', function() {
    const visible = getVisibleCheckboxes();
    visible.forEach(c => c.checked = this.checked);
    syncSelectAllState();
    updBar();
});

document.querySelectorAll('.cb').forEach(c => c.addEventListener('change', function() {
    syncSelectAllState();
    updBar();
}));

function updBar() {
    const n   = document.querySelectorAll('.cb:checked').length;
    const bar = document.getElementById('action-bar');
    document.getElementById('sel-count').textContent = n + ' dipilih';
    if (n > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
    else        { bar.classList.add('hidden');    bar.classList.remove('flex'); }
}

function clearAll() {
    document.querySelectorAll('.cb, #sa').forEach(c => c.checked = false);
    document.getElementById('sa').indeterminate = false;
    updBar();
}

function doBulk(action) {
    const n = document.querySelectorAll('.cb:checked').length;
    const lbl = {aktifkan:'mengaktifkan',nonaktifkan:'menonaktifkan',reset_iq:'mereset Tes IQ',reset_msdt:'mereset Tes Kepribadian Bagian 2',reset_papi:'mereset Tes Kepribadian Bagian 1'};

    if (!confirm(`Yakin ${lbl[action]} ${n} pegawai terpilih?\nTindakan ini tidak bisa dibatalkan.`)) return;
    document.getElementById('at').value = action;
    document.getElementById('bf').submit();
}


// SEARCH (server-side)
function clearSearch() {
    const params = new URLSearchParams(window.location.search);
    params.delete('q');
    params.set('page', '1');
    const qs = params.toString();
    window.location.href = 'status_pegawai.php' + (qs ? ('?' + qs) : '');
}

document.addEventListener('keydown', e => {
    if (e.key==='/' && document.activeElement.tagName!=='INPUT') {
        e.preventDefault(); document.getElementById('q').focus();
    }
});

syncSelectAllState();
</script>
</body>
</html>