#!/usr/bin/env bash

## Install software
# Install python software tools, add key for mariadb repo
apt-get update
apt-get install -y python-software-properties
apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8

# Add repositories for PHP and MariaDB 10.1
add-apt-repository -y ppa:ondrej/php
add-apt-repository -y 'deb http://nyc2.mirrors.digitalocean.com/mariadb/repo/10.1/ubuntu xenial main'

# Set the root password for MariaDB install
export DEBIAN_FRONTEND=noninteractive
sudo debconf-set-selections <<< 'mariadb-server-10.1 mysql-server/root_password password a'
sudo debconf-set-selections <<< 'mariadb-server-10.1 mysql-server/root_password_again password a'

# Update apt-get, install Nginx, PHP, and MariaDB
apt-get update
apt-get install -y nginx php5.5-fpm php5.5-cli php5.5-mysql mariadb-server git unzip

## Setup configurations
# Link /var/www to dandelion
rm -rf /var/www
mkdir /var/www
ln -fs /vagrant /var/www/dandelion

# Copy Nginx config
cp /vagrant/app/config/Nginx-sample-config.conf /etc/nginx/sites-enabled/default
# Turn off sendfile
sed -i.bak "s/sendfile on/sendfile off/" /etc/nginx/nginx.conf
rm /etc/nginx/nginx.conf.bak
service nginx restart

# Set timezone in PHP
sed -i.bak "s/;date.timezone =/date.timezone = America\/Chicago/" /etc/php5/fpm/php.ini
rm /etc/php5/fpm/php.ini.bak
service php5-fpm restart

# Copy Dandelion configuration
if [ -e /vagrant/app/config/config.php ]; then
    # Create backup of old
    cp /vagrant/app/config/config.php /vagrant/app/config/config.php.bak
fi
cp /vagrant/vagrant/config.sample.php /vagrant/app/config/config.php

# Setup database
mysql -u root -p"a" -e "CREATE DATABASE dandelion;"
mysql -u root -p"a" dandelion < /vagrant/app/install/mysql_schema.sql # 6.0.x base
mysql -u root -p"a" dandelion < /vagrant/app/install/upgrades/db_upgrade_mysql_6.1.0.sql

# Setup Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer
cd /vagrant
composer install

# Setup Node
# Windows doesn't handle linux NPM correctly due to symlinks
# If Vagrant is running on Windows, the first arg "nonpm" will be set
if [ "$1" != "nonpm" ]; then
    apt-get install -y npm nodejs
    npm install -g gulp
    npm install
    gulp
else
    echo "Windows detected, not running 'npm install'"
fi
