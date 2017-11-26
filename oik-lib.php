<?php
/*
Plugin Name: oik library management 
Plugin URI: https://www.oik-plugins.com/oik-plugins/oik-lib-shared-library-management/
Description: OIK library management - for shared libraries
Version: 0.1.1
Author: bobbingwide
Author URI: https://www.oik-plugins.com/author/bobbingwide
Text Domain: oik-lib
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2015-2017 Bobbing Wide (email : herb@bobbingwide.com )

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
	//bw_backtrace();
	
	$loaded = oik_require_lib( "oik-admin" );
	$loaded = oik_require_lib( "class-BW-" );
	oik_lib_options_add_page();

	oik_lib_maybe_activate_mu();
  add_action( "admin_notices", "oik_lib_admin_notices", 8 );
} 

/**
 * Activate the MU version of this plugin
 *
 * When the plugin is deactivated then the MU plugin may also be deactivated.
 * In order to achieve this, before the plugin is deactivated, enable-mu should be set to false.
 * Note: If oik is not active then bw_get_option() won't be available so the MU logic can only be
 * controlled by the constant. 
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
 *
 * Hard code our dependency on oik-depends 
 * @TODO Explain why do we need to do this... since it's oik-lib? 
 * 
 */
function oik_lib_admin_notices() {
	$loaded = oik_require_lib( "oik-depends" );
	bw_trace2( $loaded, "oik-depends loaded?", false );
	
	$loaded = oik_require_lib( "oik-activation" );
	bw_trace2( $loaded, "oik-activation loaded?", false );
	
} 

/**
 * Boot ourselves up using the initial shared libraries
 *
 * If not already loaded we load the "oik-lib" library
 * then we invoke oik_lib_boot() to ensure the libraries upon which
 * oik-lib is dependent are loaded. 
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
 * Note: In order for a plugin to share a library the library file should exist.
 * Its presence is determined by oik_lib_check_libs() 
 
 * @TODO Add version information... this can be deferred until the library is actually needed.
 *
 * @param array $libraries the registered libraries
 * @return array the registered libraries
 */ 
function oik_lib_oik_query_libs( $libraries ) {
	bw_trace2( null, null, true, BW_TRACE_VERBOSE );
	$libs = array( "bobbfunc" => null
							, "bobbforms" => "bobbfunc"
							, "oik-admin" => "bobbforms"
							, "oik-depends" => null
							, "oik-activation" => "oik-depends" 
							, "class-BW-" => "bobbfunc,oik-admin"
							, "class-oik-update" => null
							);
	$libraries = oik_lib_check_libs( $libraries, $libs, "oik-lib" );
	bw_trace2( $libraries, "new libraries", false, BW_TRACE_VERBOSE );
	return( $libraries );
}

/**
 * Lazy load the oik-lib options page
 *
 */
function oik_lib_options_add_page() {
	bw_backtrace();
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
	bw_backtrace( BW_TRACE_VERBOSE );
	load_plugin_textdomain( 'oik-lib' );
}


/**
 * Implement "plugins_loaded" action for "oik-lib"
 * 
 */ 
function oik_lib_reset_libs() {
	$oik_lib = oik_libs();
	$oik_lib->reset();
}

/**
 * Implement "wp_loaded" action for oik-lib
 *
 * @TODO Decide whether or not oik-lib will actually provide the oik-admin, bobbforms and bobbfunc shared libraries
 *
 * Note: Prior to v3.0.0 bobbfunc did not provide bw_as_array().
 * This function is currently needed by oik-lib admin.
 * The oik-admin library, which is dependent upon bobbforms, which is dependent upon bobbfunc, is not aware of this problem.
 * It doesn't currently have any version dependency.
 *
 * So we need to check for this first. 
 * Note: We have to wrap each call to oik_require_lib() with !is_wp_error() 
 *  
 */
function oik_lib_wp_loaded() {
	$bobbfunc = oik_require_lib( "bobbfunc", "3.2.*" ); 
	bw_trace2( $bobbfunc, "bobbfunc?", false, BW_TRACE_DEBUG );
	if ( !is_wp_error( $bobbfunc ) ) {
		if ( !is_wp_error( oik_require_lib( "oik-admin" ) ) && !is_wp_error( oik_require_lib( "bobbforms" ) ) ) {
			add_action( 'admin_menu', 'oik_lib_options_add_page');
		}
	}	else {
		//gob();
	}
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
		bw_trace2( "oik_lib_boot_oik_lib worked", null, false, BW_TRACE_DEBUG );
		add_filter( "oik_query_libs", "oik_lib_oik_query_libs", 11 );
		add_action( "admin_menu", "oik_lib_admin_menu" );
		oik_lib_fallback( __DIR__ . "/libs" );
		add_action( "init", "oik_lib_init" );
		add_action( "plugins_loaded", "oik_lib_reset_libs" );
		add_action( "wp_loaded", "oik_lib_wp_loaded" );
	} else {
		gob();
	}
}	

oik_lib_loaded();



