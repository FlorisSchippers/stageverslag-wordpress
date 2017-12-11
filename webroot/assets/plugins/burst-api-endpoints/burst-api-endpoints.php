<?php
/*
Plugin Name: Burst API Endpoints
Plugin URI: http://burst-digital.com/
Description: Creates custom endpoints for the REST API
Version: 0.1
Author: Burst
Author URI: http://burst-digital.com/
License: GPL2/Creative Commons
Text Domain: hero-sep
*/

add_action( 'rest_api_init', function () {

	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_pre_serve_request', function ( $value ) {
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
		header( 'Access-Control-Allow-Headers: X-Timestamp, X-Nonce, Content-Type, Origin' );

		return $value;
	} );

//	register_rest_route( 'survey/v1', '/answer/(?P<id>\d+)', array(
//		'methods'  => 'POST',
//		'callback' => 'answer',
//	) );
//
//	register_rest_route( 'survey/v1', 'export/email', array(
//		'methods'  => 'GET',
//		'callback' => 'export_email'
//	) );
//
//	register_rest_route( 'survey/v1', '/subscribe', array(
//		'methods'  => 'POST',
//		'callback' => 'subscribe',
//	) );
//
//	register_rest_route( 'survey/v1', '/subscribe', array(
//		'methods'  => 'GET',
//		'callback' => 'init_subscribe',
//	) );
//
//	add_filter( 'rest_prepare_questions', 'question', 20, 3 );
} );

/**
 * Export email addresses from newsletter to excel file.
 *
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response
 */
function export_email( WP_REST_Request $request ) {

	$params = $request->get_query_params();

	if ( ! isset( $params['password'] ) || $params['password'] !== '2VqhtykcMCRLDkBYpB2w7oLBHFwR' ) {
		return new WP_REST_Response( [ 'Error' => 'Invalid password' ], 403 );
	}

	$export = array();
	$args   = array(
		'post_type'   => 'emails',
		'post_status' => 'any',
	);
	$query  = new WP_Query( $args );
	$emails = $query->get_posts();

	foreach ( $emails as $email ) {
		$export[] = _decrypt( $email->post_content );
	}

	header( "Content-Type: application/xls" );
	header( "Content-Disposition: attachment; filename=export-survey.xls" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	echo "Emails" . "\r\n";
	echo implode( "\r\n", array_values( $export ) ) . "\r\n";
}

/**
 * Modify response on GET /questions to add a timestamp & nonce.
 *
 * @param \WP_REST_Response $response
 * @param $post
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response
 */

function question( WP_REST_Response $response, $post, WP_REST_Request $request ) {

	$timestamp                   = time();
	$response->data['timestamp'] = $timestamp;

	if ( isset( $response->data['acf'] ) ) {
		foreach ( $response->data['acf']['answers'] as &$answer ) {
			$answer['nonce'] = wp_create_nonce( $answer['answer'] . $timestamp );
		}
	}

	return $response;
}

/**
 * Returns an error response.
 *
 * @param $code
 * @param $message
 *
 * @return \WP_REST_Response
 */
function error_response( $code, $message ) {

	$error = [
		'error' => $message
	];

	return new WP_REST_Response( $error, $code );
}

/**
 * Custom endpoint for posting answers.
 * Verifies nonce & updates ACF fields.
 *
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response
 */
/*
 * OLD FUNCTION WITH DYNAMIC RESULTS, MAYBE USEFUL FOR LATER
function answer(WP_REST_Request $request) {

  $nonce = $request->get_header('X-Nonce');
  $timestamp = $request->get_header('X-Timestamp');

  $params = $request->get_json_params();
  $answer = $params['answer'];
  $qid = $params['id'];

  if (!wp_verify_nonce($nonce, $answer . $timestamp)) {
    return new WP_REST_Response("Invalid request", 403);
  }

  if (have_rows('answers', $qid)) {

    $return = [
      'results' => [],
      'fact' => '',
      'image' => '//localhost:3001/hero-intro.png'
    ];

    $fact = get_field('fact', $qid);
    $total = 0;
    $fact_amount = 0;

    while (have_rows('answers', $qid)) {

      the_row();

      $should_analyze = get_sub_field('fact');
      $row_answer = get_sub_field('answer');
      $amount = get_sub_field('amount');

      if ($row_answer === $answer) {

        $amount++;
        update_sub_field('amount', $amount, $qid);
      }

      if ($should_analyze) {
        $fact_amount = $amount;
      }
      $total += $amount;
      array_push($return['results'], [
        'amount' => (string) $amount,
        'answer' => $row_answer,
      ]);
    }

    $percentage = round($fact_amount / $total * 100);
    // TODO Percentages of all answers

    $return['fact'] = str_replace('%', $percentage . '%', $fact);
    return new WP_REST_Response($return, 200);
  }
  return new WP_REST_Response("No matching answer found", 404);
}*/

/**
 * Custom endpoint that returns a nonce & timestamp for the email subscription.
 *
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response
 */
function init_subscribe( WP_REST_Request $request ) {

	$timestamp = time();
	$nonce     = wp_create_nonce( $timestamp );

	$return = [
		'timestamp' => $timestamp,
		'nonce'     => $nonce
	];

	return new WP_REST_Response( $return );
}

/**
 * Custom endpoint for subscribing to the e-mail newsletter.
 * Verifies nonce & stores the email address.
 *
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response
 */
function subscribe( WP_REST_Request $request ) {

	$nonce     = $request->get_header( 'X-Nonce' );
	$timestamp = $request->get_header( 'X-Timestamp' );

	/*
	 * Gives errors on posting, inconsistent so removed it for now.
	if (!wp_verify_nonce($nonce, $timestamp)) {
	  return error_response(403, 'Invalid request');
	}*/

	$params = $request->get_json_params();
	$email  = $params['email'];

	if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		return error_response( 403, 'Invalid data' );
	}

	$email = _encrypt( $email );

	wp_insert_post( array(
		'post_type'    => 'emails',
		'post_title'   => $email,
		'post_content' => $email,
	) );

	return new WP_REST_Response( 'subscribed', 200 );
}

