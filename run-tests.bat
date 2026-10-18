@echo off
echo =================================================================
echo    MARABUNTA SWIMMING CLUB (MSC) - AUTOMATED TEST RUNNER        
echo =================================================================
echo Menjalankan script testing otomatis...
echo.

powershell -ExecutionPolicy Bypass -File "%~dp0run-tests.ps1" %*

pause
