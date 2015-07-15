#!/usr/bin/env bash

## Install software
# Install python software tools, add key for mariadb repo
apt-get update
apt-get install -y python-software-properties
apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db

# Add repositories for PHP 5.6 and MariaDB 10.0
add-apt-repository -y ppa:ondrej/php5-5.6
add-apt-repository -y 'deb http://nyc2.mirrors.digitalocean.com/mariadb/repo/10.0/ubuntu trusty main'

# Set the root password for MariaDB install
export DEBIAN_FRONTEND=noninteractive
sudo debconf-set-selections <<< 'mariadb-server-10.0 mysql-server/root_password password a'
sudo debconf-set-selections <<< 'mariadb-server-10.0 mysql-server/root_password_again password a'

# Update apt-get, install Nginx, PHP, and MariaDB
apt-get update
apt-get install -y nginx php5-fpm php5-mysql mariadb-server npm nodejs-legacy git

## Setup configurations
# Link /var/www to dandelion
if ! [ -L /var/www ]; then
    rm -rf /var/www
    mkdir /var/www
    ln -fs /vagrant /var/www/dandelion
fi

# Copy Nginx config, set to listen on port 8081, restart Nginx
cp /vagrant/app/install/Nginx-sample-config.conf /etc/nginx/sites-enabled/default
# Set listening port
sed -i.bak "s/listen 80/listen 8081/" /etc/nginx/sites-enabled/default
rm /etc/nginx/sites-enabled/default.bak
# Turn off sendfile
sed -i.bak "s/sendfile on/sendfile off/" /etc/nginx/nginx.conf
rm /etc/nginx/nginx.conf.bak
service nginx restart

# Copy Dandelion configuration
if [ -e /vagrant/app/config/config.php ]; then
    # Create backup of old
    cp /vagrant/app/config/config.php /vagrant/app/config/config.php.bak
fi
cp /vagrant/vagrant/config.sample.php /vagrant/app/config/config.php

# Setup database
mysql -u root -p"a" -e "CREATE DATABASE dandelion;"
mysql -u root -p"a" dandelion < /vagrant/app/install/mysql_schema.sql

# Setup Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer
cd /vagrant
composer install

# Setup Node
npm install -g gulp
npm install
gulp
