@echo off
echo =============================================================
echo   STARTING FREE CLOUDFLARE TUNNEL (LARAVEL SPINHARISMA)
echo =============================================================
echo.
echo 1. Pastikan server Laravel anda sudah berjalan (php artisan serve --port=8000)
echo 2. Cloudflare akan membuatkan URL HTTPS publik secara otomatis di bawah:
echo.
.\cloudflared.exe tunnel --url http://localhost:8000
pause
