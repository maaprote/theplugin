<?php

/**
 * Options page.
 * 
 */

namespace Rodrigo\ThePlugin\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OptionsPage {

	/**
	 * Constructor.
	 * 
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'rest_api_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add menu page.
	 * 
	 * @return void
	 */
	public function add_menu_page() {
		add_menu_page(
			__( 'The Plugin', 'rt-theplugin' ),
			__( 'The Plugin', 'rt-theplugin' ),
			'manage_options',
			'rt-theplugin',
			array( $this, 'render_menu_page' ),
			'dashicons-admin-generic',
			2
		);
	}

	/**
	 * Register settings.
	 * 
	 * @return void
	 */
	public function register_settings() {

		// Display first name.
		register_setting(
			'rt-theplugin',
			'rt_newsletter_form_display_first_name',
			array(
				'type'              => 'boolean',
				'description'       => __( 'Display newsletter form first name', 'rt-theplugin' ),
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);

		// Display last name.
		register_setting(
			'rt-theplugin',
			'rt_newsletter_form_display_last_name',
			array(
				'type'              => 'boolean',
				'description'       => __( 'Display newsletter form last name', 'rt-theplugin' ),
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => false,
			)
		);

		// Primary/Accent color.
		register_setting(
			'rt-theplugin',
			'rt_newsletter_form_primary_color',
			array(
				'type'              => 'string',
				'description'       => __( 'Newsletter form primary color', 'rt-theplugin' ),
				'sanitize_callback' => 'sanitize_hex_color',
				'show_in_rest'      => true,
				'default'           => '#212121',
			)
		);
	}

	/**
	 * Render menu page.
	 * 
	 * @return void
	 */
	public function render_menu_page() {
		echo '<div id="rt-theplugin-admin-page"></div>';
	}
	
}