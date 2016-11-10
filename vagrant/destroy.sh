#!/bin/bash
echo "Backing up database on VM"

mkdir -p vagrant/private/db_backups
BACKUPPATH="/vagrant/vagrant/private/db_backups/database-$(date +%Y%m%d-%H:%M:%S).sql"
CMD="mysqldump dandelion -u root -pa > $BACKUPPATH"

vagrant ssh -c "$CMD"

if [ $? -ne 0 ]; then
    echo "Failed to backup database"
fi
