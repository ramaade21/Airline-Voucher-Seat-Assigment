@echo off
title Airline Voucher - Start
color 0A

echo ============================================
echo   Airline Voucher Seat Assignment - START
echo ============================================
echo.

if not exist "backend\vendor" (
    echo [ERROR] Folder backend\vendor tidak ditemukan.
    echo         Jalankan setup.bat terlebih dahulu.
    pause
    exit /b 1
)

if not exist "frontend\node_modules" (
    echo [ERROR] Folder frontend\node_modules tidak ditemukan.
    echo         Jalankan setup.bat terlebih dahulu.
    pause
    exit /b 1
)

echo [..] Membuka Backend (Laravel) di jendela baru  -> http://localhost:8000
start "Airline Voucher - BACKEND (Laravel)" cmd /k "cd backend && php artisan serve"

timeout /t 3 /nobreak >nul

echo [..] Membuka Frontend (React) di jendela baru    -> http://localhost:5173
start "Airline Voucher - FRONTEND (React)" cmd /k "cd frontend && npm run dev"

timeout /t 3 /nobreak >nul

echo.
echo [OK] Kedua server sedang berjalan di jendela terpisah.
echo      Backend : http://localhost:8000
echo      Frontend: http://localhost:5173
echo.
echo Membuka browser...
start "" "http://localhost:5173"

echo.
echo Tutup jendela CMD masing-masing (BACKEND / FRONTEND) untuk menghentikan server.
pause
