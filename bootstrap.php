<?php
/*
Plugin Name: WP LESS
Description: LESS extends CSS with variables, mixins, operations and nested rules. This plugin magically parse all your <code>*.less</code> files queued with <code>wp_enqueue_style</code> in WordPress.
Author: thom4
Author URI: https://thom4.net/
Plugin URI: https://github.com/thom4parisot/wp-less/
License: Apache-2.0
*/

if (!class_exists('WPLessPluginLoader'))
{
	require dirname(__FILE__).'/lib/Loader.class.php';
	$WPLessPlugin = WPLessPluginLoader::load(function($WPLessPlugin) {
		register_activation_hook(__FILE__, array($WPLessPlugin, 'install'));
		register_deactivation_hook(__FILE__, array($WPLessPlugin, 'uninstall'));

		$WPLessPlugin->dispatch();
	});
}
