<?php

abstract class WPPluginToolkitConfiguration
{
  const UNIX_NAME = null;
  const I18N_DIR =  'i18n';

  protected $base_class_name,
            $base_dirname,
            $base_filename,
            $dirname,
            $filename,
            $i18n_path,
            $i18n_path_from_plugins,
            $options,
            $plugin_path;

  /**
   * Launch the configure process
   * It is generally totally specific to each plugin.
   * 
   * @author oncletom
   * @protected
   */
  protected function configure()
  {
    $this->configureOptions();
  }

  /**
   * Let the plugin configure its own options
   * 
   * @author oncletom
   * @abstract
   * @protected
   */
  abstract protected function configureOptions();

  /**
   * Base constructor for a plugin configuration
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @param string $baseClassName
   * @param string $baseFileName
   */
  public function __construct($baseClassName, $baseFileName)
  {
    $unix_name_pattern = $baseClassName.'Configuration::UNIX_NAME';
    if (is_null(constant($unix_name_pattern)))
    {
      throw new Exception(sprintf('%s has not been configured for %sConfiguration.', $unix_name_pattern, $baseClassName));
    }

    $this->base_class_name =  $baseClassName;
    $this->setupPath($baseFileName, constant($unix_name_pattern));
    //$this->options = new $baseClassName.'OptionCollection';

    $this->configure();
  }

  /**
   * Returns resolved plugin full path location
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getDirname()
  {
    return $this->dirname;
  }

  /**
   * Returns resolved plugin full path filename
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }

  /**
   * Returns plugin prefix for classes
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getPrefix()
  {
    return $this->base_class_name;
  }

  /**
   * Returns resolved plugin path location, from plugin path
   * 
   * In theory, it's the same as Unix path but in fact, if the plugin is renamed it can helps
   * Not very used yet, though.
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getPluginPath()
  {
    return $this->plugin_path;
  }  

  /**
   * Build paths for various access
   * 
   * @author oncletom
   * @protected
   * @since 1.0
   * @version 1.0
   * @param string $baseFileName
   * @param string $unix_name
   */
  protected function setupPath($baseFileName, $unix_name)
  {
    $this->base_filename =    $baseFileName;
    $this->base_dirname =     dirname($baseFileName);

    if (function_exists('is_link') && is_link(WP_PLUGIN_DIR.'/'.$unix_name))
    {
      $this->filename =                 WP_PLUGIN_DIR.'/'.$unix_name.'/'.basename($this->base_filename);
      $this->i18n_path =                PLUGINDIR.'/'.$unix_name.'/i18n';
      $this->i18n_path_from_plugins =   $unix_name.'/i18n';
    }
    else
    {
      $this->filename =                 $this->base_filename;
      $this->i18n_path =                PLUGINDIR.'/'.dirname(plugin_basename($this->filename)).'/i18n';
      $this->i18n_path_from_plugins =   dirname(plugin_basename($this->filename)).'/i18n';
    }

    $this->dirname =      dirname($this->filename);
    $this->plugin_path =  preg_replace('#(.+)([^/]+/[^/]+)$#sU', "$2", $this->filename);
  }
}
