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

# 1. WEBSERVER
log "Installation von Apache..."
apt install -y apache2

# 2. & 3. MYSQL & PHPMYADMIN
log "Installation von MariaDB und phpMyAdmin..."
apt install -y mariadb-server phpmyadmin

# MariaDB für Pi-User konfigurieren
mysql -e "
    CREATE USER IF NOT EXISTS 'pi'@'localhost' IDENTIFIED VIA unix_socket;
    GRANT ALL PRIVILEGES ON *.* TO 'pi'@'localhost' WITH GRANT OPTION;
    FLUSH PRIVILEGES;"

# 4. SICHERHEIT

# a) Backup-System
log "Richte Backup-System ein..."
cat > /usr/local/bin/backup-lamp <<EOF
#!/bin/bash
BACKUP_DIR="/root/backups/\$(date +%Y%m%d)"
mkdir -p \$BACKUP_DIR

# MySQL Dump
mysqldump --all-databases > \$BACKUP_DIR/all_databases.sql

# Apache Config
cp -r /etc/apache2 \$BACKUP_DIR/
cp -r /etc/php \$BACKUP_DIR/
cp -r /etc/mysql \$BACKUP_DIR/
cp -r /var/www \$BACKUP_DIR/

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
mysql < all_databases.sql

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
log "- Backups: Täglich um 03:00 Uhr in /root/backups/"
log "- System-Check: Alle 5 Minuten in /var/log/system-status.log"
log "- Backup durchführen: backup-lamp"
log "- Backup wiederherstellen: restore-lamp [backup.tar.gz]"
log "- System-Status prüfen: check-system"

# Führe ersten System-Check durch
/usr/local/bin/check-system