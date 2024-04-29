<?php

/**
 * Display Newsletter Entries Shortcode.
 * 
 */

namespace Rodrigo\ThePlugin\Frontend\Shortcodes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rodrigo\ThePlugin\Frontend\Shortcodes\ShortcodesHelpers;
use Rodrigo\ThePlugin\Services\DatabaseNewsletterFormService;

class DisplayNewsletterEntries {

	/**
	 * Shortocde slug.
	 * 
	 */
	const SHORTCODE_SLUG = 'theplugin_newsletter_form_entries';

	/**
	 * Ajax action.
	 * 
	 */
	const AJAX_ACTION = 'theplugin_newsletter_form_entries';

	/**
	 * Ajax nonce.
	 * 
	 */
	const AJAX_NONCE = 'theplugin_newsletter_form_entries_nonce';

	/**
	 * Constructor.
	 * 
	 */
	public function __construct() {
		add_shortcode( $this::SHORTCODE_SLUG, array( $this, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_' . $this::AJAX_ACTION, array( $this, 'ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_' . $this::AJAX_ACTION, array( $this, 'ajax_handler' ) );
	}

	/**
	 * Enqueue scripts.
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Enqueue scripts only if the shortcode is present in the post content.
		if ( ! ShortcodesHelpers::post_content_has_shortcode( $this::SHORTCODE_SLUG ) ) {
			return;
		}

		wp_enqueue_style( 'rtp-newsletter-form-entries', RT_THEPLUGIN_PLUGIN_URL . "assets/css/shortcodes/newsletter-form-entries{$min}.css", array( 'rtp-main' ), RT_THEPLUGIN_PLUGIN_VERSION );

		wp_enqueue_script( 'rtp-newsletter-form-entries', RT_THEPLUGIN_PLUGIN_URL . "assets/js/shortcodes/newsletter-form-entries{$min}.js", array( 'rtp-main' ), RT_THEPLUGIN_PLUGIN_VERSION, true );
		wp_localize_script( 'rtp-newsletter-form-entries', 'thepluginNewsletterFormEntriesData', array( 
			'slug' => $this::SHORTCODE_SLUG,
			'ajaxAction' => $this::AJAX_ACTION,
		) );
	}

	/**
	 * Ajax handler.
	 * 
	 * @return void
	 */
	public function ajax_handler() {

		// Check nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], $this::AJAX_NONCE ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid nonce.', 'rt-theplugin' ) ) );
		}

		$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

		if ( ! $email ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid email.', 'rt-theplugin' ) ) );
		}

		$entries_output = $this->get_entries_output( $email );
		if ( ! $entries_output ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Email not found.', 'rt-theplugin' ) ) );
		}

		wp_send_json_success( array( 'entries_output' => $entries_output ) );
	}

	/**
	 * Get entries output.
	 * 
	 * @param string $email
	 * @return string
	 */
	public function get_entries_output( $email = '' ) {
		$entries = ! $email ? DatabaseNewsletterFormService::get() : DatabaseNewsletterFormService::get_by_email( $email );
		$display_first_name = get_option( 'theplugin_newsletter_form_display_first_name', false );
		$display_last_name = get_option( 'theplugin_newsletter_form_display_last_name', false );

		if ( ! $entries ) {
			return;
		}

		ob_start();
		?>
		
		<table class="rtp-newsletter-entries__table">
			<thead>
				<tr>
					<th><?php echo esc_html__( 'Email', 'rt-theplugin' ); ?></th>
					<?php if ( $display_first_name ) : ?>
					<th><?php echo esc_html__( 'First name', 'rt-theplugin' ); ?></th>
					<?php endif; ?>
					<?php if ( $display_last_name ) : ?>
					<th><?php echo esc_html__( 'Last name', 'rt-theplugin' ); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $entries as $entry ) : ?>
				<tr>
					<td>
						<?php echo esc_html( $entry->email ); ?>
					</td>
					<?php if ( $display_first_name ) : ?>
					<td>
						<?php echo esc_html( $entry->first_name ); ?>
					</td>
					<?php endif; ?>
					<?php if ( $display_last_name ) : ?>
					<td>
						<?php echo esc_html( $entry->last_name ); ?>
					</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php
		return ob_get_clean();
	}

	/**
	 * Render shortcode.
	 * 
	 * @param array $atts
	 * @return string
	 */
	public function render( $atts ) {
		ob_start();
		$entries_output = $this->get_entries_output();

		?>
		<div class="rtp-newsletter-entries">
			<form class="rtp-newsletter-entries__form" action="" method="post">
				<?php wp_nonce_field( $this::AJAX_NONCE, 'nonce' ); ?>

				<div class="rtp-newsletter-entries__search-filter">
					<input class="rtp-newsletter-entries__input" name="email" type="text" required placeholder="<?php esc_attr_e( 'Search by email', 'rt-theplugin' ); ?>">
					<button class="rtp-newsletter-entries__form-submit" type="submit"><?php echo esc_html__( 'Search', 'rt-theplugin' ) ?></button>
				</div>
			</form>
			<?php if ( ! empty( $entries_output ) ) : ?>
				<div class="rtp-newsletter-entries__entries">
					<?php echo wp_kses_post( $entries_output ); ?>
				</div>
			<?php endif; ?>
			<div class="rtp-newsletter-entries__error"></div>
			<div class="rtp-newsletter-entries__loading">
				<p><?php echo esc_html__( 'Loading...', 'rt-theplugin' ); ?></p>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

}