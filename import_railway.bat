@echo off
REM ========================================
REM Railway Database Import Script (Windows)
REM ========================================
REM Script ini mengimport database bps_psikotes ke Railway MySQL
REM Pastikan sudah:
REM   1. Export bps_psikotes_full.sql dari XAMPP (sudah selesai)
REM   2. Punya koneksi internet ke Railway
REM ========================================

cls
echo.
echo ╔══════════════════════════════════════════╗
echo ║   Import Database ke Railway MySQL       ║
echo ╚══════════════════════════════════════════╝
echo.

REM Get script directory
cd /d "%~dp0"

REM Check if PHP is available
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ ERROR: PHP tidak ditemukan!
    echo.
    echo Pastikan PHP sudah di-install atau XAMPP sudah berjalan.
    echo Path PHP: C:\xampp\php\php.exe
    echo.
    pause
    exit /b 1
)

echo ✓ PHP ditemukan
echo ✓ Script directory: %cd%
echo.

REM Check if SQL file exists
if not exist "bps_psikotes_full.sql" (
    echo ❌ ERROR: Tidak dapat menemukan bps_psikotes_full.sql
    echo.
    echo File harus berada di: %cd%\bps_psikotes_full.sql
    echo.
    pause
    exit /b 1
)

echo ✓ File SQL ditemukan
echo.
echo ═══════════════════════════════════════════
echo 🔄 Memulai import database...
echo ═══════════════════════════════════════════
echo.

REM Run PHP import script
php import_railway.php

echo.
if %errorlevel% equ 0 (
    echo ✅ Import script selesai
    pause
) else (
    echo ❌ Ada error saat menjalankan script
    pause
    exit /b 1
)
