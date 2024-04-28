<?php

/**
 * Database Service.
 * 
 */

namespace Rodrigo\ThePlugin\Services;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rodrigo\ThePlugin\Interfaces\DatabaseServiceInterface;

class DatabaseNewsletterFormService implements DatabaseServiceInterface {

	/**
	 * Insert data.
	 * 
	 */
	public static function insert( $email, $first_name, $last_name ) {
		global $wpdb;

		$table_name = "{$wpdb->prefix}theplugin_subscribers";

		$wpdb->insert( 
			$table_name, 
			array( 
				'time' => current_time( 'mysql' ),
				'email' => sanitize_email( $_POST['email'] ),
				'first_name' => sanitize_text_field( $_POST['first_name'] ),
				'last_name' => sanitize_text_field( $_POST['last_name'] ),
			) 
		);
	}

	/**
	 * Get data.
	 * 
	 */
	public static function get() {
		global $wpdb;

		$table_name = "{$wpdb->prefix}theplugin_subscribers";

		$entries = $wpdb->get_results( "SELECT * FROM {$table_name}" );

		return $entries;
	}

	/**
	 * Get by email.
	 * 
	 */
	public static function get_by_email( $email ) {
		global $wpdb;

		$table_name = "{$wpdb->prefix}theplugin_subscribers";

		$email = '%' . $wpdb->esc_like( $email ) . '%';

		$sql = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE email LIKE %s", $email );

		$entries = $wpdb->get_results( $sql );

		return $entries;
	}

	/**
	 * Maybe create table.
	 * 
	 */
	public static function maybe_create_table() {
		global $wpdb;

		$table_name = "{$wpdb->prefix}theplugin_subscribers";
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id INT NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			email VARCHAR(100) NOT NULL,
			first_name VARCHAR(50),
			last_name VARCHAR(50),
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}