/**
 * Custom endpoint for posting answers.
 * Verifies nonce & updates ACF fields.
 *
 * @param \WP_REST_Request $request
 *
 * @return \WP_REST_Response
 */

function answer( WP_REST_Request $request ) {

	$nonce     = $request->get_header( 'X-Nonce' );
	$timestamp = $request->get_header( 'X-Timestamp' );

	$params = $request->get_json_params();
	$answer = $params['answer'];
	$qid    = $params['id'];

	/*
	 * Gives errors on posting, inconsistent so removed it for now.
	if (!wp_verify_nonce($nonce, $answer . $timestamp)) {
	  return error_response(403, 'Invalid request');
	}*/

	if ( have_rows( 'answers', $qid ) ) {

		$return = [
			'results' => [],
			'fact'    => '',
			'image'   => '//localhost:3001/hero-intro.png',
			'total'   => 0,
		];

		$return['fact'] = get_field( 'fact', $qid );
		$total          = 0;

		while ( have_rows( 'answers', $qid ) ) {

			the_row();

			$row_answer = get_sub_field( 'answer' );
			$amount     = get_sub_field( 'statistics' );

			if ( $row_answer === $answer ) {

				$amount ++;
				update_sub_field( 'amount', $amount, $qid );
			}

			$total += $amount;
			array_push( $return['results'], [
				'amount' => (string) $amount,
				'answer' => $row_answer,
			] );
		}

		$return['total'] = $total;

		return new WP_REST_Response( $return, 200 );
	}

	return error_response( 404, 'No matching answer found' );
}

function _encrypt( $string ) {

	$ivlen          = openssl_cipher_iv_length( $cipher = "AES-256-CBC" );
	$iv             = openssl_random_pseudo_bytes( $ivlen );
	$ciphertext_raw = openssl_encrypt( $string, $cipher, getenv( 'NONCE_KEY' ), $options = OPENSSL_RAW_DATA, $iv );
	$hmac           = hash_hmac( 'sha256', $ciphertext_raw, getenv( 'NONCE_KEY' ), $as_binary = true );

	return base64_encode( $iv . $hmac . $ciphertext_raw );
}

function _decrypt( $string ) {

	error_reporting( 0 );

	$c                  = base64_decode( $string );
	$ivlen              = openssl_cipher_iv_length( $cipher = "AES-256-CBC" );
	$iv                 = substr( $c, 0, $ivlen );
	$hmac               = substr( $c, $ivlen, $sha2len = 32 );
	$ciphertext_raw     = substr( $c, $ivlen + $sha2len );
	$original_plaintext = openssl_decrypt( $ciphertext_raw, $cipher, getenv( 'NONCE_KEY' ), $options = OPENSSL_RAW_DATA, $iv );
	$calcmac            = hash_hmac( 'sha256', $ciphertext_raw, getenv( 'NONCE_KEY' ), $as_binary = true );

	if ( hash_equals( $hmac, $calcmac ) ) {
		return $original_plaintext;
	}

	return false;
}