#!/bin/bash

# Fehlerbehandlung aktivieren
set -e
trap 'echo "Ein Fehler ist aufgetreten bei $BASH_COMMAND"' ERR

# Logging-Funktion
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Backup der ursprünglichen Konfigurationsdateien
backup_dir="/home/pi/config_backup_$(date +'%Y%m%d_%H%M%S')"
log "Erstelle Backup-Verzeichnis: $backup_dir"
mkdir -p "$backup_dir"

# System aktualisieren
log "System wird aktualisiert..."
apt update && apt upgrade -y

# Installiere Apache mit minimaler Konfiguration
log "Installation von Apache Web Server..."
apt install -y apache2
# Optimiere Apache für Raspberry Pi
cat > /etc/apache2/mods-available/mpm_prefork.conf <<EOF
<IfModule mpm_prefork_module>
    StartServers            2
    MinSpareServers         2
    MaxSpareServers         4
    MaxRequestWorkers      25
    MaxConnectionsPerChild  100
</IfModule>
EOF

# Installiere MariaDB mit optimierter Konfiguration
log "Installation von MariaDB..."
apt install -y mariadb-server
# Optimiere MariaDB für Raspberry Pi
cat > /etc/mysql/mariadb.conf.d/50-rpi-settings.cnf <<EOF
[mysqld]
key_buffer_size = 16M
max_connections = 40
innodb_buffer_pool_size = 64M
innodb_log_buffer_size = 8M
query_cache_size = 8M
max_heap_table_size = 16M
tmp_table_size = 16M
EOF

# Setze MariaDB Root Passwort
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 12)
log "Generiertes MariaDB Root Passwort: $MYSQL_ROOT_PASSWORD"
echo $MYSQL_ROOT_PASSWORD > "/home/pi/mariadb_root_password.txt"
chmod 600 "/home/pi/mariadb_root_password.txt"

mysql -e "
    ALTER USER 'root'@'localhost' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}';
    DELETE FROM mysql.user WHERE User='';
    DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
    DROP DATABASE IF EXISTS test;
    DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
    FLUSH PRIVILEGES;
"

