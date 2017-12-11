#!/bin/bash

# Include global functions
source `dirname $BASH_SOURCE`/../functions.sh

# Check for required env vars
check_env_variables APP_ROOT ENV

# Check if node has been installed
if which node > /dev/null
  then
    debug "Node is already installed, skipping..."
  else
    debug "Install Node and NPM"
    (
      curl --silent --location https://deb.nodesource.com/setup_6.x | bash -;
      apt-get install --yes nodejs;
    )
  fi

debug "Run Webpack (assuming npm install has already run)"
cd $APP_ROOT/build
npm install
npm rebuild node-sass
npm run build:$ENV
