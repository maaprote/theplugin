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
	 * @param string $email
	 * @param string $first_name
	 * @param string $last_name
	 * @return mixed
	 */
	public static function insert( $email, $first_name, $last_name ) {
		global $wpdb;

		$table_name = "{$wpdb->prefix}theplugin_subscribers";

		// check if email already exists
		$entries = self::email_exists( $email );

		if ( ! empty( $entries ) ) {
			return new \WP_Error( 'email_exists', __( 'Email already exists.', 'rt-theplugin' ) );
		}

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
	 * @return array
	 */
	public static function get() {
		global $wpdb;

		$table_name = "{$wpdb->prefix}theplugin_subscribers";

		// No need to prepare() here.
		$entries = $wpdb->get_results( "SELECT * FROM {$table_name}" );

		return $entries;
	}

	/**
	 * Get by email.
	 * 
	 * @param string $email
	 * @return array
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
	 * Check whether email already exists in the table.
	 * 
	 * @param string $email
	 * @return bool
	 */
	public static function email_exists( $email ) {
		global $wpdb;

		$table_name = "{$wpdb->prefix}theplugin_subscribers";

		$sql = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE email = %s", $email );

		$entries = $wpdb->get_results( $sql );

		return ! empty( $entries );
	}

	/**
	 * Maybe create table.
	 * 
	 * @return void
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