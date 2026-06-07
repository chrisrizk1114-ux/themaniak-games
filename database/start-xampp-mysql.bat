@echo off
echo ============================================
echo  Game Adventure - Start XAMPP MySQL
echo ============================================
echo.
echo 1. Open XAMPP Control Panel
echo 2. Click START next to MySQL
echo 3. Wait until it shows green "Running"
echo 4. Then run: database\setup-xampp.bat
echo.
echo Trying to start MySQL automatically...
echo.

if exist "C:\xamppp1\mysql_start.bat" (
    start "XAMPP MySQL" cmd /k "C:\xamppp1\mysql_start.bat"
    timeout /t 8 /nobreak >nul
    "C:\xamppp1\mysql\bin\mysql.exe" -u root -e "SELECT 1;" >nul 2>&1
    if %errorlevel% equ 0 (
        echo MySQL is running.
        cd /d "%~dp0.."
        php database\setup-xampp.php
    ) else (
        echo.
        echo MySQL did not start. Use XAMPP Control Panel manually.
        echo If MySQL fails, keep DB_CONNECTION=sqlite in .env to play offline.
    )
) else if exist "C:\xampp\mysql_start.bat" (
    start "XAMPP MySQL" cmd /k "C:\xampp\mysql_start.bat"
) else (
    echo XAMPP not found. Start MySQL from your XAMPP Control Panel.
)

echo.
pause
