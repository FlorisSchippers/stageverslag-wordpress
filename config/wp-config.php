<?php
/**
 * The base configuration for WordPress
 */
 /** @var string Directory containing all of the site's files */
 $root_dir = dirname(__DIR__);
 $domain = $_SERVER['HTTP_HOST'];

 /** @var string Document Root */
 $webroot_dir = $root_dir . '/webroot';

 /**
  * Expose global env() function from oscarotero/env
  */
 Env::init();

 /**
  * Use Dotenv to set required environment variables and load .env file in root
  */
 $dotenv = new Dotenv\Dotenv($root_dir);
 if (file_exists($root_dir . '/.env')) {
     $dotenv->load();
     $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL']);
 }

 if (
    filter_var(getenv('WP_MULTISITE'), FILTER_VALIDATE_BOOLEAN) &&
    getenv('WP_MULTISITE_DOMAIN_CURRENT_SITE') !== ''
) {
    $dotenv->required([
        'WP_MULTISITE_PATH_CURRENT_SITE',
        'WP_MULTISITE_SUBDOMAIN_INSTALL',
        'WP_MULTISITE_DOMAIN_CURRENT_SITE'
    ]);
}


// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', env('DB_NAME'));

/** MySQL database username */
define('DB_USER', env('DB_USER'));

/** MySQL database password */
define('DB_PASSWORD', env('DB_PASSWORD'));

/** MySQL hostname */
define('DB_HOST', env('DB_HOST'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
 define('AUTH_KEY', env('AUTH_KEY'));
 define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
 define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
 define('NONCE_KEY', env('NONCE_KEY'));
 define('AUTH_SALT', env('AUTH_SALT'));
 define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
 define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
 define('NONCE_SALT', env('NONCE_SALT'));

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = env('DB_PREFIX');

/**
 * Get environment specific config things
 **/
 define('WP_ENV', env('WP_ENV') ?: 'development');

 $env_config = __DIR__ . '/env/' . WP_ENV . '.php';

 if (file_exists($env_config)) {
     require_once $env_config;
 }

define('WP_SITEURL', env('WP_SITEURL'));
define('WP_HOME', env('WP_HOME'));

define ('WP_CONTENT_FOLDERNAME', 'assets');
define( 'WP_CONTENT_DIR', $webroot_dir . '/assets' );
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
define('WP_CONTENT_URL', WP_HOME . '/' . WP_CONTENT_FOLDERNAME);
define( 'WPMU_PLUGIN_DIR',WP_CONTENT_DIR. '/mu-plugins' );
define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL . '/mu-plugins' );

// Wordpress multisite
//
define('WP_ALLOW_MULTISITE', filter_var(getenv('WP_MULTISITE'), FILTER_VALIDATE_BOOLEAN));

if (defined('WP_ALLOW_MULTISITE') && getenv('WP_MULTISITE_DOMAIN_CURRENT_SITE') !== '') {
    /**
    * Cookie settings
    *
    * Resolving The WordPress Multisite Redirect Loop
    *
    * @link https://tommcfarlin.com/resolving-the-wordpress-multisite-redirect-loop/
    */
    if (filter_var(getenv('WP_MULTISITE_SUBDOMAIN_INSTALL'), FILTER_VALIDATE_BOOLEAN)) {
        define('COOKIE_DOMAIN', $domain);
        define('ADMIN_COOKIE_PATH', '/');
    }

    define('MULTISITE', filter_var(getenv('WP_MULTISITE'), FILTER_VALIDATE_BOOLEAN));
    define('SUBDOMAIN_INSTALL', filter_var( getenv('WP_MULTISITE_SUBDOMAIN_INSTALL'), FILTER_VALIDATE_BOOLEAN));
    define('DOMAIN_CURRENT_SITE', getenv('WP_MULTISITE_DOMAIN_CURRENT_SITE'));
    define('PATH_CURRENT_SITE', getenv('WP_MULTISITE_PATH_CURRENT_SITE'));
    define('SITE_ID_CURRENT_SITE', 1);
    define('BLOG_ID_CURRENT_SITE', 1);
}

/* That's all, stop editing! Happy blogging. */
