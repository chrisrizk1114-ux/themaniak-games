@echo off
setlocal
cd /d "%~dp0.."
echo The Maniak - cloud database setup
echo.
php database\setup-cloud-db.php
pause
