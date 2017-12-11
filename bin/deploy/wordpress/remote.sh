#!/bin/bash

cd $DEPLOY_DIR

# Fix permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# If this is the first deploy, the shared folder does not exists
# Create the shared dir and it's content
if [ ! -d "$SHARED_DIR" ]; then
    echo "Create shared folders and set permissions"
    mkdir -p $SHARED_DIR/webroot/assets/plugins
    mkdir -p $SHARED_DIR/webroot/assets/themes
    #mkdir -p $SHARED_DIR/webroot/assets/mu

    cd $SHARED_DIR
    chmod -R o+w webroot/assets
fi

rsync -az --delete $DEPLOY_DIR/webroot/assets/plugins/* $SHARED_DIR/webroot/assets/plugins
rsync -az --delete $DEPLOY_DIR/webroot/assets/themes/* $SHARED_DIR/webroot/assets/themes
#rsync -az --delete $DEPLOY_DIR/webroot/assets/mu/* $SHARED_DIR/webroot/assets/mu

rm -rf $DEPLOY_DIR/webroot/assets

echo "Create symlink to shared content folders"
ln -sf $SHARED_DIR/webroot/assets $DEPLOY_DIR/webroot/assets


# Connect the correct environment for db settings
echo ">>> Link environment file"
cd $APP_ROOT
ln -sf .env-$ENV .env
echo "Created db env symlink"

echo "CD to deploy dir"
cd $DEPLOY_DIR/webroot

echo "Change current symlink to put site live"
cd $PROJECT_PATH
rm -rf current
ln -sf releases/$RELEASE_FOLDER current

exit
