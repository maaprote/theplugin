<?php

/**
 * Newsletter Form Shortcode.
 * 
 */

namespace Rodrigo\ThePlugin\Frontend\Shortcodes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rodrigo\ThePlugin\Frontend\Shortcodes\ShortcodesHelpers;
use Rodrigo\ThePlugin\Services\DatabaseNewsletterFormService;

class NewsletterForm {

	/**
	 * Shortocde slug.
	 * 
	 */
	const SHORTCODE_SLUG = 'theplugin_newsletter_form';

	/**
	 * Ajax action.
	 * 
	 */
	const AJAX_ACTION = 'theplugin_newsletter_form';

	/**
	 * Ajax nonce.
	 * 
	 */
	const AJAX_NONCE = 'theplugin_newsletter_form_nonce';

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
	 */
	public function enqueue_scripts() {
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Enqueue scripts only if the shortcode is present in the post content.
		if ( ! ShortcodesHelpers::post_content_has_shortcode( $this::SHORTCODE_SLUG ) ) {
			return;
		}

		wp_enqueue_style( 'rtp-newsletter-form', RT_THEPLUGIN_PLUGIN_URL . "assets/css/shortcodes/newsletter-form{$min}.css", array( 'rtp-main' ), RT_THEPLUGIN_PLUGIN_VERSION );
		wp_add_inline_style( 'rtp-newsletter-form', $this->get_options_css() );

		wp_enqueue_script( 'rtp-newsletter-form', RT_THEPLUGIN_PLUGIN_URL . "assets/js/shortcodes/newsletter-form{$min}.js", array( 'rtp-main' ), RT_THEPLUGIN_PLUGIN_VERSION, true );
		wp_localize_script( 'rtp-newsletter-form', 'thepluginNewsletterFormData', array( 
			'slug' => $this::SHORTCODE_SLUG,
			'ajaxAction' => $this::AJAX_ACTION,
		) );
	}

	/**
	 * Get options CSS.
	 * 
	 */
	public function get_options_css() {
		$primary_color  = get_option( 'rt_newsletter_form_primary_color', '#212121' );
		$fields_padding = get_option( 'rt_newsletter_form_fields_padding', 10 );

		$css = "
			.rtp-newsletter-form {
				--rtp-newsletter-form-primary-color: {$primary_color};
				--rtp-newsletter-form-fields-padding: {$fields_padding}px;
			}
		";

		return $css;
	
	}

	/**
	 * Ajax handler.
	 * 
	 */
	public function ajax_handler() {

		// Check nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], $this::AJAX_NONCE ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid nonce.', 'rt-theplugin' ) ) );
		}

		// Check email.
		if ( ! isset( $_POST['email'] ) || ! is_email( $_POST['email'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid email.', 'rt-theplugin' ) ) );
		}

		// Check first name.
		if ( ! isset( $_POST['first_name'] ) || empty( $_POST['first_name'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'First name is required.', 'rt-theplugin' ) ) );
		}

		// Check last name.
		if ( ! isset( $_POST['last_name'] ) || empty( $_POST['last_name'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Last name is required.', 'rt-theplugin' ) ) );
		}

		// Insert subscriber.
		DatabaseNewsletterFormService::insert( $_POST['email'], $_POST['first_name'], $_POST['last_name'] );

		wp_send_json_success( array( 'message' => esc_html__( 'You have been subscribed.', 'rt-theplugin' ) ) );
	
	}

	/**
	 * Render shortcode.
	 * 
	 * @param array $atts
	 * @return string
	 */
	public function render( $atts ) {
		ob_start();
		?>
		<div class="rtp-newsletter-form">
			<form class="rtp-newsletter-form__form" action="" method="post">
				<?php wp_nonce_field( $this::AJAX_NONCE, 'nonce' ); ?>
				<input class="rtp-newsletter-form__form-field" type="email" name="email" placeholder="<?php echo esc_attr__( 'Your email address', 'rt-theplugin' ); ?>" required>
				<?php if ( get_option( 'rt_newsletter_form_display_first_name', false ) ) : ?>
					<input class="rtp-newsletter-form__form-field" type="text" name="first_name" placeholder="<?php echo esc_attr__( 'First name', 'rt-theplugin' ); ?>" required>
				<?php endif; ?>
				<?php if ( get_option( 'rt_newsletter_form_display_last_name', false ) ) : ?>
					<input class="rtp-newsletter-form__form-field" type="text" name="last_name" placeholder="<?php echo esc_attr__( 'Last name', 'rt-theplugin' ); ?>" required>
				<?php endif; ?>

				<button class="rtp-newsletter-form__form-submit" type="submit" data-loading="<?php echo esc_attr__( 'Loading...', 'rt-theplugin' ); ?>"><?php echo esc_html__( 'Subscribe', 'rt-theplugin' ) ?></button>
			</form>
			<div class="rtp-newsletter-form__error"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}