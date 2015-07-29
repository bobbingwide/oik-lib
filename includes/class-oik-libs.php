<?php // (C) Copyright Bobbing Wide 2015

/**
 * Class for micro managing shared libraries of PHP code
 *
 */
class OIK_libs {

	/**
	 * The list of libraries
	 *
	 * @var array
	 * @access public
	 */
	public $libraries;
	
	/**
	 * The list of loaded libraries
	 *
	 * Our record of the libraries that have been loaded
	 *
	 * @var array
	 * @access public
	 */
	public $loaded_libraries;
	
	/**
	 * The list of dependencies checked
	 * 
	 * Each time we check dependencies we add the library name to the list
	 * even if we don't actually load the library
	 * This is intended to stop recursive dependency checking.
	 */
	public $checked_libraries;
	
	/**
	 * Local did_filter (as opposed to did_action) for "oik_query_libs" 
   */
	private $did_query_libs;
	
	
	/**
	 * @var OIK_libs - the true instance
	 */
	private static $instance;
	
	/**
	 * Return a single instance of this class
	 *
	 * @return object 
	 */
	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof OIK_libs ) ) {
			self::$instance = new OIK_libs;
		}
		return self::$instance;
	}
	
	
	/**
	 * Constructor
	 *
	 */
	function __construct() {
		$this->libraries = array();
		$this->loaded_libraries = array();
		$this->did_query_libs = false;
	}
	
	/**
	 * Register a library
	 *
	 * @param string $library the library handle - which may be duplicated
	 * @param string $src the fully qualified file name ( e.g. oik_path( $file, $plugin ) )
	 * @param array $deps dependencies on other libraries
	 * @param string $ver version
	 * @param array $args additional arguments for more advanced dependencies and other stuff
	 *
	 * @return 
	 */
	function register_lib( $library, $src=null, $deps=null, $ver=null, $args=null ) {
	
		if ( $args && is_array( $args ) ) { 
			$lib_args = $args;
		} else { 
			$lib_args = array();
		}
		$lib_args['library'] = bw_array_get( $lib_args, 'library', $library );
		$lib_args['src'] = bw_array_get( $lib_args, 'src', $src );
		$lib_args['deps'] = bw_array_get( $lib_args, 'deps', $deps );
		$lib_args['version'] = bw_array_get( $lib_args, 'version', $ver ); 
		$lib = new OIK_lib( $lib_args );
		$this->add( $lib );
		return( $lib );
	
	}
	
	/**
	 * Add a registered library	
	 *
	 * Note that we can have duplicate entries with the same library name
	 * It's only when we load the library ( using require) that it becomes resolved.
	 */
	 
	function add( $lib ) {
		$this->libraries[] = $lib;
	
	}
	
	/**
	 * Deregister a library
	 * 
	 * I can't think of a reason for doing this
	 * and it certainly won't work once the library has been loaded
	 * but it could be used to force some dependency checking to fail
	 * for whatever reason you might want it to.
	 */
	function deregister_lib( $library ) {
	
	} 
	
	/**
	 * Require a library file
	 *
	 * @param string $library the library name e.g. "lib-boot"
	 * @param string $version the required library version e.g "2.5" - with wildcard stuff
	 * @return object the lib object loaded
	 */
	function require_lib( $library, $version=null ) {
		$this->query_libs();
		$lib = $this->determine_lib( $library, $version );
		if ( $lib ) {
			if ( file_exists( $lib->src ) ) {
				require_once( $lib->src ); 
				$this->loaded( $lib );
			} else {
				bw_trace2( $lib, "Library file missing" );
				$lib = false;
			}
		}
		return( $lib );
	}
	
	/**
	 * Query the shared libraries available
	 *
	 * We may need to do this for "muplugins_loaded" and "plugins_loaded" 
	 * So perhaps it's not a good idea to use a bool for did_query_libs; but support multiple invocations
	 * controlled by some other mechanism
	 * I know. We reset the filter to false when we see "muplugins_loaded" or "plugins_loaded"
	 * So it's "do_query_libs" rather than "did_query_libs" eh?
	 *
	 * @param bool $force true when you want to force the filter to be re-processed
	 *
	 */
	function query_libs( $force=false ) {
		if ( !$this->did_query_libs || $force ) {
			$this->libraries = apply_filters( "oik_query_libs", $this->libraries ); 
		}
		$this->did_query_libs = true;
	}
	
	/**
	 * Mark the library as loaded
	 *
	 * @param object $lib
	 * @return object 
	 */
	function loaded( $lib ) {
		$this->loaded_libraries[ $lib->library ] = $lib ;
		do_action( "oik_lib_loaded", $lib );
		return( $lib );
	}
	
	/**
	 * Determine if the library has been loaded
	 *
	 * @param string $library the library to be loaded
	 * @param string $version the version to be loaded
	 * @return object the loaded library, or null
	 */
	function is_loaded( $library, $version ) {
		$loaded = null;
		$lib = bw_array_get( $this->loaded_libraries, $library, null );
		if ( $lib ) {
			$this->compatible_version( $lib->version, $version );
			$loaded = $lib;
		} 
		bw_trace2( $loaded, "loaded" );
		return( $loaded );
	}
	
	/**
	 * Determine if a given $lib object is already loaded
	 *
	 * The library may have been loaded outwith this class
	 * If so, we record it as loaded for future invocation
	 * @param object $lib an OIK_lib object
	 * @return 
	 */
	function is_already_loaded( $lib ) {
		$src = $lib->src;
		$src = str_replace( "/", DIRECTORY_SEPARATOR, $src );
		$files = get_included_files();
		bw_trace2( $files, "included files" );
		$loaded = bw_array_get( array_flip( $files), $src, null );
		if ( $loaded ) {
			$loaded = $this->loaded( $lib );
		}
		bw_trace2( $loaded, "already loaded" );
		return( $loaded );		
	}
	
	/**
	 * Determine if the current version is compatible with the required version
	 *
	 * @TODO Decide what to do about version checking, which is already fully implemented for packages in Composer
	 *
	 * @param string $current_version a specific version
	 * @param string $required_version may include wildcards
	 */ 
	function compatible_version( $current_version, $required_version ) {
		return( true );
	}
	
	/**
	 * Determine a library file
	 *
	 * Find the $lib that satisfies this request for a library / version combination
	 *
	 */
	function determine_lib( $library, $version ) {
	
		$selected = $this->is_loaded( $library, $version );
		if ( !$selected ) {
			if ( $this->libraries && count( $this->libraries ) ) {
				foreach ( $this->libraries as $key => $lib ) {
					if ( $lib->library == $library ) {
						$selected = $this->is_already_loaded( $lib );
						if ( !$selected ) {
							if ( $this->compatible_version( $lib->version, $version ) ) {
								$selected = $this->load_dependencies( $lib ); 
								$selected = $lib;
							}
						}
						if ( $selected ) {
							break;
						}
					}	
				}
			}
		}
		bw_trace2( $selected, "selected" );
		return( $selected );
	}
	
	/**
	 * Load the libraries upon which this library is dependent
	 * 
	 * Here we assume that this will be possible
	 * It's first come first served when it comes to library hell ( similar to DLL hell )
	 * We'll let composer or some other thing resolve the version incompatibilities.
	 */
	function load_dependencies( $lib ) {
		$checked = $this->checked( $lib );
		if ( !$checked ) {
			$lib_deps = $lib->deps();
			if ( $lib_deps ) {
				foreach ( $lib_deps as $library => $version ) {
					$checked = $this->require_lib( $library, $version );		
				}
			}
		}
		if ( !$checked ) {
			$checked = $lib;
		}
		return( $checked );
	}
	
	/**
	 * Determine if the library has been checked for dependencies
	 * 
	 * If the library is already loaded then we assume that its dependencies have been checked
	 * If the library has already been checked then we don't need to do it again.
	 * Each library that's checked is added to the array of checked libraries.
	 *
	 * @param object $lib 
	 * @return object the checked library object or null
	 */
	function checked( $lib ) {
		$checked = $this->is_loaded( $lib->library, $lib->version );
		$checked = bw_array_get( $this->checked_libraries, $lib->library, $checked );
		$this->checked_libraries[ $lib->library ] = $lib;
		bw_trace2( $checked, "checked" );
		return( $checked );
	}

	/**
	 * Require a library function
	 *
	 * @param string $library 
	 * @param string/callable function $func
	 * @param string $version
	 * @param array $args
	 * @return 
	 */
	function require_func( $library, $func, $version=null, $args=null ) {
		$required_func = null;
		if ( is_callable( $func ) ) {
			$required_func = $func;
		} else {
			// @TODO Should this be $this->require_lib() ?
			$lib_loaded = oik_require_lib( $library, $version );
			if ( is_callable( $func ) ) {
				$required_func = $func;
			}
		}
		return( $required_func );
	}

}


