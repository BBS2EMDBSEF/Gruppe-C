#!/bin/bash

# Überprüfe, ob das Skript als root ausgeführt wird
if [ "$EUID" -ne 0 ]; then 
    echo "Dieses Skript muss als root ausgeführt werden!"
    exit 1
fi

# Fehlerbehandlung aktivieren
set -e
trap 'echo "Ein Fehler ist aufgetreten bei $BASH_COMMAND"' ERR

# Logging-Funktion
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Backup der ursprünglichen Konfigurationsdateien
backup_dir="/root/config_backup_$(date +'%Y%m%d_%H%M%S')"
log "Erstelle Backup-Verzeichnis: $backup_dir"
mkdir -p "$backup_dir"
[ -f /etc/apache2/apache2.conf ] && cp /etc/apache2/apache2.conf "$backup_dir/"
[ -f /etc/ssh/sshd_config ] && cp /etc/ssh/sshd_config "$backup_dir/"

# Aktualisiere das System
log "System wird aktualisiert..."
apt update && apt upgrade -y

# Installiere Apache
log "Installation von Apache Web Server..."
apt install -y apache2
systemctl enable apache2

# Installiere MariaDB
log "Installation von MariaDB..."
apt install -y mariadb-server
systemctl enable mariadb

# Sichere MariaDB-Installation (automatisiert)
log "Konfiguriere MariaDB..."
mysql_secure_installation <<EOF

y
your_secure_password_here
your_secure_password_here
y
y
y
y
EOF

# Installiere PHP und Module
log "Installation von PHP und zugehörigen Modulen..."
apt install -y php php-mysql php-cli php-common php-json php-opcache php-readline \
    php-xml php-curl php-gd php-mbstring

# Installiere phpMyAdmin mit vorkonfigurierter Antwort
log "Installation von phpMyAdmin..."
debconf-set-selections <<< 'phpmyadmin phpmyadmin/dbconfig-install boolean true'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/app-password-confirm password your_secure_password_here'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/admin-pass password your_secure_password_here'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/app-pass password your_secure_password_here'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2'
apt install -y phpmyadmin

# Apache-Konfiguration
log "Konfiguriere Apache..."
a2enmod rewrite
echo "ServerTokens Prod" >> /etc/apache2/apache2.conf
echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# SSH-Härtung
log "Konfiguriere SSH..."
cp /etc/ssh/sshd_config /etc/ssh/sshd_config.bak
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin no/' /etc/ssh/sshd_config
sed -i 's/#Port 22/Port 2222/' /etc/ssh/sshd_config  # Ändere SSH-Port
systemctl restart ssh

# UFW-Firewall
log "Konfiguriere UFW Firewall..."
ufw default deny incoming
ufw default allow outgoing
ufw allow 2222/tcp  # Neuer SSH-Port
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS
ufw --force enable

# Erstelle einen einfachen Status-Check
cat > /var/www/html/status.php <<EOF
<?php
\$services = array(
    'Apache' => array('port' => 80),
    'MariaDB' => array('port' => 3306),
    'SSH' => array('port' => 2222)
);

foreach (\$services as \$service => \$config) {
    \$connection = @fsockopen('127.0.0.1', \$config['port'], \$errno, \$errstr, 1);
    echo \$service . ': ' . (\$connection ? 'Online' : 'Offline') . "<br/>";
    if (\$connection) fclose(\$connection);
}
?>
EOF

# Setze sichere Berechtigungen
log "Setze Berechtigungen..."
chown -R www-data:www-data /var/www/html
find /var/www/html -type f -exec chmod 644 {} \;
find /var/www/html -type d -exec chmod 755 {} \;

# Abschluss
log "Installation und Konfiguration abgeschlossen!"
log "Backup-Dateien wurden gespeichert in: $backup_dir"
log "Bitte ändern Sie alle Standardpasswörter!"

# Zeige Status der Dienste
systemctl status apache2 mariadb ssh | grep Active