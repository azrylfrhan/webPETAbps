<?php 
include '../backend/auth_check.php'; 
require_once '../backend/config.php';

$nip = mysqli_real_escape_string($conn, $_GET['nip']);
$has_biodata = false;
$cek_biodata = mysqli_query($conn, "SHOW TABLES LIKE 'biodata_peserta'");
if ($cek_biodata && mysqli_num_rows($cek_biodata) > 0) {
    $has_biodata = true;
}

$select_usia = $has_biodata ? "TIMESTAMPDIFF(YEAR, b.tanggal_lahir, CURDATE()) AS usia" : "NULL AS usia";
$select_tempat_lahir = $has_biodata ? "b.tempat_lahir" : "NULL AS tempat_lahir";
$select_tanggal_lahir = $has_biodata ? "b.tanggal_lahir" : "NULL AS tanggal_lahir";
$select_email = $has_biodata ? "b.email" : "NULL AS email";
$join_biodata = $has_biodata ? "LEFT JOIN biodata_peserta b ON b.nip = u.nip" : "";

$sql = "SELECT u.*, $select_usia, $select_tempat_lahir, $select_tanggal_lahir, $select_email
        FROM users u
        $join_biodata
        WHERE u.nip = '$nip' AND u.role = 'peserta'";

$query = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($query);

