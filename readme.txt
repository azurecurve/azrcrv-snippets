=== Snippets ===

Description:	Allows snippets of HTML, PHP, JavaScript and CSS to be created; an alternative to using a functions.php file.
Version:		2.0.2
Tags:			HTML,PHP,JavaScript,CSS
Author:			azurecurve
Author URI:		https://development.azurecurve.co.uk/
Plugin URI:		https://development.azurecurve.co.uk/classicpress-plugins/snippets/
Download link:	https://github.com/azurecurve/azrcrv-snippets/releases/download/v2.0.2/azrcrv-snippets.zip
Donate link:	https://development.azurecurve.co.uk/support-development/
Requires PHP:	5.6
Requires:		1.0.0
Tested:			4.9.99
Text Domain:	snippets
Domain Path:	/languages
License: 		GPLv2 or later
License URI: 	http://www.gnu.org/licenses/gpl-2.0.html

Allows snippets of HTML, PHP, JavaScript and CSS to be created; an alternative to using a functions.php file or adding styles to a child theme.

== Description ==

# Description

Allows snippets of HTML, PHP, JavaScript and CSS to be created; an alternative to using a functions.php file or adding styles to a child theme.

Snippets can be used to create re-usable HTML or JavaScript snippets or to create PHP to add_actions or add_filters without needing to add them to the functions.php file or create a plugin.

The following types of snippet can be created:
* HTML - can be loaded using the shortcode.
* Internal CSS - automatically added as internal stylesheet.
* CSS Stylesheet - automatically loaded.
* Internal JavaScript - automatically added as internal JavaScript.
* JavaScript File - automatically loaded.
* PHP - can be loaded using the shortcode.
* PHP File - automatically loaded.

Shortcode usage is either *[snippet id=1013]* (where the supplied id value is a snippet post_id) or *[snippet slug='hello-world']*.

All snippets are loaded only on the site frontend; this protects the admin dashboard from white screen errors caused by badly formed PHP.

== Installation ==

# Installation Instructions

 * Download the plugin from [GitHub](https://github.com/azurecurve/azrcrv-snippets/releases/latest/).
 * Upload the entire zip file using the Plugins upload function in your ClassicPress admin panel.
 * Activate the plugin.
 * Configure relevant settings via the settings page in the admin control panel (azurecurve menu).

== Frequently Asked Questions ==

# Frequently Asked Questions

### Can I translate this plugin?
Yes, the .pot fie is in the plugins languages folder and can also be downloaded from the plugin page on https://development.azurecurve.co.uk; if you do translate this plugin, please sent the .po and .mo files to translations@azurecurve.co.uk for inclusion in the next version (full credit will be given).

### Is this plugin compatible with both WordPress and ClassicPress?
This plugin is developed for ClassicPress, but will likely work on WordPress.

== Changelog ==

# Changelog

### [Version 2.0.2](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v2.0.2)
 * Fix bug with load of PHP files.

### [Version 2.0.1](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v2.0.1)
 * Fix bug with load of snippet folder option.

### [Version 2.0.0](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v2.0.0)
 * Add missing snippet url setting; this is a breaking change requiring settings to be updated.
 * Add uninstall.
 * Update translations to escape strings.
 * Update azurecurve menu and logo.

### [Version 1.4.0](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v1.4.0)
 * Set priority on enqueue of CSS and JavaScript.
 
### [Version 1.3.0](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v1.3.0)
 * Replace admin menu icon with svg.
 
### [Version 1.2.0](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v1.2.0)
 * Add advanced mode toggle; until enabled PHP/JavaScript snippets and files are unavailable for creation, but existing ones will continue to function.

### [Version 1.1.1](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v1.1.1)
 * Fix bug when deleting post.
 
### [Version 1.1.0](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v1.1.0)
 * Amend to ensure snippets are not visible or accessible from the front end.
 * Update azurecurve menu.
 * Fix bug in azurecurve menu.

### [Version 1.0.1](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v1.0.1)
 * Fix bug with internal CSS and JavaScript not loading correctly.

### [Version 1.0.0](https://github.com/azurecurve/azrcrv-snippets/releases/tag/v1.0.0)
 * Initial release.

== Other Notes ==

# About azurecurve

**azurecurve** was one of the first plugin developers to start developing for Classicpress; all plugins are available from [azurecurve Development](https://development.azurecurve.co.uk/) and are integrated with the [Update Manager plugin](https://codepotent.com/classicpress/plugins/update-manager/) by [CodePotent](https://codepotent.com/) for fully integrated, no hassle, updates.

Some of the top plugins available from **azurecurve** are:
* [Add Twitter Cards](https://development.azurecurve.co.uk/classicpress-plugins/add-twitter-cards/)
* [Breadcrumbs](https://development.azurecurve.co.uk/classicpress-plugins/breadcrumbs/)
* [Series Index](https://development.azurecurve.co.uk/classicpress-plugins/series-index/)
* [To Twitter](https://development.azurecurve.co.uk/classicpress-plugins/to-twitter/)
* [Theme Switcher](https://development.azurecurve.co.uk/classicpress-plugins/theme-switcher/)
* [Toggle Show/Hide](https://development.azurecurve.co.uk/classicpress-plugins/toggle-showhide/)