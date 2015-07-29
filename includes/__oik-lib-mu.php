<?php // (C) Copyright Bobbing Wide 2015

/*
Plugin Name: _oik-lib-MU
Plugin URI: http://www.oik-plugins.com/oik-plugins/oik-lib
Description: oik library management - MU version
Version: 0.1
Author: bobbingwide
Author URI: http://www.oik-plugins.com/author/bobbingwide
License: GPL2

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
 * oik-lib Must Use version is the file that is used to implement oik-lib's logic as a Must Use plugin
 *
 * 
 *  
 */
if ( defined( 'WP_PLUGIN_DIR' ) ) {
	$file = WP_PLUGIN_DIR .  '/oik-lib/libs/oik-lib.php';
} else {
	$file = dirname( dirname( __FILE__ ) ) . '/plugins/oik-lib/libs/oik-lib.php';
}

if ( file_exists( $file ) ) {
	require_once( $file );
	oik_lib_boot();
} else {
	gob();
}

  
 
 
 
