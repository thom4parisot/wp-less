<?php
/*
Plugin Name: WP LESS
Description: LESS extends CSS with variables, mixins, operations and nested rules. This plugin magically parse all your <code>*.less</code> files queued with <code>wp_enqueue_style</code> in WordPress.
Author: Oncle Tom
Version: 1.0-dev
Author URI: http://case.oncle-tom.net/
Plugin URI: hhttp://wordpress.org/extend/plugins/wp-less/

  This plugin is released under version 3 of the GPL:
  http://www.opensource.org/licenses/gpl-3.0.html

If you are a theme author, include the other file, `bootstrap-theme.php`.
*/

require dirname(__FILE__).'/lib/Plugin.class.php';
$WPLessPlugin = WPPluginToolkitPlugin::create('WPLess', __FILE__);

if (!is_admin())
{
  add_action('wp_print_styles', array($WPLessPlugin, 'process'));
}
