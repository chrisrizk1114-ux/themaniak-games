@echo off
setlocal
echo ============================================
echo  XAMPP MySQL Repair (Game Adventure)
echo ============================================
echo.
echo Fixes "MySQL shutdown unexpectedly" caused by
echo crashed privilege tables or corrupt InnoDB data.
echo.

set XAMPP=C:\xamppp1
if not exist "%XAMPP%\mysql\bin\aria_chk.exe" set XAMPP=C:\xampp
if not exist "%XAMPP%\mysql\bin\aria_chk.exe" (
    echo XAMPP not found at C:\xamppp1 or C:\xampp
    pause
    exit /b 1
)

echo [1/4] Stopping MySQL...
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 2 /nobreak >nul

echo [2/4] Repairing system tables...
cd /d "%XAMPP%\mysql\data\mysql"
"%XAMPP%\mysql\bin\aria_chk.exe" -r db
"%XAMPP%\mysql\bin\aria_chk.exe" -r procs_priv
"%XAMPP%\mysql\bin\aria_chk.exe" -r tables_priv
"%XAMPP%\mysql\bin\aria_chk.exe" -r plugin
"%XAMPP%\mysql\bin\aria_chk.exe" -r servers
"%XAMPP%\mysql\bin\aria_chk.exe" -s proxies_priv >nul 2>&1
if errorlevel 1 (
    echo Restoring proxies_priv from backup...
    copy /Y "%XAMPP%\mysql\backup\mysql\proxies_priv.MAD" "%XAMPP%\mysql\data\mysql\proxies_priv.MAD"
    copy /Y "%XAMPP%\mysql\backup\mysql\proxies_priv.MAI" "%XAMPP%\mysql\data\mysql\proxies_priv.MAI"
)

if exist "%XAMPP%\mysql\data\ib_buffer_pool" (
    echo Resetting buffer pool cache...
    del /F /Q "%XAMPP%\mysql\data\ib_buffer_pool" >nul 2>&1
)

echo [3/4] Starting MySQL...
start "XAMPP MySQL" /MIN cmd /c "%XAMPP%\mysql_start.bat"
timeout /t 10 /nobreak >nul

echo [4/4] Testing connection...
"%XAMPP%\mysql\bin\mysql.exe" -u root -e "SELECT VERSION();" >nul 2>&1
if %errorlevel% equ 0 (
    echo.
    echo SUCCESS - MySQL is running on port 3306.
    echo Open phpMyAdmin or run database\setup-xampp.bat
    "%XAMPP%\mysql\bin\mysql.exe" -u root -e "DROP TABLE IF EXISTS rentacar_db.customer;" >nul 2>&1
) else (
    echo.
    echo FAILED - Check log: %XAMPP%\mysql\data\mysql_error.log
    echo Run XAMPP Control Panel as Administrator if needed.
)

echo.
pause
