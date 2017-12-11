#!/bin/bash

scriptdir=`dirname $BASH_SOURCE`

# Include global functions
source $scriptdir/../functions.sh

# Check for required env vars
check_env_variables USER HOST PROJECT_PATH ENV APP_ROOT

export RELEASE_FOLDER=$(date +"%Y%m%d_%H%M")
export DEPLOY_DIR=$PROJECT_PATH/releases/$RELEASE_FOLDER
export CURRENT_LINK=$PROJECT_PATH/current
export SHARED_DIR=$PROJECT_PATH/shared_content
export REMOTE_APP_ROOT=$DEPLOY_DIR
export WEBROOT=$REMOTE_APP_ROOT/webroot

# Create deep remote folder, as this cannot be done with rsync"
debug "Creating remote release dir $DEPLOY_DIR"
ssh $USER@$HOST mkdir -p $DEPLOY_DIR

debug "Rsync build code to remote folder"
rsync -avzl \
  --delete \
  --exclude=/.git* \
  --exclude=/src \
  --exclude=/build \
  $APP_ROOT/ \
  $USER@$HOST:$DEPLOY_DIR

# Execute deploy code on remote server and pass some variables
ssh $USER@$HOST "export PROJECT_PATH=$PROJECT_PATH && export ENV=$ENV && export DEPLOY_DIR=$DEPLOY_DIR && export CURRENT_LINK=$CURRENT_LINK && export RELEASE_FOLDER=$RELEASE_FOLDER && export SHARED_DIR=$SHARED_DIR && export APP_ROOT=$REMOTE_APP_ROOT && export WEBROOT=$WEBROOT && bash -s" < $scriptdir/wordpress/remote.sh
