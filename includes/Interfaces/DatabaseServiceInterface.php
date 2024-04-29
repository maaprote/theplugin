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
	 * @param string $email
	 * @param string $first_name
	 * @param string $last_name
	 * @return mixed
	 */
	public static function insert( $email, $first_name, $last_name );

	/**
	 * Get data.
	 * 
	 * @return array
	 */
	public static function get();

	/**
	 * Maybe create table.
	 * 
	 * @return void
	 */
	public static function maybe_create_table();
}