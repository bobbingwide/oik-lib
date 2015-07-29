<?php
/*
Plugin Name: oik library management 
Plugin URI: http://www.oik-plugins.com/oik-plugins/oik-lib
Description: OIK library management - for shared libraries
Version: 0.0.1
Author: bobbingwide
Author URI: http://www.oik-plugins.com/author/bobbingwide
Text Domain: oik-lib
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2015 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

/**
 * Implement "admin_menu" for oik-lib
 */
function oik_lib_admin_menu() {
	bw_trace2();
	bw_backtrace();
	oik_lib_maybe_activate_mu();
  add_action( "admin_notices", "oik_lib_admin_notices", 9 );
} 

/**
 * Activate the MU version of this plugin
 *
 * When the plugin is deactivated then the MU plugin may also be deactivated.
 * In order to achieve, before the plugin is deactivated, enable-mu should be set to false, . 
 * 
 *
 * @TODO Should this use oik_require_lib()? 
 */
function oik_lib_maybe_activate_mu() {
	if ( function_exists( "oik_require" ) ) {
		oik_require( "libs/oik-lib-mu.php", "oik-lib" );
		if ( defined( "OIK_ALLOW_MU" ) ) {
			$activate = OIK_ALLOW_MU;
		} else {
			if ( function_exists( "bw_get_option" ) ) {
				$activate = bw_get_option( "enable-mu", "bw_lib" );
			} else {
				$activate = false;
			}
		}
		oik_lib_activate_mu( $activate  );
	}		
}

/**
 * Implement "admin_notices" for oik-lib 
 */
function oik_lib_admin_notices() {
	//$loaded = oik_require_lib( "oik-depends" );
	//bw_trace2( $loaded, "oik-depends loaded?", false );
	$loaded = oik_require_lib( "oik-activation" );
	bw_trace2( $loaded, "oik-activation loaded?", false );
} 

/**
 * Boot ourselves up using the shared libraries "lib-boot", "lib-trace" and "lib-require"
 *
 * Then register the shared libraries so that other plugins can use oik_require_lib()
 * 
 * @return bool true if the initial libraries loaded 
 * 
 */
function oik_lib_boot_oik_lib() {
	$loaded = true;
	if ( !function_exists( "oik_lib_boot" ) ) {
		$oik_lib_file = __DIR__ . "/libs/oik-lib.php";
		$loaded = include_once( $oik_lib_file );
	}
	if ( function_exists( "oik_lib_boot" ) ) {
		$loaded = oik_lib_boot();
	} else {
		$loaded = false;
	}
	return( $loaded );
}

/**
 * Implement "oik_query_libs" for oik-lib
 *
 * Note: The library files should exist. 
 * Their presence should be checked before they're added to the array
 * @TODO Add version information... this can be deferred until the library is actually needed.
 *
 * @param array $libraries the registered libraries
 * @return array the registered libraries
 */ 
function oik_lib_oik_query_libs( $libraries ) {
	$libs = array( "bobbfunc" => null, "bobbforms" => "bobbfunc", "oik-admin" => "bobbforms", "oik-depends" => null, "oik-activation" => "oik-depends" );
	$libraries = oik_lib_check_libs( $libraries, $libs, "oik-lib" );
	bw_trace2();
	return( $libraries );
}

/**
 * Lazy load the oik-lib options page
 *
 */
function oik_lib_options_add_page() {
	oik_require( "admin/oik-lib.php", "oik-lib" );
	oik_lib_admin_do_page();
}

/**
 * Implement "init" for oik-lib 
 *
 * This will load the translation strings for the plugin but the shared library files may have the wrong domain.
 * 
 * @TODO Decide how to deal with the domain for shared libraries
 * Possibly, load each library into "oik-lib" ... but how do we build/deliver the .po and .mo files?
 * 
 */
function oik_lib_init() {
	bw_load_plugin_textdomain( 'oik-lib' );
}

/**
 * Functions to invoke when loaded
 *
 * Just about everything that needs to be done should be done by the _oik-lib-mu plugin
 * so we don't need to do anything except when being activated / deactivated
 * 
 * Given that we don't do anything much we might as well let ourselves become deactivated. 
 * In which case, we should have / share an option field to record the state of play.
 * One of the values will be "Always load MU libraries"
 *  
 * This can also be used by the _oik-lib-mu plugin to 
 * decide which library files should be loaded, regardless of the state of the plugins which deliver them.
 * 
 * So we only need to do anything when we're processing "admin" stuff
 *
 */
function oik_lib_loaded() {
	if ( oik_lib_boot_oik_lib() ) { 
		add_action( "admin_menu", "oik_lib_admin_menu" );
		add_filter( "oik_query_libs", "oik_lib_oik_query_libs" );
		oik_lib_fallback( __DIR__ . "/libs" );
		if ( oik_require_lib( "oik-admin" ) && oik_require_lib( "bobbforms" ) && oik_require_lib( "bobbfunc" )  ) {
			add_action( 'admin_menu', 'oik_lib_options_add_page');
		} 
		add_action( "init", "oik_lib_init" );
	}
}	

oik_lib_loaded();



