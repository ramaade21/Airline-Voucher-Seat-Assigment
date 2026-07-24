@echo off
title Airline Voucher - Stop
color 0C

echo Menghentikan server di port 8000 (Laravel) dan 5173 (React)...
echo.

for /f "tokens=5" %%p in ('netstat -ano ^| findstr :8000 ^| findstr LISTENING') do (
    echo Menghentikan proses PID %%p pada port 8000...
    taskkill /F /PID %%p >nul 2>nul
)

for /f "tokens=5" %%p in ('netstat -ano ^| findstr :5173 ^| findstr LISTENING') do (
    echo Menghentikan proses PID %%p pada port 5173...
    taskkill /F /PID %%p >nul 2>nul
)

echo.
echo [OK] Selesai. Jika jendela CMD backend/frontend masih terbuka, tutup manual juga.
pause
