@echo off
setlocal enabledelayedexpansion
title Airline Voucher - Setup
color 0B

echo ============================================
echo   Airline Voucher Seat Assignment - SETUP
echo ============================================
echo.

REM --- Check PHP ---
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] PHP tidak ditemukan di PATH.
    echo         Buka project ini lewat Terminal Laragon, atau tambahkan PHP ke PATH.
    pause
    exit /b 1
)
for /f "tokens=2 delims= " %%v in ('php -v ^| findstr /b "PHP"') do set PHP_VER=%%v
echo [OK] PHP ditemukan: %PHP_VER%

REM --- Check Composer ---
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Composer tidak ditemukan di PATH.
    pause
    exit /b 1
)
echo [OK] Composer ditemukan.

REM --- Check Node/npm ---
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Node.js tidak ditemukan di PATH.
    echo         Install Node.js LTS dari https://nodejs.org atau via Laragon Quick Add.
    pause
    exit /b 1
)
where npm >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] npm tidak ditemukan di PATH.
    pause
    exit /b 1
)
echo [OK] Node.js dan npm ditemukan.
echo.

REM ============================================
REM  BACKEND SETUP (Laravel)
REM ============================================
echo ---- Setting up Backend (Laravel) ----
pushd backend

if not exist ".env" (
    copy /y .env.example .env >nul
    echo [OK] .env dibuat dari .env.example
) else (
    echo [SKIP] .env sudah ada, tidak ditimpa.
)

echo [..] Menjalankan composer install (ini bisa memakan waktu beberapa menit)...
call composer install --no-interaction
if %errorlevel% neq 0 (
    echo [ERROR] composer install gagal. Cek pesan error di atas.
    popd
    pause
    exit /b 1
)

echo [..] Generate application key...
call php artisan key:generate --ansi

if not exist "database\database.sqlite" (
    type nul > database\database.sqlite
    echo [OK] database\database.sqlite dibuat.
) else (
    echo [SKIP] database.sqlite sudah ada.
)

echo [..] Menjalankan migration...
call php artisan migrate --force
if %errorlevel% neq 0 (
    echo [ERROR] Migration gagal. Pastikan extension pdo_sqlite aktif di php.ini.
    popd
    pause
    exit /b 1
)

echo.
set /p SEED_CHOICE="Isi data contoh (seed)? (Y/N): "
if /i "%SEED_CHOICE%"=="Y" (
    call php artisan db:seed
    echo [OK] Data contoh berhasil ditambahkan.
)

popd
echo [DONE] Backend siap.
echo.

REM ============================================
REM  FRONTEND SETUP (React)
REM ============================================
echo ---- Setting up Frontend (React) ----
pushd frontend

if not exist ".env" (
    copy /y .env.example .env >nul
    echo [OK] .env dibuat dari .env.example
) else (
    echo [SKIP] .env sudah ada, tidak ditimpa.
)

echo [..] Menjalankan npm install (ini bisa memakan waktu beberapa menit)...
call npm install
if %errorlevel% neq 0 (
    echo [ERROR] npm install gagal. Cek pesan error di atas.
    popd
    pause
    exit /b 1
)

popd
echo [DONE] Frontend siap.
echo.

echo ============================================
echo   SETUP SELESAI!
echo   Jalankan start.bat untuk menyalakan aplikasi.
echo ============================================
pause
