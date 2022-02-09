<?php
/**
 * Provides the functions necessary for instantly
 * sending Email post markup
 */
namespace GMUCF\Utils\Includes\EmailSend;


/**
 * Properly formats incoming arguments
 * to send an email from WordPress
 *
 * @author Jim Barnes
 * @since 1.0.0
 * @param array $args The argument array
 * @return bool True if the email was sent.
 */
function send_test( $args ) {
	$args = shortcode_atts(
		array(
			'to'            => array( 'webcom@ucf.edu' ),
			'subject'       => '**TEST** Test Email **TEST**',
			'from_friendly' => 'Good Morning UCF Admin',
			'from'          => 'webcom@ucf.edu',
			'body'          => 'Hello World',
		),
		$args
	);

	$headers = array();

	$from_friendly = $args['from_friendly'];
	$from_email    = $args['from'];

	$sender = "From: $from_friendly <$from_email>";

	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	$headers[] = $sender;

	return wp_mail(
		$args['to'],
		trim( $args['subject'] ),
		trim( $args['body'] ),
		$headers
	);
}


/**
 * Helper function for text/html issues
 *
 * @author Jim Barnes
 * @since 1.0.0
 * @param string $content_type
 * @return string
 */
function gmucf_content_type( $content_type ) {
	return 'text/html';
}


/**
 * Generates markup for an email based
 * on the Email post ID
 *
 * @author Jim Barnes
 * @since 1.0.0
 * @param int $post_id
 * @return string
 */
function generate_email_markup( $post_id ) {
	if ( ! defined( 'UCF_EMAIL_EDITOR__PLUGIN_DIR' ) ) return '';

	$permalink = get_permalink( $post_id );

	$args = array(
		'timeout' => 15
	);

	$response = wp_remote_get( $permalink, $args );

	if ( wp_remote_retrieve_response_code( $response ) >= 400 ) return null;

	$body = wp_remote_retrieve_body( $response );

	return $body;
}


/**
 * Sanitizes a test recipient's email address to ensure
 * errant spaces are removed around the address, and that
 * consistent lowercase letters are used.
 *
 * @author Jo Dickson
 * @since 1.0.2
 * @param string $recipient_email A test recipient's email address
 * @return string Sanitized recipient's email address
 */
function sanitize_recipient_email( $recipient_email ) {
	return strtolower( trim( $recipient_email ) );
}


/**
 * Sends an email instantly from an Email post
 *
 * @author Jim Barnes
 * @since 1.0.0
 * @param int $post_id Email post ID
 * @return bool Whether the email contents were sent successfully
 */
function instant_send( $post_id ) {
	$markup = generate_email_markup( $post_id );

	$args = array(
		'body' => $markup
	);

	// Get recipients
	$recipients          = array();
	$base_recipients_raw = get_option( 'email_preview_base_list' );
	$base_recipients     = explode( ',', $base_recipients_raw ) ?: array();
	$requester           = get_field( 'requester', $post_id );
	$test_recipients_raw = get_field( 'preview_recipients', $post_id );
	$test_recipients     = explode( ',', $test_recipients_raw ) ?: array();

	$recipients = array_merge( $base_recipients, $test_recipients );
	if ( $requester ) {
		$recipients[] = $requester;
	}
	$recipients = array_unique( array_filter( array_map( __NAMESPACE__ . '\sanitize_recipient_email', $recipients ) ) );

	if ( count( $recipients ) > 0 ) {
		$args['to'] = $recipients;
	}

	// Get subject line and from details
	$subject       = get_field( 'subject_line', $post_id );
	$from_email    = get_field( 'from_email_address', $post_id );
	$from_friendly = get_field( 'from_friendly_name', $post_id );

	if ( $subject ) {
		$args['subject'] = "**TEST** $subject **TEST**";
	}

	if ( $from_email && $from_friendly ) {
		$args['from']          = $from_email;
		$args['from_friendly'] = $from_friendly;
	}

	$send = send_test( $args );

	return $send;
}


/**
 * The ajax handler for Email instant sends.
 *
 * @author Jim Barnes
 * @since 1.0.0
 */
function instant_send_ajax() {
	$post_id = intval( $_POST['post_id'] );

	$send = instant_send( $post_id );

	$retval = array(
		'success' => $send
	);

	echo json_encode( $retval );

	wp_die();
}

add_action( 'wp_ajax_instant-send', __NAMESPACE__ . '\instant_send_ajax', 10 );
