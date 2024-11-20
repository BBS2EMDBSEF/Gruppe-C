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

# Füge diesen Teil nach der PHP-Installation und vor der Firewall-Konfiguration ein:

# Apache PHP Konfiguration
log "Konfiguriere Apache für PHP..."

# Aktiviere benötigte Apache-Module
a2enmod php
a2enmod rewrite
a2enmod headers
a2enmod ssl

# Apache Sicherheitseinstellungen
cat > /etc/apache2/conf-available/security.conf <<EOF
ServerTokens Prod
ServerSignature Off
TraceEnable Off
Header set X-Content-Type-Options nosniff
Header set X-Frame-Options SAMEORIGIN
Header set X-XSS-Protection "1; mode=block"
EOF

# Aktiviere Security-Konfiguration
a2enconf security

# Optimiere Apache für PHP
cat > /etc/apache2/mods-available/php.conf <<EOF
<FilesMatch ".+\.ph(ar|p|tml)$">
    SetHandler application/x-httpd-php
</FilesMatch>
<FilesMatch ".+\.phps$">
    SetHandler application/x-httpd-php-source
    Require all denied
</FilesMatch>
<FilesMatch "^\.ph(ar|p|ps|tml)$">
    Require all denied
</FilesMatch>
<DirectoryMatch "/var/www/.*">
    php_admin_value upload_tmp_dir /var/www/tmp
    php_admin_value session.save_path /var/www/sessions
    php_admin_value open_basedir "/var/www/:/tmp/:/usr/share/php/:/dev/urandom"
</DirectoryMatch>
EOF

# Erstelle notwendige Verzeichnisse
mkdir -p /var/www/tmp
mkdir -p /var/www/sessions
chown www-data:www-data /var/www/tmp
chown www-data:www-data /var/www/sessions
chmod 750 /var/www/tmp
chmod 750 /var/www/sessions

# Optimiere PHP für Apache
cat > /etc/php/*/apache2/php.ini <<EOF
[PHP]
engine = On
short_open_tag = Off
precision = 14
output_buffering = 4096
zlib.output_compression = Off
implicit_flush = Off
unserialize_callback_func =
serialize_precision = -1
disable_functions = system,exec,shell_exec,passthru,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source,pcntl_exec
disable_classes =
zend.enable_gc = On
expose_php = Off
max_execution_time = 30
max_input_time = 60
memory_limit = 128M
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
display_errors = Off
display_startup_errors = Off
log_errors = On
log_errors_max_len = 1024
ignore_repeated_errors = Off
ignore_repeated_source = Off
report_memleaks = On
html_errors = On
variables_order = "GPCS"
request_order = "GP"
register_argc_argv = Off
auto_globals_jit = On
post_max_size = 20M
auto_prepend_file =
auto_append_file =
default_mimetype = "text/html"
default_charset = "UTF-8"
doc_root =
user_dir =
enable_dl = Off
file_uploads = On
upload_max_filesize = 20M
max_file_uploads = 20
allow_url_fopen = On
allow_url_include = Off
default_socket_timeout = 60

[CLI Server]
cli_server.color = On

[Date]
date.timezone = Europe/Berlin

[Pdo_mysql]
pdo_mysql.default_socket=

[mail function]
SMTP = localhost
smtp_port = 25
mail.add_x_header = Off

[SQL]
sql.safe_mode = Off

[MySQL]
mysql.allow_local_infile = Off
mysql.allow_persistent = On
mysql.max_persistent = -1
mysql.max_links = -1
mysql.default_port =
mysql.default_socket =
mysql.default_host =
mysql.default_user =
mysql.default_password =
mysql.connect_timeout = 60
mysql.trace_mode = Off

[MySQLi]
mysqli.max_persistent = -1
mysqli.allow_persistent = On
mysqli.max_links = -1
mysqli.default_port = 3306
mysqli.default_socket =
mysqli.default_host =
mysqli.default_user =
mysqli.default_pw =
mysqli.reconnect = Off

[mysqlnd]
mysqlnd.collect_statistics = On
mysqlnd.collect_memory_statistics = Off

[Session]
session.save_handler = files
session.use_strict_mode = 1
session.use_cookies = 1
session.use_only_cookies = 1
session.name = PHPSESSID
session.auto_start = 0
session.cookie_lifetime = 0
session.cookie_path = /
session.cookie_domain =
session.cookie_httponly = 1
session.cookie_samesite = Strict
session.serialize_handler = php
session.gc_probability = 1
session.gc_divisor = 1000
session.gc_maxlifetime = 1440
session.referer_check =
session.cache_limiter = nocache
session.cache_expire = 180
session.use_trans_sid = 0
session.sid_length = 32
session.sid_bits_per_character = 5

[Assertion]
zend.assertions = -1

[OPcache]
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
opcache.enable_file_override=0
opcache.validate_timestamps=1
EOF

# Apache Virtual Host Konfiguration
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined

    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # PHP-FPM Konfiguration
    <FilesMatch ".+\.ph(ar|p|tml)$">
        SetHandler "proxy:unix:/run/php/php-fpm.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
EOF

# Aktiviere die Standard-Site
a2ensite 000-default

# Erstelle .htaccess für zusätzliche Sicherheit
cat > /var/www/html/.htaccess <<EOF
# Grundlegende Sicherheitseinstellungen
Options -Indexes
ServerSignature Off

# PHP-Fehler nicht anzeigen
php_flag display_errors off

# Verzeichnislisting deaktivieren
IndexIgnore *

# Zugriff auf versteckte Dateien und Verzeichnisse verbieten
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Schutz sensibler Dateien
<FilesMatch "^(wp-config\.php|php\.ini|\.htaccess|\.git)">
    Order allow,deny
    Deny from all
</FilesMatch>

# MIME-Type Sicherheit
<IfModule mod_mime.c>
    AddType application/javascript .js
    AddType text/css .css
    AddType text/html .html .htm
    AddType image/gif .gif
    AddType image/jpeg .jpg .jpeg
    AddType image/png .png
</IfModule>

# Aktiviere RewriteEngine
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Umleitung von HTTP auf HTTPS (wenn SSL konfiguriert ist)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Blockiere Zugriff auf versteckte Dateien und Verzeichnisse
    RewriteCond %{SCRIPT_FILENAME} -d [OR]
    RewriteCond %{SCRIPT_FILENAME} -f
    RewriteRule "(^|/)\." - [F]
</IfModule>

# Grundlegende XSS-Schutzmaßnahmen
<IfModule mod_headers.c>
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Content-Type-Options nosniff
    Header set X-Frame-Options SAMEORIGIN
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Permissions-Policy "geolocation=(), midi=(), camera=(), usb=(), magnetometer=(), accelerometer=(), gyroscope=(), payment=()"
</IfModule>
EOF

# Setze Berechtigungen für .htaccess
chown www-data:www-data /var/www/html/.htaccess
chmod 644 /var/www/html/.htaccess

# Erstelle PHP-Info-Seite für Tests
cat > /var/www/html/phpinfo.php <<EOF
<?php
// Nur von localhost zugreifbar
if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1') {
    phpinfo();
} else {
    header("HTTP/1.1 403 Forbidden");
    echo "Zugriff verweigert";
}
EOF

# Neustart der Dienste
log "Starte Apache und PHP neu..."
systemctl restart apache2

# Teste die Apache-Konfiguration
apache2ctl -t

# Überprüfe PHP-Konfiguration
php -v

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
