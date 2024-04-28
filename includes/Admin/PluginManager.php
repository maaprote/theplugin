<?php

/**
 * Plugin Manager.
 * 
 */

namespace Rodrigo\ThePlugin\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rodrigo\ThePlugin\Services\DatabaseNewsletterFormService;

class PluginManager {

	/**
	 * Constructor.
	 * 
	 */
	public function __construct() {

		// Register activation hook.
		register_activation_hook( RT_THEPLUGIN_PLUGIN_PATH . 'rt-theplugin.php', array( $this, 'activate' ) );

		// Register deactivation hook.
		register_deactivation_hook( RT_THEPLUGIN_PLUGIN_PATH . 'rt-theplugin.php', array( $this, 'deactivate' ) );

	}

	/**
	 * Activation hook.
	 * 
	 * @return void
	 */
	public function activate() {
		DatabaseNewsletterFormService::maybe_create_table();
	}

	/**
	 * Deactivation hook.
	 * 
	 * @return void
	 */
	public function deactivate() {}
}