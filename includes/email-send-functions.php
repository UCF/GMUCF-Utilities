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
function send_instant_preview( $args ) {
	$args = shortcode_atts(
		array(
			'to'            => array( 'webcom@ucf.edu' ),
			'subject'       => '**PREVIEW** Test Email **PREVIEW**',
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

	global $wp_query;

	$wp_the_query = $wp_query;

	$args = array(
		'p'         => $post_id,
		'post_type' => 'ucf-email'
	);

	$query = new WP_Query( $args );

	$wp_query = $query;

	ob_start();

	include_once UCF_EMAIL_EDITOR__PLUGIN_DIR . 'templates/blank/blank-template.php';

	// Reset wp_query
	$wp_query = $wp_the_query;

	return ob_get_clean();
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
	$recipients_raw = get_field( 'preview_recipients', $post_id );
	$recipients     = array_map( 'trim', explode( ',', $recipients_raw ) );

	if ( is_array( $recipients ) && count( $recipients ) > 0 ) {
		$args['to'] = $recipients;
	}

	// Get subject line and from details
	$subject       = get_field( 'subject_line' );
	$from_email    = get_field( 'from_email_address' );
	$from_friendly = get_field( 'from_friendly_name' );

	if ( $subject ) {
		$args['subject'] = "*** PREVIEW *** $subject *** PREVIEW ***";
	}

	if ( $from_email && $from_friendly ) {
		$args['from']          = $from_email;
		$args['from_friendly'] = $from_friendly;
	}

	$send = send_instant_preview( $args );

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
