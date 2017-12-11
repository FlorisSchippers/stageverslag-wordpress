<?php
/**
 * WordPress session managment.
 *
 * Standardizes WordPress session data and uses either database transients or in-memory caching
 * for storing user session information.
 *
 * @package WordPress
 * @subpackage Session
 * @since   3.7.0
 */

/**
 * Return the current cache expire setting.
 *
 * @return int
 */
function burst_wp_session_cache_expire() {
	$wp_session = Burst_WP_Session::get_instance();

	return $wp_session->cache_expiration();
}

/**
 * Alias of burst_wp_session_write_close()
 */
function burst_wp_session_commit() {
	burst_wp_session_write_close();
}

/**
 * Load a JSON-encoded string into the current session.
 *
 * @param string $data
 */
function burst_wp_session_decode( $data ) {
	$wp_session = Burst_WP_Session::get_instance();

	return $wp_session->json_in( $data );
}

/**
 * Encode the current session's data as a JSON string.
 *
 * @return string
 */
function burst_wp_session_encode() {
	$wp_session = Burst_WP_Session::get_instance();

	return $wp_session->json_out();
}

/**
 * Regenerate the session ID.
 *
 * @param bool $delete_old_session
 *
 * @return bool
 */
function burst_wp_session_regenerate_id( $delete_old_session = false ) {
	$wp_session = Burst_WP_Session::get_instance();

	$wp_session->regenerate_id( $delete_old_session );

	return true;
}

/**
 * Start new or resume existing session.
 *
 * Resumes an existing session based on a value sent by the _wp_session cookie.
 *
 * @return bool
 */
function burst_wp_session_start() {
	$wp_session = Burst_WP_Session::get_instance();
	do_action( 'burst_wp_session_start' );

	return $wp_session->session_started();
}
add_action( 'plugins_loaded', 'burst_wp_session_start' );

/**
 * Return the current session status.
 *
 * @return int
 */
function burst_wp_session_status() {
	$wp_session = Burst_WP_Session::get_instance();

	if ( $wp_session->session_started() ) {
		return PHP_SESSION_ACTIVE;
	}

	return PHP_SESSION_NONE;
}

/**
 * Unset all session variables.
 */
function burst_wp_session_unset() {
	$wp_session = Burst_WP_Session::get_instance();

	$wp_session->reset();
}

/**
 * Write session data and end session
 */
function burst_wp_session_write_close() {
	$wp_session = Burst_WP_Session::get_instance();

	$wp_session->write_data();
	do_action( 'burst_wp_session_commit' );
}
add_action( 'shutdown', 'burst_wp_session_write_close' );

/**
 * Clean up expired sessions by removing data and their expiration entries from
 * the WordPress options table.
 *
 * This method should never be called directly and should instead be triggered as part
 * of a scheduled task or cron job.
 */
function burst_wp_session_cleanup() {
	global $wpdb;

	if ( defined( 'WP_SETUP_CONFIG' ) ) {
		return;
	}

	if ( ! defined( 'WP_INSTALLING' ) ) {
		$expiration_keys = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '_burst_wp_session_expires_%'" );

		$now = time();
		$expired_sessions = array();

		foreach( $expiration_keys as $expiration ) {
			// If the session has expired
			if ( $now > intval( $expiration->option_value ) ) {
				// Get the session ID by parsing the option_name
				$session_id = substr( $expiration->option_name, 20 );

				$expired_sessions[] = $expiration->option_name;
				$expired_sessions[] = "_burst_wp_session_$session_id";
			}
		}

		// Delete all expired sessions in a single query
		if ( ! empty( $expired_sessions ) ) {
			$option_names = implode( "','", $expired_sessions );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name IN ('$option_names')" );
		}
	}

	// Allow other plugins to hook in to the garbage collection process.
	do_action( 'burst_wp_session_cleanup' );
}
add_action( 'burst_wp_session_garbage_collection', 'burst_wp_session_cleanup' );

/**
 * Register the garbage collector as a twice daily event.
 */
function burst_wp_session_register_garbage_collection() {
	if ( ! wp_next_scheduled( 'burst_wp_session_garbage_collection' ) ) {
		wp_schedule_event( time(), 'hourly', 'burst_wp_session_garbage_collection' );
	}
}
add_action( 'wp', 'burst_wp_session_register_garbage_collection' );
