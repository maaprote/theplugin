<?php

/**
 * Admin assets.
 * 
 */

namespace Rodrigo\ThePlugin\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminAssets {

	/**
	 * Constructor.
	 * 
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts.
	 * 
	 */
	public function admin_enqueue_scripts() {
		$current_screen = get_current_screen();

		if ( 'toplevel_page_rt-theplugin' !== $current_screen->id ) {
			return;
		}

		$asset_file = include( RT_THEPLUGIN_PLUGIN_PATH . 'assets/js/admin/page-options-build.asset.php' );

		wp_enqueue_script( 'rt-theplugin-page-options', RT_THEPLUGIN_PLUGIN_URL . 'assets/js/admin/page-options-build.js', $asset_file['dependencies'], $asset_file['version'], true );

		wp_enqueue_style( 'wp-components' );
	}
}