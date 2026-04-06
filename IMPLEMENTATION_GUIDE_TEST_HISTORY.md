# Panduan Implementasi: Riwayat Tes IQ Komprehensif

## 📋 Ringkasan Fitur

Fitur baru ini memungkinkan:
1. ✅ Setiap tes terekam sebagai percobaan terpisah (tidak menimpa hasil sebelumnya)
2. ✅ Menyimpan tanggal tes, alasan tes, semua jawaban, dan skor setiap bagian
3. ✅ Menampilkan riwayat lengkap di halaman detail pegawai
4. ✅ Admin dapat melihat jawaban untuk setiap percobaan
5. ✅ Ketika tes direset, percobaan lama tetap terekam dalam riwayat

---

## 🔧 Langkah-Langkah Implementasi

### 1. Jalankan Migrasi Database

Jalankan file SQL migration untuk membuat tabel baru:

```bash
# File: tes_proses/sql/migration_test_history.sql
mysql -u root -p bps_psikotes < tes_proses/sql/migration_test_history.sql
```

Atau jalankan melalui phpMyAdmin / MySQL GUI:

Tabel baru yang akan dibuat:
- **iq_test_attempts** - Melacak setiap percobaan tes
- **iq_attempt_answers** - Menyimpan jawaban untuk setiap percobaan
- **iq_attempt_results** - Menyimpan hasil skor untuk setiap percobaan

### 2. Perbarui Database Connection di File Backend

File `/backend/test_attempt_functions.php` sudah dibuat dengan fungsi-fungsi:
- `createTestAttempt()` - Membuat percobaan tes baru
- `getCurrentAttempt()` - Mendapat percobaan aktif
- `getAttemptHistory()` - Mendapat semua percobaan
- `completeAttempt()` - Menyelesaikan percobaan
- `calculateAttemptResults()` - Hitung skor
- `resetTestWithHistory()` - Reset dengan tracking

### 3. Update Script Test Engine IQ

File yang perlu diupdate:
- `/tes_proses/tes_iq/test_save.php` - Update untuk save ke iq_attempt_answers
- `/tes_proses/tes_iq/api/finish_test.php` - Update untuk call completeAttempt()
- `/tes_proses/tes_iq/api/start_session.php` - Update untuk create attempt

**Contoh modifikasi di start_session.php:**
```php
require_once '../../../backend/test_attempt_functions.php';

$nip = $_SESSION['user_nip'];

// Buat percobaan baru
$attempt_id = createTestAttempt($conn, $nip);

if ($attempt_id) {
    $_SESSION['current_attempt_id'] = $attempt_id;
    echo json_encode(['success' => true, 'attempt_id' => $attempt_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal membuat percobaan tes']);
}
```

**Contoh modifikasi di test_save.php:**
```php
$attempt_id = $_SESSION['current_attempt_id'] ?? null;

if ($attempt_id) {
    $stmt = $conn->prepare("
        INSERT INTO iq_attempt_answers 
        (attempt_id, user_nip, question_id, jawaban_user)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE jawaban_user = ?
    ");
    $stmt->bind_param('issss', $attempt_id, $nip, $q_id, $answer, $answer);
    $stmt->execute();
    $stmt->close();
}
```

### 4. Update Reset Logic

✅ Sudah diupdate di `/admin/status_pegawai.php`

File sekarang menggunakan `resetTestWithHistory()` yang:
- Menandai percobaan lama sebagai "incomplete" jika masih berjalan
- Membuat percobaan baru dengan nomor urut bertambah
- Menyimpan alasan reset di database
- Admin diminta input alasan sebelum reset (modal dialog)

### 5. Aktivasi View Di Admin Area

✅ Sudah diupdate di `/admin/detail_pegawai.php`

Fitur baru:
- Menampilkan tab "Riwayat Tes IQ" dengan semua percobaan
- Untuk setiap percobaan yang selesai: tanggal, alasan, skor 9 bagian, total
- Tombol "Lihat Jawaban" untuk detail lengkap
- Modal menampilkan semua jawaban dengan kunci jawaban

---

## 🚀 Status Implementasi

### ✅ Sudah Selesai:
1. SQL migration files dibuat (`migration_test_history.sql`)
2. Backend functions dibuat (`test_attempt_functions.php`)
3. Admin reset workflow diupdate (`status_pegawai.php`)
4. Admin detail page diupdate (`detail_pegawai.php`)
5. API endpoint dibuat (`admin/api/get_attempt_answers.php`)

### ⚠️ Masih Perlu Dikerjakan:
1. Update test engine IQ untuk gunakan attempt tracking
   - [ ] `/tes_proses/tes_iq/api/start_session.php`
   - [ ] `/tes_proses/tes_iq/api/save_answer.php` atau `test_save.php`
   - [ ] `/tes_proses/tes_iq/api/finish_test.php`

