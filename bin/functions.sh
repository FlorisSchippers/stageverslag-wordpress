#!/bin/bash

RESTORE='\033[0m'

RED='\033[00;31m'
GREEN='\033[00;32m'
YELLOW='\033[00;33m'
BLUE='\033[00;34m'
PURPLE='\033[00;35m'
CYAN='\033[00;36m'
LIGHTGRAY='\033[00;37m'

LRED='\033[01;31m'
LGREEN='\033[01;32m'
LYELLOW='\033[01;33m'
LBLUE='\033[01;34m'
LPURPLE='\033[01;35m'
LCYAN='\033[01;36m'
WHITE='\033[01;37m'

function debug() {
    echo -e "${YELLOW}[DEBUG]${RESTORE} $1"
    echo -en "${RESTORE}"
}
function error() {
    echo -e "${RED}[ERROR]${RESTORE} $1"
    echo -en "${RESTORE}"
}
function success() {
    echo -e "${GREEN}[SUCCESS]${RESTORE} $1"
    echo -en "${RESTORE}"
}

# Check if ENV vars are set and not empty
# Usage: $ check_env_variables APP_ROOT WEBROOT
function check_env_variables() {
  for var in "$@"; do
    if [ -z "${!var}" ]; then
      error "Required environment variable '$var' not set or empty"
      exit 1;
    fi
  done
}
