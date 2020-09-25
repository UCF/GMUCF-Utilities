<?php
/**
 * Handles admin related functions related to
 * the Email custom post type
 */
namespace GMUCF\Utils\Admin\Email;


/**
 * Defines markup for a "Send Test" button for Emails.
 *
 * @since 1.0.0
 * @author Jim Barnes
 * @param object $post WP_Post object
 * @return void
 */
function instant_send_button( $post ) {
	if ( $post->post_type === 'ucf-email' ) :
?>
<div class="misc-pub-section instant-send">
	<a style="margin-bottom: 12px;" class="preview button" href="#send-test" id="instant-send">Send Test</a>
</div>
<?php
	endif;
}

add_action( 'post_submitbox_misc_actions', __NAMESPACE__ . '\instant_send_button', 10, 1 );


/**
 * Defines inline javascript necessary for the
 * Send Test button to function.
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
	(function($) {
		$post_id = <?php echo $post->ID; ?>;

		var data = {
			post_id: $post_id,
			action: 'instant-send'
		};
		var $sendBtn = $('#instant-send');
		var $spinner = $('<img src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" alt="Processing..." style="margin-left: 6px; display: inline-block; vertical-align: sub;">');

		var onPostSuccess = function(response) {
			if ( response.success === true ) {
				var $markup = $(
					'<div class="updated notice notice-success is-dismissible">' +
						'<p>Email test sent.</p>' +
					'</div>'
				);
			} else {
				var $markup = $(
					'<div class="notice notice-error is-dismissible">' +
						'<p>There was a problem sending the email test.</p>' +
					'</div>'
				);
			}

			$markup.insertAfter('.wp-header-end');
		};

		$sendBtn.on('click', function() {
			$sendBtn.append($spinner);
			$.post(
				ajaxurl,
				data,
				onPostSuccess,
				'json'
			).always(function() {
				$spinner.remove();
			});
		});
	}(jQuery));
	</script>
<?php
}

add_action( 'admin_footer-post.php', __NAMESPACE__ . '\insert_instant_send_js', 10, 1 );