2. Testing menyeluruh dengan test attempt baru

3. (Optional) Migrate data lama ke tabel  baru jika ada data existing

---

## 📊 Contoh Data Structure

### iq_test_attempts
```
id | nip        | attempt_number | tanggal_mulai | tanggal_selesai | alasan_tes               | status
1  | 21024160   | 1              | 2024-04-06... | 2024-04-06...   | Tes awal                 | finished
2  | 21024160   | 2              | 2024-04-07... | 2024-04-07...   | Reset tes oleh admin     | finished
3  | 21024160   | 3              | 2024-04-08... | NULL            | Pegawai minta ulang test | running
```

### iq_attempt_answers
```
id | attempt_id | user_nip  | question_id | jawaban_user | waktu_jawab
1  | 1          | 21024160  | 1           | A            | 2024-04-06...
2  | 1          | 21024160  | 2           | B            | 2024-04-06...
...
```

### iq_attempt_results
```
id | attempt_id | user_nip  | se | wa | an | ge | ra | zr | fa | wu | me | skor_total
1  | 1          | 21024160  | 18 | 15 | 14 | 12 | 16 | 14 | 13 | 4* | 17 | 123
2  | 2          | 21024160  | 19 | 16 | 15 | 13 | 17 | 15 | 14 | 8* | 18 | 135
```

*Note: WU section masih perlu pengecekan kembali sesuai kunci jawaban

---

## 🔄 Workflow Alur

### Alur Tes Baru:
1. Admin aktivasi/memberikan akses tes ke pegawai
2. Pegawai mulai tes IQ → `start_session` create `iq_test_attempts` record
3. Pegawai jawab pertanyaan → save ke `iq_attempt_answers` dengan attempt_id
4. Pegawai selesai → `finish_test` call `completeAttempt()` yang hitung skor
5. Skor disimpan di `iq_attempt_results`

### Alur Reset:
1. Admin buka `/admin/status_pegawai.php`
2. Pilih pegawai + klik "Reset IQ"
3. Modal meminta alasan reset
4. Percobaan lama ditandai "incomplete", percobaan baru dibuat dengan nomor +1
5. Alasan disimpan di database
6. Di `/admin/detail_pegawai.php` admin bisa lihat riwayat lengkap

---

## 🧪 Testing Checklist

- [ ] Jalankan SQL migration - tabel terbuat
- [ ] Buka `/admin/detail_pegawai.php` - tab "Riwayat Tes IQ" muncul
- [ ] Update API endpoint test engine
- [ ] Test pegawai mulai tes baru - `iq_test_attempts` auto-create
- [ ] Test pegawai jawab pertanyaan - save ke `iq_attempt_answers`
- [ ] Test pegawai selesai tes - skor otomatis hitung
- [ ] Admin reset test - percobaan baru dibuat, lama tetap terekam
- [ ] Klik "Lihat Jawaban" di detail pegawai - tampil jawaban+kunci

---

## 📞 Troubleshooting

### Tabel tidak muncul di phpMyAdmin
- Check: Masuk ke database `bps_psikotes`
- Jalankan ulang migration: `mysql -u root -p bps_psikotes < migration_test_history.sql`

### Modal alasan tidak muncul saat reset
- Check: JavaScript `openReasonModal()` di `status_pegawai.php`
- Console browser untuk error

### Jawaban tidak tersimpan
- Check: `iq_attempt_answers` kosong?
- Verify: `start_session` setkan `$_SESSION['current_attempt_id']`
- Verify: `save_answer` API pakai `attempt_id` saat INSERT

### Skor tidak terhitung
- Check: `iq_attempt_results` kosong?
- Debug: `completeAttempt()` function di `test_attempt_functions.php`
- Check: Jawaban key match dengan database `iq_questions.jawaban_benar`

---

## 📝 Files Modified / Created

**Created:**
- ✅ `tes_proses/sql/migration_test_history.sql`
- ✅ `backend/test_attempt_functions.php`
- ✅ `admin/api/get_attempt_answers.php`

**Modified:**
- ✅ `admin/status_pegawai.php` - Reset workflow + modal
- ✅ `admin/detail_pegawai.php` - History display section

**To Update (Next Step):**
- ⏳ `tes_proses/tes_iq/api/start_session.php`
- ⏳ `tes_proses/tes_iq/api/save_answer.php`
- ⏳ `tes_proses/tes_iq/api/finish_test.php`

---

## 💾 Backup Recommendation

Sebelum running migration:
```bash
# Backup database
mysqldump -u root -p bps_psikotes > bps_psikotes_backup_$(date +%Y%m%d).sql
```

---

## ✉️ Kontakt Support

Jika ada masalah atau pertanyaan tentang implementasi, lihat file `.instructions.md` atau dokumentasi dalam kode.
