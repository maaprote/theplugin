<?php

/**
 * Database Service Interface.
 * 
 */

namespace Rodrigo\ThePlugin\Interfaces;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface DatabaseServiceInterface {

	/**
	 * Insert data.
	 * 
	 */
	public static function insert( $email, $first_name, $last_name );

	/**
	 * Get data.
	 * 
	 */
	public static function get();

	/**
	 * Get data by email.
	 * 
	 */
	public static function get_by_email( $email );

	/**
	 * Maybe create table.
	 * 
	 */
	public static function maybe_create_table();
}