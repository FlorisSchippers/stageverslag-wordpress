#!/bin/bash

# Include global functions
source `dirname $BASH_SOURCE`/../functions.sh

# Check for required env vars
check_env_variables APP_ROOT WEBROOT

debug "Installing composer"
cd $APP_ROOT
composer install --no-interaction
