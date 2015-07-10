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

    case $2 in
        help)
            echo "Usage: build -b -t -v"
            echo "-b Branch to checkout"
            echo "-t Tag to checkout"
            echo "-v Version to checkout"
            echo "Defaults to master branch"
            exit 0
    esac

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
    if test $? != 0
         then
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

### Main Script ###
case $1 in
    build)
        buildCommand $@
        ;;
    bumpver)
        bumpverCommand $@
        ;;
    *)
        echo "Invalid command"
        exit 1
esac
