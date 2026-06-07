@echo off
echo Game Adventure - XAMPP database setup
echo.

cd /d "%~dp0.."

php database\setup-xampp.php

pause
