=== oik-lib ===
Contributors: bobbingwide, vsgloik
Donate link: http://www.oik-plugins.com/oik/oik-donate/
Tags: library, boot, shared, trace, Must-Use, dependency, version
Requires at least: 4.2
Tested up to: 4.3
Stable tag: 0.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: oik-lib
Domain Path: /languages/

== Description ==
Shared library management for WordPress plugins.

= Background =

In June 2015, other than extension plugins, very few WordPress plugins shared common logic.
Hardly any implemented plugin dependency logic.

Plugins that used the same code had two choices:

1. Make the code subtly different; by function name, namespace or classes
1. Carefully manage the duplicated functions


Conversely, many developers were using Composer to satisfy requirements for libraries used by "plugins".
Show evidence...



The oik-lib plugin is intended to support an API whereby plugins can request library functions
without worrying too much about the source of the functions, or even the implementation.

The plugin is intended to be easy to install and maintain and does not require SSH access to be used.



= Requirements = 

* Ability to request a library
* Notification actions when dependencies satisfied
* Plugin dependency checking
* Shared delivery of library functions
* Applicable to plugins and themes
* Fallback support for standalone plugins
* Version checking
* Support Composer packages

See [oik-lib - requirements summary](http://www.oik-plugins.com/wordpress-plugins-from-oik-plugins/free-oik-plugins/oik-lib-shared-library-management/oik-lib-requirements-summary/)

= Implementation =

There are some library functions that are so crucial that these need to be available all the time.

While they are not part of WordPress core, these functions will need to be implemented in such a way that they can be available 
for use by any plugin or theme at all times.

This plugin achieves this by becoming a MU plugin.

It provides the basic set of APIs to allow each plugin to get involved with sharing.

The APIs are provided in library files which can be bundled in standalone plugins.

Multiple plugins may deliver the library functions, but only one of them will be used.


The logic will attempt to take into account a variety of ways of indicating the presence of library functions.
Advanced logic, dynamically loadable on demand, will be used to attempt to resolve issues with inactive code or incompatible versions.



== Installation ==
1. Upload the contents of the oik-lib plugin to the `/wp-content/plugins/oik-lib' directory
1. Activate the oik-lib plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= Where are the FAQs =
See the official plugin documentation at [oik-plugins](http://oik-plugins.com/oik-plugins/oik-lib-shared-library-management)


== Screenshots ==
1. oik-lib in action

== Upgrade Notice ==
= 0.0.4 =
Synchronized with oik-bwtrace v2.0.7. 
Please also upgrade the oik base plugin and oik-bwtrace.

= 0.0.3 =
Upgrade for support for Composer packages. See oik-fum sample plugin.
Please also upgrade oik and oik-bwtrace.

= 0.0.2 =
Upgrade to get better support for plugins performing dependency checking

= 0.0.1 =
First version for WordPress PHP library management  


== Changelog ==
= 0.0.4 =
* Changed: Synchronized with oik-bwtrace v2.0.7
* Changed: Synchronized with oik v3.0.0-alpha.0917

= 0.0.3 =
* Added: Add screenshot of the oik-lib admin page
* Added: oik-admin, bobbforms and bobbfunc for admin page
* Added: oik_require_file() API uses OIK_libs::require_file() method
* Added: support for Composer libraries with multiple files
* Changed: Delivers and loads oik-admin shared library
* Changed: Don't display the "error" column
* Changed: set oik-depends library to v3.0.0
* Changed: some comment changes

= 0.0.2 = 
* Fixed: Corrected logic around plugin dependency checking
* Changed: libs/bwtrace.php now at 2.0.1
* Changed: libs/oik-lib.php now at 0.0.2
* Changed: libs/oik_boot.php now at 3.0.0
* Changed: Now dependent upon bobbfunc:3.0.0 - for bw_as_array()
* Changed: Implements hook for admin_notices earlier; now priority 8, was 9.
* Changed: Corrected names of the default libraries in the comments
* Changed: Now dependent upon bobbfunc v3.0.0
* Fixed: oik_lib_init() uses load_plugin_textdomain() instead of bw_load_plugin_textdomain()
* Changed: Defers admin checks until "wp_loaded". Now tests for WP_errors in the response
* Changed: Implements "oik_query_libs" with a lower priority than oik

= 0.0.1 =
* Added: New plugin


== Further reading ==
If you want to read more about the oik plugins then please visit the
[oik plugin](http://www.oik-plugins.com/oik) 
**"the oik plugin - for often included key-information"**