if (!$user) { header("Location: status_pegawai.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pegawai | Admin BPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-100 flex min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<div class="ml-[260px] flex-1 p-8">
    <div class="max-w-3xl mx-auto">
        <a href="status_pegawai.php" class="inline-flex items-center text-sm text-slate-500 hover:text-navy mb-6 transition-colors">
            ← Kembali ke Daftar
        </a>

        <?php if(isset($_GET['new_pass'])): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-emerald-800 text-sm font-bold">Password berhasil di-reset!</p>
                <p class="text-emerald-600 text-xs">Berikan password ini kepada pegawai: <span class="bg-white px-2 py-1 rounded border font-mono text-lg select-all"><?= htmlspecialchars($_GET['new_pass']) ?></span></p>
            </div>
            <button onclick="navigator.clipboard.writeText('<?= $_GET['new_pass'] ?>')" class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg font-bold">Salin</button>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-navy p-6 text-white">
                <h2 class="text-xl font-bold">Profil Lengkap Pegawai</h2>
                <p class="text-slate-400 text-sm mt-1">NIP: <?= $user['nip'] ?></p>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-2 gap-6 mb-10">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                        <p class="text-slate-700 font-semibold border-b border-slate-100 pb-2 mt-1"><?= htmlspecialchars($user['nama']) ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Unit Kerja</label>
                        <p class="text-slate-700 font-semibold border-b border-slate-100 pb-2 mt-1"><?= htmlspecialchars($user['satuan_kerja']) ?></p>
                    </div>
                </div>

                <div class="mb-10 bg-sky-50 rounded-xl p-6 border border-sky-100">
                    <div class="mb-4 pb-3 border-b border-sky-200">
                        <h3 class="text-sm font-bold text-slate-700">Biodata Peserta</h3>
                        <p class="text-xs text-slate-500 mt-1">Usia dihitung otomatis dari tanggal lahir dan tanggal hari ini.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tempat Lahir</label>
                        <p class="text-slate-700 font-semibold mt-2"><?= htmlspecialchars($user['tempat_lahir'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Lahir</label>
                        <p class="text-slate-700 font-semibold mt-2"><?= !empty($user['tanggal_lahir']) ? date('d/m/Y', strtotime($user['tanggal_lahir'])) : '-' ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Usia (Otomatis)</label>
                        <p class="text-slate-700 font-semibold mt-2"><?= isset($user['usia']) && $user['usia'] !== null ? (int)$user['usia'] . ' tahun' : '-' ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</label>
                        <p class="text-slate-700 font-semibold mt-2 break-all"><?= htmlspecialchars($user['email'] ?? '-') ?></p>
                    </div>
                    </div>

                    <?php if (empty($user['tanggal_lahir']) && empty($user['email']) && empty($user['tempat_lahir'])): ?>
                    <div class="mt-5 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        Biodata belum diisi oleh peserta.
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Jabatan & Pangkat Section -->
                <div class="grid grid-cols-2 gap-6 mb-10 bg-slate-50 rounded-xl p-6 border border-slate-200">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Jabatan</label>
                        <p id="jabatan-display" class="text-slate-700 font-semibold mt-2"><?= htmlspecialchars($user['jabatan'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pangkat/Golongan</label>
                        <p id="pangkat-display" class="text-slate-700 font-semibold mt-2"><?= htmlspecialchars($user['pangkat_golongan'] ?? '-') ?></p>
                    </div>
                    <div class="col-span-2 pt-4 border-t border-slate-200">
                        <button onclick="openEditModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm">
                            ✏️ Edit Informasi
                        </button>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
                    <h3 class="text-amber-800 font-bold text-sm mb-1">Reset Akses Akun</h3>
                    <p class="text-amber-700/70 text-xs mb-4">Sistem akan membuatkan password acak baru secara otomatis.</p>
                    
                    <form action="proses_reset_pass.php" method="POST" onsubmit="return confirm('Generate password acak baru untuk pegawai ini?');">
                        <input type="hidden" name="nip" value="<?= $user['nip'] ?>">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Informasi -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-navy p-6 text-white">
            <h3 class="text-lg font-bold">Edit Informasi Pegawai</h3>
            <p class="text-slate-400 text-sm mt-1">NIP: <?= $user['nip'] ?></p>
        </div>
        
        <form id="editForm" method="POST" action="proses_edit_pegawai.php" class="p-6 space-y-5">
            <input type="hidden" name="nip" value="<?= $user['nip'] ?>">
            
            <div>
                <label class="text-sm font-semibold text-slate-700 block mb-2">Nama Lengkap</label>
                <input type="text" 
                       name="nama" 
                       id="nama-input"
                       value="<?= htmlspecialchars($user['nama'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700"
                       placeholder="Masukkan nama lengkap">
            </div>
            
            <div>
                <label class="text-sm font-semibold text-slate-700 block mb-2">Unit Kerja</label>
                <input type="text" 
                       name="satuan_kerja" 
                       id="satuan_kerja-input"
                       value="<?= htmlspecialchars($user['satuan_kerja'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700"
                       placeholder="e.g., BPS Sulawesi Utara">
            </div>
            
            <div>
                <label class="text-sm font-semibold text-slate-700 block mb-2">Jabatan</label>
                <input type="text" 
                       name="jabatan" 
                       id="jabatan-input"
                       value="<?= htmlspecialchars($user['jabatan'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700"
                       placeholder="e.g., Statistisi Muda">
            </div>
            
            <div>
                <label class="text-sm font-semibold text-slate-700 block mb-2">Pangkat/Golongan</label>
                <input type="text" 
                       name="pangkat" 
                       id="pangkat-input"
                       value="<?= htmlspecialchars($user['pangkat_golongan'] ?? '') ?>" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-700"
                       placeholder="e.g., III/b">
            </div>
            
            <div class="pt-4 border-t border-slate-200 flex gap-3">
                <button type="button" 
                        onclick="closeEditModal()" 
                        class="flex-1 px-4 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-all shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal ketika klik di luar modal
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Handle form submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('proses_edit_pegawai.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display values
            document.querySelector('.grid.grid-cols-2.gap-6.mb-10 > div:nth-child(1) > p').innerText = data.nama;
            document.querySelector('.grid.grid-cols-2.gap-6.mb-10 > div:nth-child(2) > p').innerText = data.satuan_kerja;
            document.getElementById('jabatan-display').innerText = data.jabatan;
            document.getElementById('pangkat-display').innerText = data.pangkat;
            closeEditModal();
            
            // Show success notification
            showNotification('✓ Data berhasil diperbarui!', 'success');
        } else {
            showNotification('❌ ' + (data.error || 'Gagal menyimpan data'), 'error');
        }
    })
    .catch(error => {
        showNotification('❌ Terjadi kesalahan: ' + error, 'error');
    });
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-6 right-6 px-6 py-3 rounded-lg text-white font-semibold shadow-lg z-50 animate-pulse
        ${type === 'success' ? 'bg-emerald-500' : 'bg-red-500'}`;
    notification.innerText = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modal dengan ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});
</script>
</body>
</html>