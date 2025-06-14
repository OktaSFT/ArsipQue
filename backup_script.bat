@echo off

:: Konfigurasi
set DB_USER=admin_backup
set DB_PASS=admin123
set DB_NAME=arsipku
set BACKUP_DIR=C:\backup_arsip

:: Format nama file
set DATE=%date:~-4,4%-%date:~3,2%-%date:~0,2%
set FILE=%BACKUP_DIR%\%DB_NAME%_%DATE%.sql

:: Buat folder jika belum ada
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

:: Backup database
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" -u %DB_USER% -p%DB_PASS% %DB_NAME% > "%FILE%"

:: Cek berhasil atau gagal
if %errorlevel% equ 0 (
    echo Backup berhasil: %FILE%
) else (
    echo Backup gagal!
)
