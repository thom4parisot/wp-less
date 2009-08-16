<?php
/**
 * Base plugin class to extend
 * 
 * @author oncletom
 * @package plugin-toolkit
 */
abstract class WPPluginToolkitPlugin
{
  protected $configuration;
  protected static $autoload_configured = false;

  /**
   * Plugin constructor
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @param WPPluginToolkitConfiguration $configuration
   */
  public function __construct(WPPluginToolkitConfiguration $configuration)
  {
    $this->configuration = $configuration;

    if (!self::$autoload_configured)
    {
      spl_autoload_register(array($this, 'configureAutoload'));
    }

    do_action($this->configuration->getUnixName().'_plugin_construct', $this);
  }

  /**
   * Autoloads classes for this plugin
   * 
   * @author oncletom
   * @return boolean
   * @param string $className
   * @version 1.0
   * @since 1.0
   */
  public function configureAutoload($className)
  {
    $prefix = $this->configuration->getPrefix();

    if (!preg_match('/^'.$prefix.'/U', $className))
    {
      return;
    }

    $libdir = $this->configuration->getDirname().'/lib';
    $path = preg_replace('/([A-Z]{1})/U', "/$1", str_replace($prefix, '', $className)).'.class.php';

    if (file_exists($libdir.$path))
    {
      require $libdir.$path;
    }

    return false;
  }

  /**
   * WordPress plugin builder
   * 
   * @author oncletom
   * @static
   * @final
   * @since 1.0
   * @version 1.0
   * @param string $baseClassName
   * @param string $baseFileName
   * @return $baseClassName+Plugin instance
   */
  public final static function create($baseClassName, $baseFileName)
  {
    require_once dirname(__FILE__).'/BaseConfiguration.class.php';
    require_once dirname($baseFileName).'/lib/Configuration.class.php';

    $class =          $baseClassName.'Plugin';
    $configuration =  $baseClassName.'Configuration';

    list($class, $configuration) = apply_filters('plugin-toolkit_create', array($class, $configuration));

    $object = new $class(new $configuration($baseClassName, $baseFileName));
    do_action('plugin-toolkit_create', $object);

    return $object;
  }

  /**
   * Returns the current configuration
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return WPPluginToolkitConfiguration instance
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }
}
