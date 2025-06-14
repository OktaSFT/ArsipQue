#!/bin/bash
# Script backup database ArsipKu untuk Linux/Unix
# Jalankan script ini dengan cron setiap hari

# Konfigurasi database
DB_HOST="localhost"
DB_USER="root"
DB_PASS=""
DB_NAME="arsipku"

# Konfigurasi backup
BACKUP_DIR="/var/backups/arsipku"
DATE=$(date +"%Y-%m-%d")
TIME=$(date +"%H-%M-%S")
BACKUP_FILE="$BACKUP_DIR/arsipku_backup_${DATE}_${TIME}.sql"

# Buat folder backup jika belum ada
mkdir -p "$BACKUP_DIR"

# Jalankan mysqldump
if mysqldump -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" > "$BACKUP_FILE"; then
    echo "$(date): Backup berhasil: $BACKUP_FILE" >> "$BACKUP_DIR/backup_log.txt"
    
    # Hapus backup lama (lebih dari 30 hari)
    find "$BACKUP_DIR" -name "*.sql" -type f -mtime +30 -delete
    
    # Kompres backup
    gzip "$BACKUP_FILE"
    
    echo "Backup berhasil: ${BACKUP_FILE}.gz"
else
    echo "$(date): Backup gagal!" >> "$BACKUP_DIR/backup_log.txt"
    echo "Backup gagal!"
fi
