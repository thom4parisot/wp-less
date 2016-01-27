<?php
if (!class_exists('WPLessPlugin')) {
  require dirname(__FILE__) . '/Plugin.class.php';
}

/**
 * Wrapper for plugin loading
 *
 * @author oncletom
 * @author mrgrain
 * @package wp-less
 * @subpackage lib
 */
class WPLessPluginLoader
{
  /**
   * @param callable|null $bootstrap
   * @return mixed
     */
  public static function load($bootstrap = null)
  {
    // Create Plugin Instance
    $WPLessPlugin = WPPluginToolkitPlugin::create('WPLess', dirname(__FILE__), 'WPLessPlugin');

    // Run bootstrap closure and return plugin
    if (is_callable($bootstrap)) {
      $bootstrap($WPLessPlugin);
    }

    return $WPLessPlugin;
  }

  /**
   * @param callable|null $bootstrap
   * @return mixed|object
     */
  public static function getInstance($bootstrap = null)
  {
    // Try to get existing instance
    $WPLessPlugin = WPLessPlugin::getInstance();

    // Create a new instance if needed
    if (is_null($WPLessPlugin)) {
      $WPLessPlugin = self::load($bootstrap);
    }

    return $WPLessPlugin;
  }
}
