<?php
/**
 * Handles admin related functions related to
 * the Email custom post type
 */
namespace GMUCF\Utils\Admin\Email;


/**
 * Defines markup for a "Send Preview" button for Emails.
 *
 * @since 1.0.0
 * @author Jim Barnes
 * @return void
 */
function instant_send_button( $post ) {
	if ( $post->post_type === 'ucf-email' ) :
?>
<div class="misc-pub-section instant-send">
	<a style="margin-bottom: 12px;" class="preview button" href="#send-preview" id="instant-send">Send Preview</a>
</div>
<?php
	endif;
}

add_action( 'post_submitbox_misc_actions', __NAMESPACE__ . '\instant_send_button', 10, 1 );


/**
 * Defines inline javascript necessary for the
 * Send Preview button to function.
 *
 * @since 1.0.0
 * @author Jim Barnes
 * @return void
 */
function insert_instant_send_js() {
	global $post;

	if ( $post->post_type !== 'ucf-email' ) return;
?>
	<script>
		$post_id = <?php echo $post->ID; ?>;

		var data = {
			post_id: $post_id,
			action: 'instant-send'
		};

		var onPostSuccess = function(response) {
			if ( response.success === true ) {
				var $markup = jQuery(
					'<div class="updated notice notice-success is-dismissible">' +
						'<p>Preview of email sent.</p>' +
					'</div>'
				);
			} else {
				var $markup = jQuery(
					'<div class="notice notice-error is-dismissible">' +
						'<p>There was a problem sending the preview.</p>' +
					'</div>'
				);
			}

			$markup.insertAfter('.wp-header-end');
		};

		jQuery('#instant-send').on('click', function() {
			jQuery.post(
				ajaxurl,
				data,
				onPostSuccess,
				'json'
			);
		});
	</script>
<?php
}

add_action( 'admin_footer-post.php', __NAMESPACE__ . '\insert_instant_send_js', 10, 1 );
