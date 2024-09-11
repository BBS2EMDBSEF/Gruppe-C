#!/bin/bash

# Stelle das neueste Backup wieder her
BACKUP_DIR="/backup"
LATEST_BACKUP=$(ls -t ${BACKUP_DIR}/*.tar.gz | head -1)
if [ -f "$LATEST_BACKUP" ]; then
  tar -xzvf "$LATEST_BACKUP" -C /
  echo "Wiederherstellung von $LATEST_BACKUP erfolgreich."
else
  echo "Kein Backup gefunden."
fi
