#!/bin/bash

# Logging-Funktion
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# SYSTEM UPDATE
log "Aktualisiere System..."
apt update && apt upgrade -y
apt autoremove -y
apt clean

# Apache und PHP Installation
apt install -y apache2 php php-mysql php-common php-cli php-json php-mbstring php-zip libapache2-mod-php

# Apache Konfiguration für die Website
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Konfiguration für PHP-Dateien
    <FilesMatch "\.php$">
        SetHandler application/x-httpd-php
    </FilesMatch>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# PHP Konfiguration
cat > /etc/php/*/apache2/php.ini <<EOF
[PHP]
engine = On
short_open_tag = Off
memory_limit = 128M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 30
max_input_time = 60
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
default_charset = "UTF-8"
date.timezone = Europe/Berlin
file_uploads = On

[Session]
session.save_handler = files
session.save_path = "/var/lib/php/sessions"
session.use_strict_mode = 1
session.use_cookies = 1
session.use_only_cookies = 1
session.name = PHPSESSID
session.cookie_httponly = 1
session.cookie_samesite = Strict
EOF

# .htaccess für zusätzliche Sicherheit
cat > /var/www/html/.htaccess <<EOF
# Verzeichnislisting deaktivieren
Options -Indexes

# PHP Fehler nicht anzeigen
php_flag display_errors off

# Zugriff auf .inc Dateien verbieten
<FilesMatch "\.inc$">
    Require all denied
</FilesMatch>

# Basis Sicherheitsheader
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
EOF

# Apache Module aktivieren
a2enmod rewrite
a2enmod headers

# Berechtigungen setzen
chown -R www-data:www-data /var/www
find /var/www/html -type f -exec chmod 644 {} \;
find /var/www/html -type d -exec chmod 755 {} \;
chmod 644 /var/www/html/.htaccess

# Verzeichnis für PHP-Sessions
mkdir -p /var/lib/php/sessions
chown www-data:www-data /var/lib/php/sessions
chmod 700 /var/lib/php/sessions

# PHP Error Log erstellen
touch /var/log/php_errors.log
chown www-data:www-data /var/log/php_errors.log
chmod 664 /var/log/php_errors.log

# Apache neustarten
systemctl restart apache2

# 2. & 3. MYSQL & PHPMYADMIN
log "Installation von MariaDB und phpMyAdmin..."
apt install -y mariadb-server phpmyadmin

# Lösche Standard Apache Seite
log "Konfiguriere Website..."
rm -f /var/www/html/index.html

# MariaDB Konfiguration mit festem Passwort
log "Konfiguriere MariaDB..."
mysql -e "
    ALTER USER 'root'@'localhost' IDENTIFIED BY 'c';
    CREATE USER 'pi'@'localhost' IDENTIFIED BY 'c';
    GRANT ALL PRIVILEGES ON *.* TO 'pi'@'localhost' WITH GRANT OPTION;
    FLUSH PRIVILEGES;"

# Erstelle und importiere Datenbank
log "Erstelle und konfiguriere Datenbank..."
mysql -u root -pc << "EOF"
-- Datenbank: `php_projekt`
CREATE DATABASE IF NOT EXISTS `php_projekt` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_general_ci;
USE `php_projekt`;

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `users`
CREATE TABLE `users` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `nachname` VARCHAR(255) NOT NULL,
  `vorname`  VARCHAR(255) NOT NULL,
  `email`    VARCHAR(255) NOT NULL,
  `passwort` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Beispiel-Datensätze
INSERT INTO `users` (`id`, `username`, `nachname`, `vorname`, `email`, `passwort`) VALUES
(75, 'borna', 'Borna', 'Ghazaleh', 'borna@borna.de', '$2y$10$mJ75vpei0M2ElJDZMwEOhu2LUu3Ng8MEHQPBqCXA5CRegaCnkeF0K'),
(76, 'admin', 'admin', 'admin', 'admin@admin.de', '$2y$10$0bKqPZ80Uokt8Y8bTjKroup6rQGYO6PBMi8RbqaOa7B6SEClO7T7.');

-- --------------------------------------------------------
-- Tabellenstruktur für Tabelle `files`
CREATE TABLE `files` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
EOF

# Kopiere Website-Dateien
cd /home/pi/Gruppe-C
cp -r inc js css img index.php /var/www/html/

# 4. SICHERHEIT

# a) Backup-System
log "Richte Backup-System ein..."
cat > /usr/local/bin/backup-lamp <<EOF
#!/bin/bash
BACKUP_DIR="/root/backups/\$(date +%Y%m%d)"
mkdir -p \$BACKUP_DIR

# MySQL Dump
mysqldump --all-databases -u root -pc > \$BACKUP_DIR/all_databases.sql

# Apache Config
cp -r /etc/apache2 \$BACKUP_DIR/
cp -r /etc/php \$BACKUP_DIR/
cp -r /etc/mysql \$BACKUP_DIR/
cp -r /var/www \$BACKUP_DIR/

# Logs Backup
cp -r /var/log/apache2 \$BACKUP_DIR/logs/
cp -r /var/log/mysql \$BACKUP_DIR/logs/

# Komprimiere alles
tar -czf \$BACKUP_DIR.tar.gz \$BACKUP_DIR
rm -rf \$BACKUP_DIR

# Alte Backups löschen (behalte letzte 7)
find /root/backups -name "*.tar.gz" -mtime +7 -delete
EOF

# Wiederherstellungs-Script
cat > /usr/local/bin/restore-lamp <<EOF
#!/bin/bash
if [ -z "\$1" ]; then
    echo "Verwendung: \$0 backup.tar.gz"
    exit 1
fi

RESTORE_DIR="/tmp/restore_\$(date +%s)"
mkdir -p \$RESTORE_DIR

tar -xzf "\$1" -C \$RESTORE_DIR
cd \$RESTORE_DIR/*

systemctl stop apache2 mysql

# Restore Configs
cp -r apache2/* /etc/apache2/
cp -r php/* /etc/php/
cp -r mysql/* /etc/mysql/
cp -r www/* /var/www/

# Restore Database
mysql -u root -pc < all_databases.sql

systemctl start apache2 mysql
rm -rf "\$RESTORE_DIR"
EOF

chmod +x /usr/local/bin/{backup-lamp,restore-lamp}

# Backup Cronjob
echo "0 3 * * * root /usr/local/bin/backup-lamp" > /etc/cron.d/lamp-backup

# b) SSH-Zugang
log "Konfiguriere SSH..."
apt install -y openssh-server

# SSH-Schlüssel generieren
mkdir -p /home/pi/.ssh
ssh-keygen -t rsa -b 4096 -f /home/pi/.ssh/id_rsa -N ""
cp /home/pi/.ssh/id_rsa.pub /home/pi/.ssh/authorized_keys
chown -R pi:pi /home/pi/.ssh
chmod 700 /home/pi/.ssh
chmod 600 /home/pi/.ssh/authorized_keys

# SSH hardening
cat > /etc/ssh/sshd_config <<EOF
Port 22222
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
AuthorizedKeysFile .ssh/authorized_keys
Protocol 2
EOF

# c) Firewall
log "Konfiguriere Firewall..."
apt install -y ufw

ufw default deny incoming
ufw default allow outgoing
ufw allow 80/tcp        # HTTP
ufw allow 443/tcp       # HTTPS
ufw allow 22222/tcp     # SSH
ufw --force enable

# d) Überwachung
log "Installiere Monitoring-Tools..."
apt install -y htop iftop net-tools

# Monitoring-Script
cat > /usr/local/bin/check-system <<EOF
#!/bin/bash
echo "=== System Status ==="
date
echo -e "\n=== Dienste Status ==="
systemctl status apache2 | grep Active
systemctl status mysql | grep Active
systemctl status ssh | grep Active
systemctl status php | grep Active
echo -e "\n=== Speicher ==="
free -h
echo -e "\n=== Festplatte ==="
df -h
echo -e "\n=== Firewall Status ==="
ufw status
echo -e "\n=== Aktive Verbindungen ==="
ss -tulpn
EOF

chmod +x /usr/local/bin/check-system

# Monitoring Cronjob
echo "*/5 * * * * root /usr/local/bin/check-system > /var/log/system-status.log" > /etc/cron.d/system-monitoring

log "Installation abgeschlossen!"
log "Wichtige Informationen:"
log "- SSH Port: 22222"
log "- SSH Private Key: /home/pi/.ssh/id_rsa"
log "- MariaDB/phpMyAdmin Zugangsdaten:"
log "  Benutzer: pi oder root"
log "  Passwort: c"
log "- Website wurde in /var/www/html installiert"
log "- Datenbank 'php_projekt' wurde erstellt (Spalten: id, username, nachname, vorname, email, passwort)"
log "- Backups: Täglich um 03:00 Uhr in /root/backups/"
log "- System-Check: Alle 5 Minuten in /var/log/system-status.log"
log "- Backup durchführen: backup-lamp"
log "- Backup wiederherstellen: restore-lamp [backup.tar.gz]"
log "- System-Status prüfen: check-system"

# Führe ersten System-Check durch
/usr/local/bin/check-system
