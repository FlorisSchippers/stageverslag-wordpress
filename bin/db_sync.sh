#!/bin/bash

scriptdir=`dirname $BASH_SOURCE`

# Include global functions
source $scriptdir/functions.sh

# Check for required env vars
check_env_variables APP_ROOT REMOTE_SSH_USER REMOTE_SSH_HOST REMOTE_PROJECT_PATH

cd $APP_ROOT/webroot

debug "Importing the production database"
wp db export \
    --allow-root \
    --ssh=$REMOTE_SSH_USER@$REMOTE_SSH_HOST:22$REMOTE_PROJECT_PATH/webroot \
    - | \
    wp db import --allow-root -
