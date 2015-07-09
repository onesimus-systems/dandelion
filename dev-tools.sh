#!/bin/bash
# This script will build a production release of dandelion

buildDandelion()
{
    # Directory Variables
    TMP_DIR="/tmp"
    GIT_DIR="dandelion"
    FULL_DIR=$TMP_DIR/$GIT_DIR
    DELIVERY_DIR="$HOME/Desktop"

    # Git Variables
    GIT_REPO="https://github.com/onesimus-systems/dandelion"
    GIT_BRANCH=$1
    GIT_BRANCH_FILENAME=${GIT_BRANCH#tags/}

    echo "Cleaning up"
    cd $TMP_DIR
    rm -rf $GIT_DIR

    echo "Cloning git repo"
    git clone $GIT_REPO
    cd $GIT_DIR

    echo "Checking out master"
    git checkout $GIT_BRANCH

    if test $? != 0
         then
             echo "Error checking out branch $GIT_BRANCH"
             exit 1
    fi

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
    tar czf $DELIVERY_DIR/dandelion-$GIT_BRANCH_FILENAME.tar.gz $GIT_DIR/

    echo "Cleaning up"
    cd $TMP_DIR
    rm -rf $GIT_DIR

    echo "Finished"
}

buildCommand ()
{
    BRANCH="master"

    args=`getopt v:t:b: $*`
    if test $? != 0
         then
             echo 'Usage: build -t tag'
             exit 1
    fi
    set -- $args
    for i
    do
      case "$i" in
            -b)
                shift
                BRANCH="$1"
                shift
                ;;
            -t)
                shift
                BRANCH="tags/$1"
                shift
                ;;
            -v)
                shift
                BRANCH="tags/v$1"
                shift
      esac
    done

    buildDandelion $BRANCH
}

### Main Script ###
case $1 in
    build)
        buildCommand $@
        ;;
    *)
        echo "Invalid command"
        exit 1
esac
