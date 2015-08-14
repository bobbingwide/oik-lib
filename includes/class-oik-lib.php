<?php // (C) Copyriight Bobbing Wide 2015

/**
 * Class OIK_lib
 * 
 * Helper class implementing a single library used by oik-libs
 *
 */
class OIK_lib {

	/**
	 * Library name 
	 *
	 * This is the name used during oik_require_lib()
	 * and for dependencies
	 *
	 * At present it's expected to be a simple name such as "oik_boot" or "bwtrace"
	 * In the future it may be name spaced a la composer: ie "vendor\package"
	 */
	public $library;
	
	/**
	 * Source path
	 *
	 * Fully qualified path to the Source file
	 */
	 public $path;
	
	/**
	 * Source file
	 * 
	 * Fully qualified file name for the library source file
	 * Constructed from $args['plugin'] and $args['file'] if not specified in $args['src']
	 * OR, if the other fields are defined in $args, from them.
	 */
	public $src;
	
	
	/**
	 * String or Array of dependencies
	 * 
	 * Expected to be in the form of array( 'library' => 'version' )
	 * when used to perform dependency checking.
	 * Use $this->deps() to return the expected form.
	 */
	public $deps;
	
	/**
	 * Library version
	 */
	public $version;
	
	/**
	 * Initialization function - future use
	 */
	//public $init_function;
	
	/**
	 * Additional arguments - to be defined
	 */
	public $args;
	
	/**
	 * Will contain a WP_Error if there's something wrong with the library
	 * 
	 */
	public $error;
	 
	/**
	 * Return an instance of a library object
	 *
	 * Logic is invoked Only When Necessary
	 * So we only set the minimum number of properties and leave the rest of the values in $args.
	 *
	 * @TODO Defer setting of version
	 *
	 * @param array $lib_args array of library properties as above
	 * @return the instance of the object
	 */ 
	function __construct( $lib_args ) {
		$this->library = bw_array_get( $lib_args, 'library', null );
		$this->path = null;
		$this->src = null;
		$this->deps = null;
		$this->version = bw_array_get( $lib_args, 'version', null );
		$this->args = $lib_args;
		$this->error = null;
		return( $this );
	}
	
	/**
	 * Create a WordPress error 
	 *
	 * @param string $code Error code
	 * @param string $text Translatable text further defining the code
	 * @param mixed $data Additional error data
	 * @return a WP_Error instance
	 */
	function error( $code, $text=null, $data=null ) {
		$error = new WP_Error( $code, $text, $data );
		$this->error = $error;
		return( $error );
	} 
	
	/**
	 * Obtain the value of the vendor
	 *
	 * vendor  | library | returned
	 * ------- | ------- | --------
	 *
	 */
	function vendor() {
		$vendor = bw_array_get( $this->args, "vendor", null );
		if ( !$vendor ) {
			$vendor = $this->library_vendor(); 
		}
		return( $vendor );	
	}
	
	/** 
	 * Get the vendor from the library name#
	 *
	 * If there's no slash then the vendor is not given in the library name
	 *
	 */
	function library_vendor() {
		if ( $spos = strpos( $this->library, "/" ) ) {
			$vendor = substr( $this->library, 0, $spos ); 
		} else {
			$vendor = null;
		}
		return( $vendor );
	}
	
	/**
	 * Determine the package
	 * 
	 * Get it from args['package']. If not present try args['library'] 
	 */
	function package() {
		$package = bw_array_get( $this->args, "package", null );
		if ( !$package ) {
			$package = $this->library_package(); 
		}
		return( $package );	
	}
	
	/** 
	 * Obtain the package name from the library
	 *
	 * Note: We only do this if package is not defined.
	 */
	function library_package() {
		if ( $spos = strpos( $this->library, "/" ) ) {
			$package = substr( $this->library, $spos+1 ); 
		} else {
			$package = $this->library;
		}
		return( $package );
	}
	
	/**
	 * Obtain the vendor-dir 
	 *
	 * @param string $vendor vendor name e.g. bobbingwide
	 * @param string $package package name e.g. oik_depends
	 * @return string the folder in which we will look for the library
	 */
	function vendor_dir( $vendor=null, $package=null ) {
		$vendor_dir = bw_array_get( $this->args, "vendor-dir", null );
		if ( !$vendor_dir ) {
			if ( $vendor && $package ) {
				$vendor_dir = "vendor";
			} else {
				$vendor_dir = "libs";
			}
		}
		return( $vendor_dir );
	}
	
	/**
	 * Determine the file name for this library
	 */
	function file() {
		$file = bw_array_get( $this->args, "file", null ); 
		if ( !$file ) {
			$package = $this->package();
			if ( $package ) {
				$file = "$package";
			} else {
				$file = $this->library;
			}
			$file .= ".php";
		}
		return( $file );
	}

