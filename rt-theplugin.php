<?php

/*
* Plugin Name: The Plugin.
* Plugin URI: https://github.com/maaprote
* Description: Make Magic
* Version: 1.0.0
* Requires at least: 5.5
* Requires PHP: 7.2
* Author: Rodrigo Teixeira
* Author URI: https://github.com/maaprote
* License: GPL v3 or later
* License URI: https://www.gnu.org/licenses/gpl-3.0.html
* Update URI: 
* Text Domain: rt-theplugin
* Domain Path: /languages
*/

namespace Rodrigo\ThePlugin;

use Rodrigo\ThePlugin\Admin\PluginManager;
use Rodrigo\ThePlugin\Admin\AdminAssets;
use Rodrigo\ThePlugin\Admin\OptionsPage;
use Rodrigo\ThePlugin\Frontend\MainAssets as FrontendAssets;
use Rodrigo\ThePlugin\Frontend\Shortcodes\DisplayNewsletterEntries;
use Rodrigo\ThePlugin\Frontend\Shortcodes\NewsletterForm;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'The_Plugin' ) ) {
	final class The_Plugin {
		
		/**
		 * Custom_Plugin constructor.
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();
		}

		/**
		 * Define constants.
		 */
		private function define_constants() {
			define( 'RT_THEPLUGIN_PLUGIN_VERSION', '1.0.0' );
			define( 'RT_THEPLUGIN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			define( 'RT_THEPLUGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Include required core files.
		 */
		private function includes() {
			require __DIR__ . '/vendor/autoload.php';

			new PluginManager();
			new AdminAssets();
			new OptionsPage();
			new FrontendAssets();
			new NewsletterForm();
			new DisplayNewsletterEntries();
		}
	}

	new The_Plugin();
}

