=== WP-LESS ===
Contributors: oncletom
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=752034
Tags: dev, theme, themes, toolkit, plugin-toolkit, less, lesscss, lessc, lessphp, productivity, style, stylesheet, api
Requires at least: 2.8
Tested up to: 2.8.x
Stable tag: 1.1

Implementation of LESS (Leaner CSS) in order to make themes development easier.


== Description ==
[LESS](http://lesscss.org) is a templating language based on top of CSS. It provides numerous enhancements to speed up development and make its maintenance easier.

Theme developers can even bundle the plugin without worrying about conflicts: just include the special `bootstrap-for-theme.php` and read its instructions.

= Features =

 * Variables
 * Mixins (inheritance of rules)
 * Nested Rules (write less, do more)
 * Accessors (inherit a value from a specific rule)
 * Functions (logic operations for dynamic results)

The plugin lets you concentrate on what you need: coding CSS. Everything else is handled automatically, from cache management to user delivery.  
Seriously.

= Requirements =

The sole requirement is to use WordPress API and LESS convention: the `.less` extension.

**Minimal Requirements**: PHP 5.1.2 and WordPress 2.8.  
**Relies on**: [LESSPHP 0.1.6](http://leafo.net/lessphp/), [plugin-toolkit](http://wordpress.org/extend/plugins/plugin-toolkit/).

*Notice*: in case you'd like to drop the usage of this plugin, it's safe to do it. You will just need to convert back your stylesheets to CSS.

== Installation ==

= Automatic =
 1. Search for the plugin name (`WP-LESS`)
 1. Click on the install button
 1. Activate it

= Manual =
 1. Download the latest stable archive of the plugin
 1. Unzip it in your plugin folder (by default, `wp-content/plugins`)
 1. Activate it through your WordPress plugins administration page

== Changelog ==
= Version 1.1 =
 * added `bootstrap-for-theme.php` to let themers bundle the plugin in their own themes
 * added `WPLessPlugin::registerHooks` methods to ease hooks activation
 * theme bootstrap will only load if the plugin is not alread activated
 * `WPLessPlugin::processStylesheets()` and `WPLessPlugin::processStylesheet()` now accepts an additional parameter to force the rebuild
 * lessphp: updated to version 0.1.6
 * plugin-toolkit: updated to version 1.1


= Version 1.0 =

 * implemented API to let you control the plugin the way you want
 * just in time compilation with static file caching
 * lessphp: bundled to version 0.1.6
 * plugin-toolkit: bundled experimental plugin development


== Frequently Asked Questions ==
= How do I transform a LESS file into CSS? =
Consider this bit of code to automatically enqueue your stylesheet from your theme (or plugin):  
`wp_enqueue_style('mytheme', get_bloginfo('template_directory').'/style.css', array('blueprint'), '', 'screen, projection');`

To make it process by WP-LESS, switch to this way:  
`wp_enqueue_style('mytheme', get_bloginfo('template_directory').'/style.less', array('blueprint'), '', 'screen, projection');`

You understood well: you just need to change the extension of the file.

= And if I don't use the wp_enqueue_style method? =
For the moment, it's the unique way to handle this.  
Helpers will be provided soon to include LESS files in your templates in a fluent way.

= What if a *.less file contains only pure CSS? =
Nothing special. The LESS parser is fully compliant with CSS syntax.  
It means nothing will be broken so don't worry.

= I'm a themer and I don't want to ask my users to activate this plugin =
It's a very good moto. Since the 1.1 release, there is a special bootstrap file: `bootstrap-for-theme.php`.  
Everything is prepared and documented inside, with examples and hint.

Just help yourself!

== Screenshots ==

1. Sample of LESS to CSS conversion.