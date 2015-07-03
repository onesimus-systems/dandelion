#!/bin/bash
# This script will build a production release of dandelion

# Directory Variables
TMP_DIR="/tmp"
GIT_DIR="dandelion"
FULL_DIR=$TMP_DIR/$GIT_DIR
DELIVERY_DIR="$HOME/Desktop"

# Git Variables
GIT_REPO="https://github.com/onesimus-systems/dandelion"
GIT_BRANCH="master"

echo "Cleaning up"
cd $TMP_DIR
rm -rf $GIT_DIR

echo "Cloning git repo"
git clone $GIT_REPO
cd $GIT_DIR

echo "Checking out master"
git checkout $GIT_BRANCH

echo "Installing Composer"
composer install --no-dev

echo "Optimizing Autoloader"
composer dump-autoload --optimize --no-dev

echo "Installing Node Modules"
npm install >> /dev/null

echo "Running Gulp"
$TMP_DIR/dandelion/node_modules/.bin/gulp

echo "Removing dev directories"
rm -rf $FULL_DIR/.git
rm -rf $FULL_DIR/node_modules
rm -rf $FULL_DIR/public/source
rm -rf $FULL_DIR/public/build/js/maps

rm -rf $FULL_DIR/public/assets/themes/modern/less
rm -rf $FULL_DIR/public/assets/themes/modern/maps
rm -rf $FULL_DIR/public/assets/themes/legacy/less
rm -rf $FULL_DIR/public/assets/themes/legacy/maps

echo "Creating tarball"
cd $TMP_DIR
tar czf $DELIVERY_DIR/dandelion.tar.gz $GIT_DIR/

echo "Cleaning up"
cd $TMP_DIR
rm -rf $GIT_DIR

echo "Finished"
