#!/bin/bash

# Aktualisiere das System
echo "System wird aktualisiert..."
sudo apt update && sudo apt upgrade -y

# Installiere Apache
echo "Installation von Apache Web Server..."
sudo apt install -y apache2

# Installiere MariaDB
echo "Installation von MariaDB..."
sudo apt install -y mariadb-server

# Führe das sichere MariaDB Installations-Skript aus
echo "Konfiguriere MariaDB..."
sudo mysql_secure_installation

# Installiere PHP und phpMyAdmin
echo "Installation von PHP und phpMyAdmin..."
sudo apt install -y php php-mysql
sudo apt install -y phpmyadmin
sudo phpenmod mysqli

# Konfiguriere Apache für phpMyAdmin
echo "Konfiguriere Apache für phpMyAdmin..."
echo "Include /etc/phpmyadmin/apache.conf" | sudo tee -a /etc/apache2/apache2.conf

# Starte Apache neu
sudo systemctl restart apache2

# Richte SSH mit Schlüsselauthentifizierung ein
echo "Konfiguriere SSH..."
sudo sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
sudo systemctl restart ssh

# Installiere und konfiguriere die UFW-Firewall
echo "Installiere und konfiguriere UFW Firewall..."
sudo apt install -y ufw
sudo ufw enable
sudo ufw allow 22/tcp    # Erlaube SSH
sudo ufw allow 80/tcp    # Erlaube HTTP
sudo ufw allow 443/tcp   # Erlaube HTTPS

# Bestätigung
echo "Installation und Konfiguration abgeschlossen!"
