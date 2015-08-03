=== oik-lib ===
Contributors: bobbingwide, vsgloik
Donate link: http://www.oik-plugins.com/oik/oik-donate/
Tags: library, boot
Requires at least: 4.2
Tested up to: 4.3-RC1
Stable tag: 0.0.1
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

* Shared delivery of library functions
* Notification actions when libraries become available
* API to request libraries
* Automatic dependency resolution
* Automatic conflicy resolution
* Crucial / critical functionality to be always available


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
= 0.0.2 =
Upgrade to get better support for plugins performing dependency checking


= 0.0.1 =
First version for WordPress PHP library management  


== Changelog == 
= 0.0.2 = 
* Fixed: Corrected logic around plugin dependency checking

= 0.0.1 =
* Added: New plugin


== Further reading ==
If you want to read more about the oik plugins then please visit the
[oik plugin](http://www.oik-plugins.com/oik) 
**"the oik plugin - for often included key-information"**


Notes on BackPress

The development of the oik-lib plugin was not influenced by BackPress but in some respects appears
remarkably similar at some base levels. 

There are other projects similar to oik-lib. 
See the FAQs for more information.





