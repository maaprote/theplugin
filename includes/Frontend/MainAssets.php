<?php

/**
 * Main assets.
 * 
 */

namespace Rodrigo\ThePlugin\Frontend;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MainAssets {
	
	/**
	 * Constructor.
	 * 
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts.
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( 'rtp-main', RT_THEPLUGIN_PLUGIN_URL . "assets/css/main{$min}.css", array(), RT_THEPLUGIN_PLUGIN_VERSION );
		wp_enqueue_script( 'rtp-main', RT_THEPLUGIN_PLUGIN_URL . "assets/js/main{$min}.js", array(), RT_THEPLUGIN_PLUGIN_VERSION, true );
		wp_localize_script( 'rtp-main', 'thepluginData', array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
	}
}