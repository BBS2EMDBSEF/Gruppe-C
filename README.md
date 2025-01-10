# Gruppe-C

# Anleitung zur Installation und Nutzung des Skripts

Dieses Skript richtet einen vollständigen **LAMP-Stack** (Linux, Apache, MySQL, PHP) mit phpMyAdmin, Sicherheitskonfigurationen, Backups und einer vorbereiteten Datenbank ein. Diese Anleitung führt durch die Installation auf einem **frischen Debian- oder Ubuntu-System**.

---

## 🚀 **Installation**

### 1. **Repository klonen**
Klonen Sie das Repository mit `git`:
```bash
git clone https://github.com/BBS2EMDBSEF/Gruppe-C.git
```

### 2. **In das Verzeichnis wechseln**
Navigieren Sie in das geklonte Verzeichnis:
```bash
cd Gruppe-C
```

### 3. **Skript ausführen**
Führen Sie das Skript mit Root-Rechten aus:
```bash
sudo ./script.sh
```

---

## 📜 **Was wird eingerichtet?**

### **1. LAMP-Stack**
- **Apache2**: Webserver mit PHP-Unterstützung.
- **MariaDB**: MySQL-kompatible Datenbank mit Benutzer und vorbereiteter Datenbank `php_projekt`.
- **phpMyAdmin**: Datenbankverwaltung über eine Weboberfläche.

### **2. Website**
- Die Website wird im Verzeichnis `/var/www/html` installiert.
- Eine `.htaccess`-Datei wird für zusätzliche Sicherheit angelegt.

### **3. Sicherheitskonfiguration**
- **SSH-Hardening**:
  - Port: `22222`
  - Nur Schlüssel-basierte Authentifizierung.
- **Firewall**:
  - Nur HTTP (Port 80), HTTPS (Port 443) und SSH (Port 22222) erlaubt.
- **Backup-System**:
  - Tägliches Backup (03:00 Uhr) von Apache, MySQL, PHP und Website-Inhalten.
  - Skripte zum Backup und Wiederherstellen:
    - **Backup**: `backup-lamp`
    - **Restore**: `restore-lamp [backup.tar.gz]`

### **4. Monitoring**
Ein Monitoring-Skript prüft den Zustand des Systems (z. B. Dienste, Speicher) und protokolliert Ergebnisse alle 5 Minuten in `/var/log/system-status.log`.

---

## 🔑 **Wichtige Informationen**

- **SSH**
  - Port: `22222`
  - Privater Schlüssel: `/home/pi/.ssh/id_rsa``
- **Backups**:
  - Verzeichnis: `/root/backups/`
  - Tägliche Erstellung: 03:00 Uhr
- **Datenbank**:
  - Name: `php_projekt`
  - Beispieltabellen und -daten werden automatisch erstellt.

---

## 📂 **Weitere Schritte**
- **Website öffnen**: Geben Sie die Server-IP in Ihrem Browser ein (`http://<IP-Adresse>`).
- **phpMyAdmin aufrufen**: Navigieren Sie zu `http://<IP-Adresse>/phpmyadmin`.

Das System ist jetzt vollständig eingerichtet und betriebsbereit!

