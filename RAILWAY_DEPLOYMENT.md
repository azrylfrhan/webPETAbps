# 🚀 Railway Deployment Guide - Tes Psikotes

## ✅ Apa yang sudah disiapkan

1. **✓ Database Export** (`bps_psikotes_full.sql`)
   - Dump lengkap dari XAMPP local
   - Ukuran: Semua 16 tabel dengan data

2. **✓ Import Script** (`import_railway.php`)
   - PHP script untuk import otomatis ke Railway
   - Dengan verifikasi tabel dan error handling

3. **✓ Batch Runner** (`import_railway.bat`)
   - Windows batch script untuk menjalankan import dengan 1 klik
   - Auto-detect PHP, error handling, progress display

4. **✓ Updated Config** (`backend/config.php`)
   - Support environment variables dari Railway
   - Fallback ke localhost untuk development
   - Formula: `getenv('MYSQLHOST') ?: "localhost"`

5. **✓ Docker Config** (`Dockerfile`)
   - Apache + PHP 8.1 + MySQLi
   - Health checks included
   - Auto-run pada Railway

6. **✓ Railway Config** (`railway.toml`)
   - Build dan deploy settings
   - Health check configuration

---

## 📋 Step-by-Step Deployment

### **STEP 1: Push Code ke Railway** 🚀

Railway perlu kode + Docker config untuk deploy:

```bash
# Dari directory Tes-Psikotes
git add .
git commit -m "Add: Railway deployment config + environment support"
git push railway main
```

**Tunggu sampai:**
- Build completed ✓
- Service running ✓
- Domain URL muncul di Railway dashboard

---

### **STEP 2: Initial Setup - Import Database** ⚡

**Opsi A: Via Railway CLI (Recommended - Tercepat)**

Jika sudah install Railway CLI:
```bash
railway login
railway shell
mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < bps_psikotes_full.sql
```

**Opsi B: Via Web Setup Script**

Setelah app deployed, akses:
```
https://[your-app].railway.app/setup.php?key=setup_bps_psikotes_2026
```

Ini akan:
- Verify database connection
- List semua tabel + row counts
- Confirm database siap

---

### **STEP 3: Atur Environment Variables di Railway** (jika belum auto-inject)

1. **Buka Railway Dashboard** → Project Anda
2. **Variables Tab** → Klik "+ New Variable"

Tambahkan semua variable ini:
```
MYSQLHOST = mysql.railway.internal
MYSQLUSER = root
MYSQLPASSWORD = fTiMPxhUEVeKsmIyMMaOFzoeBTOYQqgQ
MYSQLDATABASE = railway
MYSQLPORT = 3306
```

*(Variable ini auto-inject dari Railway MySQL plugin, juga bisa manual seperti atas)*

---

### **STEP 3: Push Code ke Railway**

```bash
# Setup Railway remote (jika belum)
railway link [project-id]

# Atau manual
git remote add railway https://git.railway.app/[username]/[project-id].git

# Push kode
git add .
git commit -m "Deploy: Add Railway config dan environment support"
git push railway main
```

---

### **STEP 4: Deploy & Verify**

1. **Cek logs di Railway Dashboard:**
   - Services → [App Name] → Logs
   - Tunggu "Build completed" → "Running"

2. **Test aplikasi:**
   ```
   https://[your-railway-domain].railway.app
   ```

3. **Cek database connection:**
   - Buka dashboard.php
   - Jika muncul test cards (Tes 1, Tes 2, Tes 3) = ✅ Database connected

---

## 🔧 Troubleshooting

### ❌ "Koneksi database gagal"
**Solusi:**
- Cek Railway MySQL status (Logs)
- Verify credentials di railway.toml dan environment variables
- Cek port 3306 accessible dari Railway app

### ❌ "mysql.railway.internal not found"
**Solusi:**
- Ini normal, Railway memerlukan beberapa detik untuk DNS propagation
- Tunggu 1-2 menit dan refresh
- Atau gunakan IP address jika tersedia di Railway dashboard

### ❌ "CORS / 404 errors"
**Solusi:**
- Pastikan `.htaccess` di-enable di Apache (sudah di-include di Dockerfile)
- Cek relative paths di API calls (sudah fixed: `api/...`)
- Check Laravel/routing rules jika ada

### ✅ Database imported tapi test tidak jalan
**Solusi:**
- Cek `iq_test_sessions` table punya data
- Jalankan: `curl https://[domain]/tes_proses/tes_iq/api/get_section.php`
- Jika 401 = Session issue, pastikan user login dulu

---

## 📊 Database Info (untuk verifikasi)

Setelah import, cek tabel-tabel ini ada di Railway:

| Kategori | Tabel | Rows |
|----------|-------|------|
| User | `users` | (sesuai data) |
| Test IQ | `iq_sections` | 9 |
| | `iq_questions` | 176 |
| | `iq_options` | 800 |
| | `iq_example_questions` | 9 |
| | `iq_example_options` | 40 |
| | `iq_test_sessions` | (per session) |
| | `iq_user_answers` | (per answers) |
| Test PAPI | `soal` | (sesuai data) |
| Results | `hasil_msdt` | (per results) |
| | `hasil_papi` | (per results) |

**Command untuk verify:**
```sql
SHOW TABLES;
SELECT COUNT(*) FROM iq_sections;  -- Harus 9
SELECT COUNT(*) FROM iq_questions; -- Harus 176
SELECT COUNT(*) FROM iq_options;   -- Harus 800
```

---

## 🎯 Next Steps (Optional)

- [ ] Setup custom domain di Railway
- [ ] Configure email notifications (untuk hasil tes)
- [ ] Setup backup schedule
- [ ] Add SSL certificate (Railway otomatis provide)

---

## 📞 Emergency: Rollback ke Localhost

Jika production crash, bisa kembali ke development:

1. **Config otomatis fallback ke localhost** (sudah setup)
2. **XAMPP server on:**
   ```bash
   C:\xampp\mysql\bin\mysqld.exe
   C:\xampp\apache\bin\httpd.exe
   ```
3. **atau gunakan XAMPP Control Panel**

---

**Created:** March 31, 2026  
**Project:** Tes Psikotes  
**Status:** Ready for Railway Deployment ✅
