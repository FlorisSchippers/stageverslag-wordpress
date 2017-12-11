<?php
/** Production */
ini_set('display_errors', 0);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', false);
/** Disable all file modifications including updates and update notifications */
define('DISALLOW_FILE_MODS', true);

$_SERVER['HTTPS'] = 'on';
define('FORCE_SSL_ADMIN', true);

/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache
define('DONOTVERIFY_WP_LOADER', true);
