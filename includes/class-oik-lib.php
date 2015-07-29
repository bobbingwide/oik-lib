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
	 */
	public $library;
	
	/**
	 * Source file
	 * 
	 * Fully qualified file name for the library source file
	 * Constructed from $args['plugin'] and $args['file'] if not specified in $args['src']
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
	 * Return an instance of a library object
	 *
	 * Logic is invoked Only When Necessary
	 * So we only set the minimum number of properties and leave the rest of the values in $args.
	 *
	 * @TODO Defer setting of $this->src until really necessary
	 *
	 * @param array $lib_args array of library properties as above
	 * @return the instance of the object
	 */ 
	function __construct( $lib_args ) {
		$this->library = bw_array_get( $lib_args, 'library', null );
		$this->src = bw_array_get( $lib_args, 'src', null );
		if ( !$this->src ) {
			$file = bw_array_get( $lib_args, 'file', null );
			$plugin = bw_array_get( $lib_args, 'plugin', null );
			$this->src = oik_path( $file, $plugin ); 
		}
		$this->deps = null;
		$this->version = bw_array_get( $lib_args, 'version', null );
		//$this->init_function = bw_array_get( $lib_args, 'init_function', null );
		$this->args = $lib_args;
		
		return( $this );
	
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
				$deps = explode( ",", $deps );
			}
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