	/**
	 * Build path from lib_args
	 *
	 * $lib_args is a sparse array that may contain the following
	 * 
	 * Key          | Contents
	 * ------------ | ------------------------------------------------------------------------------
	 * 'library'    | library name - either a shareable library name or in 'vendor/package' format
	 * 'plugin'     |	plugin name - the plugin's folder name 
	 * 'theme'      | theme name - the theme's folder
	 * 'vendor-dir' | See below
	 * 'vendor'     | vendor name for a Composer package
	 * 'package'    | package name for a Composer package
	 * 'file'       | file name of the main library file in the path. default $library.php for 'libs', $package.php otherwise
	 * 
	 * 
	 * vendor-dir defaults to "libs" if 'vendor' and 'package' are not defined or 'vendor' if they are.
	 * If the library name appears to be in the form of vendor/package then we'll err towards it being a Composer package.
	 *
	 * @TODO If we need to work with symlinked files we may need to cobble something up from bw_trace_anonymize_symlinked_file()
	 * 
	 */
	function path_from_lib_args() {
		$path_array = array();
		$error = null;
		$plugin = bw_array_get( $this->args, 'plugin', null );
		if ( $plugin ) {
			$path_array[] = WP_PLUGIN_DIR;
			$path_array[] = $plugin;
		} elseif ( $theme = bw_array_get( $this->args, 'theme', null ) ) {
			$path_array[] = WP_CONTENT_DIR;
			$path_array[] = 'themes';
			$path_array[] = $theme;
		} else {
			bw_backtrace();
			$error = $this->error( "parameter error", "Missing plugin/theme", $this );
		}
		
		if ( !$error ) {
			$vendor = $this->vendor();
			$package = $this->package();
			$vendor_dir = $this->vendor_dir( $vendor, $package );
			$path_array[] = $vendor_dir;
			if ( $vendor ) {
				$path_array[] = $vendor;
			}
			if ( $package ) {
				$path_array[] = $package;
			}
		}
		$path = implode( DIRECTORY_SEPARATOR, $path_array ); 
		return( $path );  
	}

	/**
	 * Lazy setting of $this->path
	 *
	 * If args['src'] is specified we can build the path in reverse
	 * We need $path in order to be able to satisfy oik_require_file( $file, $library );
	 * We probably only need $src for the initial library load: oik_require_lib( $library );
	 * 
	 */
	function path() {
		if ( null === $this->path ) {
			$args_src = bw_array_get( $this->args, 'src', null );
			if ( $args_src ) {
				$this->path = dirname( $args_src );
			} else { 
				$this->path = $this->path_from_lib_args();
			}
		}
		if ( !is_dir( $this->path ) ) {
			$this->error( "directory missing", "Path is not a directory", $this );
		}
		return( $this->path );
	}

	/**
	 * Lazy setting of $this->src
	 *
	 * 
	 */
	function src() {
		if ( null === $this->src ) {
			$this->src = bw_array_get( $this->args, 'src', null );
		}
		if ( !$this->src ) {
			$path = $this->path();
			$file = $this->file();
			$this->src = $path;
			$this->src .= "/";
			$this->src .= $file;
			//$file = bw_array_get( $lib_args, 'file', null );
			//$plugin = bw_array_get( $lib_args, 'plugin', null );
			//$this->src = oik_path( $file, $plugin ); 
		}
		if ( !file_exists( $this->src ) ) {
			$this->error( "file missing", "File not found", $this );
		}
	}
	
	/**
	 * Return dependencies to check as array of library => version
	 * 
	 * If not already done create the dependency array for the library
	 * given a comma separated string, array of library:version or
	 * array of library => version
	 *
	 * Note: We don't expect libraries to just have numeric names.
	 * 
	 * @return mixed dependency array or null
	 */
	function deps() {
		if ( null === $this->deps ) {
			$deps = bw_array_get( $this->args, 'deps', null );
      if ( !is_array( $deps ) )	{
				if ( $deps ) {
					$deps = explode( ",", $deps );
				} else {
					$deps = array();
				}
			}
			bw_trace2( $deps, "deps", false );
			$deps_array = array();
			if ( count( $deps ) ) {
				foreach ( $deps as $key => $value ) {
					if ( is_numeric( $key ) ) {
						list( $key, $value ) = explode( ":", $value . ":*" );
					} 
					$deps_array[ $key ] = $value;
				}
			}	
			$this->deps = $deps_array;
		}
		if ( count( $this->deps ) ) {
			return( $this->deps );
		}	
    return( null );
	}

}