# Installiere PHP mit minimalen Modulen
log "Installation von PHP..."
apt install -y php php-mysql php-common php-cli php-json
# PHP für Raspberry Pi optimieren
cat > /etc/php/*/php.ini <<EOF
memory_limit = 128M
upload_max_filesize = 16M
post_max_size = 16M
max_execution_time = 30
max_input_time = 30
EOF

# Firewall einrichten
log "Konfiguriere Firewall..."
apt install -y ufw
ufw default deny incoming
ufw default allow outgoing
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS
ufw allow 22/tcp    # SSH
ufw --force enable

# Erstelle einfache Info-Seite
cat > /var/www/html/info.php <<EOF
<?php
echo '<h1>Raspberry Pi LAMP Server</h1>';
echo '<h2>System Information:</h2>';
echo 'PHP Version: ' . phpversion() . '<br>';
echo 'Server Software: ' . \$_SERVER['SERVER_SOFTWARE'] . '<br>';
\$db = new mysqli('localhost', 'root', '${MYSQL_ROOT_PASSWORD}');
echo 'MariaDB Version: ' . \$db->server_info . '<br>';
echo '<h2>Memory Usage:</h2>';
echo 'Memory: ' . shell_exec('free -h');
?>
EOF

# Setze Berechtigungen
log "Setze Berechtigungen..."
chown -R www-data:www-data /var/www/html
find /var/www/html -type f -exec chmod 644 {} \;
find /var/www/html -type d -exec chmod 755 {} \;

# Monitoring-Tool für Ressourcen
apt install -y htop

# Erstelle Wartungsskript
cat > /usr/local/bin/lamp-maintenance <<EOF
#!/bin/bash
echo "Cleaning up temporary files..."
find /tmp -type f -mtime +7 -delete
echo "Optimizing MariaDB tables..."
mysqlcheck -o --all-databases -u root -p${MYSQL_ROOT_PASSWORD}
echo "Checking Apache status..."
systemctl status apache2
echo "Memory usage:"
free -h
echo "Disk usage:"
df -h
EOF
chmod +x /usr/local/bin/lamp-maintenance

# Backup-Skript erstellen
log "Erstelle Backup-Skript..."
cat > /usr/local/bin/create-system-backup <<EOF
#!/bin/bash

# Backup-Verzeichnis erstellen
BACKUP_DIR="/home/pi/backups/\$(date +%Y%m%d_%H%M%S)"
mkdir -p \$BACKUP_DIR

# Datenbank-Dump erstellen
echo "Erstelle MariaDB Backup..."
mysqldump --all-databases -u root -p\$(cat /home/pi/mariadb_root_password.txt) > \$BACKUP_DIR/all_databases.sql

# Apache Konfiguration sichern
echo "Sichere Apache Konfiguration..."
cp -r /etc/apache2 \$BACKUP_DIR/apache2_conf
cp -r /var/www \$BACKUP_DIR/www_backup

# PHP Konfiguration sichern
echo "Sichere PHP Konfiguration..."
cp -r /etc/php \$BACKUP_DIR/php_conf

# MariaDB Konfiguration sichern
echo "Sichere MariaDB Konfiguration..."
cp -r /etc/mysql \$BACKUP_DIR/mysql_conf

# Systemkonfiguration sichern
echo "Sichere Systemkonfiguration..."
cp /etc/hosts \$BACKUP_DIR/
cp /etc/hostname \$BACKUP_DIR/
cp /etc/fstab \$BACKUP_DIR/

# Backup komprimieren
echo "Komprimiere Backup..."
cd \$(dirname \$BACKUP_DIR)
tar -czf \$BACKUP_DIR.tar.gz \$(basename \$BACKUP_DIR)
rm -rf \$BACKUP_DIR

echo "Backup wurde erstellt: \$BACKUP_DIR.tar.gz"

# Alte Backups aufräumen (behält die letzten 5)
cd /home/pi/backups
ls -t *.tar.gz | tail -n +6 | xargs -r rm

echo "Backup abgeschlossen!"
EOF

# Wiederherstellungs-Skript erstellen
log "Erstelle Wiederherstellungs-Skript..."
cat > /usr/local/bin/restore-system-backup <<EOF
#!/bin/bash

if [ "\$#" -ne 1 ]; then
    echo "Verwendung: \$0 /pfad/zum/backup.tar.gz"
    exit 1
fi

BACKUP_FILE=\$1
RESTORE_DIR="/tmp/restore_\$(date +%s)"

if [ ! -f "\$BACKUP_FILE" ]; then
    echo "Backup-Datei nicht gefunden!"
    exit 1
fi

echo "Stelle Backup wieder her..."

# Backup entpacken
mkdir -p \$RESTORE_DIR
tar -xzf \$BACKUP_FILE -C \$RESTORE_DIR

BACKUP_CONTENT=\$RESTORE_DIR/\$(ls \$RESTORE_DIR)

# Dienste stoppen
systemctl stop apache2 mariadb

# Konfigurationen wiederherstellen
echo "Stelle Konfigurationen wieder her..."
cp -r \$BACKUP_CONTENT/apache2_conf/* /etc/apache2/
cp -r \$BACKUP_CONTENT/www_backup/* /var/www/
cp -r \$BACKUP_CONTENT/php_conf/* /etc/php/
cp -r \$BACKUP_CONTENT/mysql_conf/* /etc/mysql/

# Systemkonfiguration wiederherstellen
cp \$BACKUP_CONTENT/hosts /etc/hosts
cp \$BACKUP_CONTENT/hostname /etc/hostname
cp \$BACKUP_CONTENT/fstab /etc/fstab

# Datenbanken wiederherstellen
echo "Stelle Datenbanken wieder her..."
mysql -u root -p\$(cat /home/pi/mariadb_root_password.txt) < \$BACKUP_CONTENT/all_databases.sql

# Berechtigungen korrigieren
chown -R www-data:www-data /var/www

# Dienste neustarten
systemctl start apache2 mariadb

# Aufräumen
rm -rf \$RESTORE_DIR

echo "Wiederherstellung abgeschlossen!"
echo "Bitte System neu starten mit: sudo reboot"
EOF

# Skripte ausführbar machen
chmod +x /usr/local/bin/create-system-backup
chmod +x /usr/local/bin/restore-system-backup

# Erstelle Backup-Verzeichnis
mkdir -p /home/pi/backups
chown pi:pi /home/pi/backups

# Abschluss und Info
log "Installation abgeschlossen!"
log "Wichtige Informationen wurden gespeichert in: $backup_dir"
log "MariaDB Root Passwort wurde gespeichert in: /home/pi/mariadb_root_password.txt"
log "Systemstatus überprüfen mit: http://[IP-ADRESSE]/info.php"
log "Wartung durchführen mit: sudo lamp-maintenance"
log "Backup erstellen mit: sudo create-system-backup"
log "Backup wiederherstellen mit: sudo restore-system-backup /pfad/zum/backup.tar.gz"

# Zeige Ressourcennutzung
echo "Aktuelle Ressourcennutzung:"
free -h
df -h
