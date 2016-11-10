#!/bin/bash
# This script will build a production release of dandelion

buildDandelion()
{
    # Directory Variables
    TMP_DIR="/tmp"
    GIT_DIR="dandelion"
    FULL_DIR="$TMP_DIR/$GIT_DIR"
    DELIVERY_DIR="$2"

    # Git Variables
    GIT_REPO="https://github.com/onesimus-systems/dandelion"
    GIT_BRANCH=$1
    GIT_BRANCH_FILENAME=${GIT_BRANCH#tags/}

    echo "Cleaning up"
    cd $TMP_DIR
    rm -rf $GIT_DIR

    echo "Cloning git repo"
    git clone $GIT_REPO $FULL_DIR
    cd $FULL_DIR

    echo "Checking out $GIT_BRANCH"
    git checkout $GIT_BRANCH

    if [ $? -ne 0 ]; then
         echo "Error checking out branch $GIT_BRANCH"
         exit 1
    fi

    echo "Installing Composer"
    composer install --no-dev

    if [ $? -ne 0 ]; then
        exit 1
    fi

    echo "Optimizing Autoloader"
    composer dump-autoload --optimize --no-dev

    if [ $? -ne 0 ]; then
        exit 1
    fi

    echo "Installing Node Modules"
    npm install

    if [ $? -ne 0 ]; then
        exit 1
    fi

    echo "Running Gulp"
    $TMP_DIR/dandelion/node_modules/.bin/gulp

    if [ $? -ne 0 ]; then
        exit 1
    fi

    echo "Removing dev directories"
    DEV_ITEMS=(
        '.git'
        'node_modules'
        'vagrant'
        'public/source'
        'public/build/js/maps'
        'public/assets/themes/modern/less'
        'public/assets/themes/modern/maps'
        'public/assets/themes/legacy/less'
        'public/assets/themes/legacy/maps'
        'composer.*'
        'package.json'
        'gulpfile.js'
        'dev-tools.sh'
        'Vagrantfile'
        'server.php'
    )

    for DIR in "${DEV_ITEMS[@]}"; do
        echo "Deleting $FULL_DIR/$DIR"
        rm -rf $FULL_DIR/$DIR
    done

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
    MV_PATH="$HOME/Desktop"

    case $2 in
        help)
            echo "Usage: build -b -t -v -p"
            echo "-b Branch to checkout"
            echo "-t Tag to checkout"
            echo "-v Version to checkout"
            echo "  Defaults to master branch"
            echo "-p Path to save gzipped tarball"
            echo "  Defaults to desktop"
            exit 0
    esac

    args=`getopt v:t:b:p: $*`
    if test $? -ne 0; then
         echo 'Usage: build -t tag'
         exit 1
    fi
    set -- $args
    for i; do
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
                ;;
            -p)
                shift
                MV_PATH="$1"
                shift
      esac
    done

    buildDandelion $BRANCH $MV_PATH
}

bumpverCommand ()
{
    # Setup options
    COMMIT=false
    DRY_RUN=false

    # Setup version variables
    NEW_VERSION=""
    CURRENT_VERSION=""
    MAJOR=""
    MINOR=""
    PATCH=""
    BUMP=$2

    # Setup regexes
    VER_REGEX="^[[:blank:]]*const VERSION = '\([[:digit:]]\.[[:digit:]]\.[[:digit:]]\)"
    VER_SED_REGEX="\(VERSION[[:blank:]]=[[:blank:]]'\)[[:digit:]]\.[[:digit:]]\.[[:digit:]]"
    VER_JSON_SED_REGEX="\(\"version\": \"\)[[:digit:]]\.[[:digit:]]\.[[:digit:]]"

    # Setup file pathnams
    APPFILE="./app/Dandelion/Application.php"
    COMPOSERFILE="./composer.json"
    NPMFILE="./package.json"

    # Parse arguments
    args=`getopt cd $*`
    if test $? -ne 0; then
         echo 'Usage: bumpver [major|minor|patch] -c -d'
         exit 1
    fi
    set -- $args
    for i; do
      case "$i" in
            -c)
                shift
                COMMIT=true
                shift
                ;;
            -d)
                shift
                DRY_RUN=true
                shift
      esac
    done

    # Get current version from application file
    CURRENT_VERSION=$(grep "$VER_REGEX" $APPFILE)
    CURRENT_VERSION=$(expr "$CURRENT_VERSION" : "$VER_REGEX")
    # Explode to array
    IFS='.' read -a CURRENT <<< "$CURRENT_VERSION"
    MAJOR=${CURRENT[0]}
    MINOR=${CURRENT[1]}
    PATCH=${CURRENT[2]}

    # Bump version as requested
    case $BUMP in
        major)
            MAJOR=$((MAJOR+1))
            MINOR=0
            PATCH=0
            ;;
        minor)
            MINOR=$((MINOR+1))
            PATCH=0
            ;;
        patch)
            PATCH=$((PATCH+1))
            ;;
        help)
            echo "Usage: bumpver [major|minor|patch] -c -d"
            echo "-c Commit and tag in git"
            echo "-d Dry run, displays changed files, will not commit"
            exit 0
            ;;
        *)
            echo "Which version number should be bumped?"
            exit 1
    esac

    # Compose new version number
    NEW_VERSION="$MAJOR.$MINOR.$PATCH"
    echo "Old Version: $CURRENT_VERSION"
    echo "New Version: $NEW_VERSION"
    echo ''

    # Edit main application file
    if [ "$DRY_RUN" = true ]; then
        sed "s/$VER_SED_REGEX/\1$NEW_VERSION/" $APPFILE
    else
        cat $APPFILE > $APPFILE.bak
        sed "s/$VER_SED_REGEX/\1$NEW_VERSION/" $APPFILE.bak > $APPFILE
        rm $APPFILE.bak
    fi

    # Edit composer file
    if [ "$DRY_RUN" = true ]; then
        sed "s/$VER_JSON_SED_REGEX/\1$NEW_VERSION/" $COMPOSERFILE
    else
        cat $COMPOSERFILE > $COMPOSERFILE.bak
        sed "s/$VER_JSON_SED_REGEX/\1$NEW_VERSION/" $COMPOSERFILE.bak > $COMPOSERFILE
        rm $COMPOSERFILE.bak
    fi

    # Edit package.json
    if [ "$DRY_RUN" = true ]; then
        sed "s/$VER_JSON_SED_REGEX/\1$NEW_VERSION/" $NPMFILE
    else
        cat $NPMFILE > $NPMFILE.bak
        sed "s/$VER_JSON_SED_REGEX/\1$NEW_VERSION/" $NPMFILE.bak > $NPMFILE
        rm $NPMFILE.bak
    fi

    # Commit changes and tag commit
    if [ "$COMMIT" = true -a "$DRY_RUN" = false ]; then
        echo "Commiting"
        git commit -am "Bumped $BUMP version"
        git tag v$NEW_VERSION
    fi
}

printHelp ()
{
    echo "Dev tools script for Dandelion"
    echo "Copyright 2015 - Onesimus Systems"
    echo "MIT"
    echo ''
    echo "Available commands:"
    echo "  build: Generate a release tarball"
    echo "  bumpver: Bump the version"
    echo ''
    echo "Type: '[command] help' to get help for a specific command"
}

### Main Script ###
case $1 in
    build)
        buildCommand $@
        ;;
    bumpver)
        bumpverCommand $@
        ;;
    help)
        printHelp
        ;;
    *)
        echo "Invalid command"
        exit 1
esac
