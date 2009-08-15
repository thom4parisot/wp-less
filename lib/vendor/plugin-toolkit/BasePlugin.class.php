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
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @param WPPluginToolkitConfiguration $configuration
   * @return 
   */
  public function __construct(WPPluginToolkitConfiguration $configuration)
  {
    $this->configuration = $configuration;

    if (!self::$autoload_configured)
    {
      spl_autoload_register(array($this, 'configureAutoload'));
    }
  }

  /**
   * Autoloads classes for this plugin
   * 
   * @author oncletom
   * @static
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
      return false;
    }

    $dirname = $this->configuration->getPluginPath();
    $filename = str_replace($prefix, '', $className).'.class.php';

    /*
     * Direct File
     */
    if (file_exists($dirname.'/'.$filename))
    {
      require $dirname.'/'.$filename;
    }
    /*
     * Subdir
     */
    else if (preg_match('/^([A-Z]{1}[a-z0-9]+)[A-Z]/U', $filename, $matches))
    {
      require $dirname.'/'.strtolower($matches[1]).'/'.str_replace($matches[1], '', $filename);
    }

    return true;
  }

  /**
   * WordPress plugin builder
   * 
   * @author oncletom
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

    $object = new $class(new $configuration($baseClassName, $baseFileName));

    return $object;
  }

  /**
   * Returns the current configuration
   * 
   * @author oncletom
   * @return WPPluginToolkitConfiguration instance
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }
}
