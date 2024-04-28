<?php

/**
 * Shortcodes Helpers.
 * 
 */

namespace Rodrigo\ThePlugin\Frontend\Shortcodes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ShortcodesHelpers {

	/**
	 * Check if post content has shortcode.
	 * 
	 * @param string $shortcode_slug
	 * @return bool
	 */
	public static function post_content_has_shortcode( $shortcode_slug ) {
		global $post;

		if ( ! $post ) {
			return false;
		}

		return has_shortcode( $post->post_content, $shortcode_slug );
	}
}