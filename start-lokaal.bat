@echo off
REM ============================================
REM   SpinGuard - Lokale test-server starten
REM ============================================
REM   Dubbelklik dit bestand om de site lokaal te
REM   bekijken op http://localhost:8000
REM ============================================

setlocal
cd /d "%~dp0"

echo.
echo ====================================================
echo   SpinGuard - lokale test-server
echo ====================================================
echo.

REM === Zoek PHP ===
set "PHP_EXE="
where php >nul 2>nul && set "PHP_EXE=php"
if not defined PHP_EXE if exist "C:\php\php.exe" set "PHP_EXE=C:\php\php.exe"
if not defined PHP_EXE if exist "C:\xampp\php\php.exe" set "PHP_EXE=C:\xampp\php\php.exe"
if not defined PHP_EXE if exist "C:\laragon\bin\php\php.exe" set "PHP_EXE=C:\laragon\bin\php\php.exe"

if not defined PHP_EXE (
    echo [FOUT] PHP is niet gevonden op deze computer.
    echo.
    echo Installeer PHP via een van deze opties:
    echo   - XAMPP:    https://www.apachefriends.org/download.html
    echo   - PHP only: https://windows.php.net/download/
    echo                ^(uitpakken naar C:\php\^)
    echo.
    pause
    exit /b 1
)

echo PHP gevonden: %PHP_EXE%
echo.
echo Server start op http://localhost:8000
echo.
echo Druk Ctrl+C om te stoppen.
echo ====================================================
echo.

REM === Open browser ===
start "" "http://localhost:8000"

REM === Start ingebouwde PHP server ===
"%PHP_EXE%" -S localhost:8000 -t .

pause
