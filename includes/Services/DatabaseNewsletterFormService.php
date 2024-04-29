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

		if ( ! isset( $_POST['email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return new \WP_Error( 'email_required', __( 'Email is required.', 'rt-theplugin' ) );
		}

		$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$table_name, 
			array( 
				'time' => current_time( 'mysql' ),
				'email' => sanitize_email( $_POST['email'] ), // phpcs:ignore WordPress.Security.NonceVerification.Missing
				'first_name' => isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing
				'last_name' => isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Missing
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

		$sql = $wpdb->prepare( "SELECT * FROM %i", $table_name );

		$entries = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- false positive (leanr more here: https://github.com/WordPress/WordPress-Coding-Standards/issues/508)

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

		$sql = $wpdb->prepare( "SELECT * FROM %i WHERE email LIKE %s", $table_name, $email ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$entries = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- false positive (leanr more here: https://github.com/WordPress/WordPress-Coding-Standards/issues/508)

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

		$sql = $wpdb->prepare( "SELECT * FROM %i WHERE email = %s", $table_name, $email );

		$entries = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- false positive (leanr more here: https://github.com/WordPress/WordPress-Coding-Standards/issues/508)

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