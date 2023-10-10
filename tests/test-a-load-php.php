<?php

/**
 * @package oik-lib
 * @copyright (C) Copyright Bobbing Wide 2023
 *
 * Unit tests to load all the PHP files for PHP 8.2
 */
class Tests_load_php extends BW_UnitTestCase
{

	/**
	 * set up logic
	 *
	 * - ensure any database updates are rolled back
	 * - we need oik-googlemap to load the functions we're testing
	 */
	function setUp(): void
	{
		parent::setUp();

	}

	function test_load_admin_php() {
		oik_require( 'admin/oik-lib.php', 'oik-lib');
		$this->assertTrue( true );
	}

	function test_load_includes_php() {
		$files = glob( 'includes/class-*.php');
		//print_r( $files );

		foreach ( $files as $file ) {
			switch ( $file ) {

				default:
					oik_require( $file, 'oik-lib');
			}

		}
		$this->assertTrue( true );

	}

	function test_load_libs_php() {
		$files = glob( 'libs/*.php');
		//print_r( $files );

		foreach ( $files as $file ) {
			switch ( $file ) {

				case '':

					break;


				default:
					oik_require( $file, 'oik-lib');
			}

		}
		$this->assertTrue( true );

	}

}
