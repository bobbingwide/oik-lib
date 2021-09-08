<?php // (C) Copyright Bobbing Wide 2015, 2021

/**
 * Admin interface for oik-lib
 *
 * Depends: oik-admin
 * Provides: oik-admin, bobbfunc, bobbforms
 */
 
 
/**
 * Lazy implementation of "admin_menu" for oik-lib
 *
 * The option set "bw_lib" contains the options for the oik-lib plugin
 * 
 */ 
function oik_lib_admin_do_page() {

  register_setting( 'oik_lib_options', 'bw_lib', 'oik_plugins_validate' );


	add_options_page( __('oik library management', 'oik-lib' ), __( 'oik library management', 'oik-lib' ), 'manage_options', 'oik_lib_options', 'oik_lib_options_do_page');
}


/**
 * Display the oik-lib options page
 *
 
 * 
 */ 
function oik_lib_options_do_page() {
	bw_context( "textdomain", "oik-lib" );
  BW_::oik_menu_header( __( "library management", "oik-lib" ) );
  BW_::oik_box( null, null, __( "Options", "oik-lib" ), "oik_lib_options" ); 
  BW_::oik_box( null, null, __( "Registered libraries", "oik-lib" ), "oik_lib_display_libraries" );
  oik_menu_footer();
  bw_flush();
}

/**
 * Display the options for oik-lib
 * 
 * 'mu' Enable as a must use plugin, if possible
 */
function oik_lib_options() {
	$option = "bw_lib";
	$options = bw_form_start( $option, "oik_lib_options" );
	bw_checkbox_arr( $option, __( "Enable as Must Use plugin?", 'oik-lib' ), $options, "enable-mu" );
	etag( "table " );
	e( isubmit( "ok", __( "Save changes", "oik-lib" ), null, "button-primary" ) ); 
  etag( "form" );
}

/**
 * Display the registered libraries
 *
 * Nothing fancy here as we cannot alter the settings
 * So we just convert each OIK_lib in the OIK_libs class to an array of fields
 * 
 * We can display $oik_libs->libraries, $oik_libs->loaded_libraries and $oik_libs->checked_libraries
 *
 * If deps is an assoc array then we only get the '*' - so we copy the value of args['deps']
 * which we know is set
 
 *
	                   [library] => oik_boot
                    [src] => C:\apache\htdocs\wordpress\wp-content\plugins\oik-bwtrace\libs\oik_boot.php
                    [deps] => 
                    [version] => 2.6
                    [init_function] => 
                    [args] => Array
 *
 */
function oik_lib_display_libraries() {
	p( "Registered libraries, showing the versions and dependencies." );
	$oik_libs = oik_libs();
	$libraries = $oik_libs->libraries;
	stag( "table", "wide-fat" );
	$labels = bw_as_array( __( "Library,Source,Dependencies,Version,Args", 'oik-lib' ) );
	bw_tablerow( $labels, "tr", "th" );
	foreach ( $libraries as $lib => $data ) {
		$data->deps = $data->args['deps'];
		unset( $data->error );
		bw_tablerow( [ $data->library, $data->src, $data->deps, $data->version, $data->args ] );
	}
	etag( "table" );
}