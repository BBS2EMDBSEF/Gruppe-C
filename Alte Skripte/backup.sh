#!/bin/bash

# Backup-Konfiguration
BACKUP_DIR="/backup"
SCRIPT_PATH="${BACKUP_DIR}/backup_script.sh"
mkdir -p ${BACKUP_DIR}
echo "#!/bin/bash" > ${SCRIPT_PATH}
echo "tar -czvf ${BACKUP_DIR}/website_$(date +%Y%m%d).tar.gz /var/www/html" >> ${SCRIPT_PATH}
chmod +x ${SCRIPT_PATH}
(crontab -l 2>/dev/null; echo "0 2 * * * ${SCRIPT_PATH}") | crontab -

echo "Automatisches Backup-System eingerichtet."
#Wird wahrscheinlich nicht mehr benötigt (Test Pending)