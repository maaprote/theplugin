<?php

/**
 * Custom Rest Endpoints.
 * 
 */

namespace Rodrigo\ThePlugin\Rest;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rodrigo\ThePlugin\Services\DatabaseNewsletterFormService;

class CustomEndpoints {

	/**
	 * Constructor.
	 * 
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Register endpoints.
	 * 
	 * @return void
	 */
	public function register_endpoints() {

		// Insert.
		register_rest_route( 'theplugin/v1', '/newsletter/insert', array(
			'methods'  => 'POST',
			'callback' => array( $this, 'insert' ),
			'permission_callback' => '__return_true',
		) );

		// Select/Get.
		register_rest_route( 'theplugin/v1', '/newsletter/get', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'get' ),
			'permission_callback' => '__return_true',
		) );
	}

	/**
	 * Insert.
	 * 
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function insert( $request ) {
		if ( empty( $_POST['email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return rest_ensure_response( esc_html__( 'Email is required.', 'rt-theplugin' ) );
		}

		if ( ! is_email( $_POST['email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return rest_ensure_response( esc_html__( 'Invalid email.', 'rt-theplugin' ) );
		}

		$display_first_name = get_option( 'rt_newsletter_form_display_first_name', false );
		if ( $display_first_name && ( ! isset( $_POST['first_name'] ) || empty( $_POST['first_name'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return rest_ensure_response( esc_html__( 'First name is required.', 'rt-theplugin' ) );
		}

		$display_last_name = get_option( 'rt_newsletter_form_display_last_name', false );
		if ( $display_last_name && ( ! isset( $_POST['last_name'] ) || empty( $_POST['last_name'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return rest_ensure_response( esc_html__( 'Last name is required.', 'rt-theplugin' ) );
		}

		$insert = DatabaseNewsletterFormService::insert( sanitize_email( $_POST['email'] ), sanitize_text_field( $_POST['first_name'] ), sanitize_text_field( $_POST['last_name'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( is_wp_error( $insert ) ) {
			return rest_ensure_response( $insert->get_error_message() );
		}

		return rest_ensure_response( esc_html__( 'Email subscribed with success.', 'rt-theplugin' ) );
	}

	/**
	 * Get.
	 * 
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get( $request ) {
		$newsletter_form_service = new \Rodrigo\ThePlugin\Services\DatabaseNewsletterFormService();
		$entries = $newsletter_form_service->get();

		return rest_ensure_response( $entries );
	}
}