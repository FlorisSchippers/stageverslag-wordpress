<?php

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/* Require composer autoloader*/
require_once(dirname(__DIR__) . '/vendor/autoload.php');

/** Location of your WordPress configuration. */
require_once(dirname(__DIR__) . '/config/wp-config.php');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . '/wp-settings.php');


/** Multi Site **/
use Gwa\Wordpress\MultisiteResolverManager;
use Gwa\Wordpress\MultisiteDirectoryResolver;


if (
    filter_var(getenv('WP_MULTISITE'), FILTER_VALIDATE_BOOLEAN) &&
    class_exists('Gwa\Wordpress\MultisiteResolverManager') &&
    getenv('GW_WP_DIR')
) {
    if (getenv('WP_MULTISITE_SUBDOMAIN_INSTALL') === 'true') {
        $type = MultisiteResolverManager::TYPE_SUBDOMAIN;
    } else {
        $type = MultisiteResolverManager::TYPE_FOLDER;
    }
    (new MultisiteResolverManager(getenv('GW_WP_DIR'), $type))->init();
}
