#!/bin/bash

# Include global functions
source `dirname $BASH_SOURCE`/../functions.sh

# Check for required env vars
check_env_variables APP_ROOT WEBROOT

debug "Linking environment file"
cd $APP_ROOT
ln -sf .env-dev .env

# build frontend
/bin/bash $APP_ROOT/bin/build/webpack.sh
/bin/bash $APP_ROOT/bin/build/wordpress.sh
