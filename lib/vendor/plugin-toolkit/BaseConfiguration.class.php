<?php

abstract class WPPluginToolkitConfiguration
{
  const UNIX_NAME = null;
  const I18N_DIR =  'i18n';

  protected $base_class_name,
            $base_dirname,
            $base_filename,
            $filename,
            $i18n_path,
            $i18n_path_from_plugins,
            $options,
            $plugin_path;

  abstract protected function configure();
  abstract protected function configureOptions();

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

  public function getPrefix()
  {
    return $this->base_class_name;
  }

  public function getPluginPath()
  {
    return $this->plugin_path;
  }  

  /**
   * Build paths for various access
   * 
   * @author oncletom
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
      $this->filename =                 WP_PLUGIN_DIR.'/'.$unix_name.'/'.basename($initial_path);
      $this->i18n_path =                PLUGINDIR.'/'.$unix_name.'/i18n';
      $this->i18n_path_from_plugins =   $unix_name.'/i18n';
    }
    else
    {
      $this->filename =                 $this->base_filename;
      $this->i18n_path =                PLUGINDIR.'/'.dirname(plugin_basename($initial_path)).'/i18n';
      $this->i18n_path_from_plugins =   dirname(plugin_basename($initial_path)).'/i18n';
    }

    $this->plugin_path = preg_replace('#(.+)([^/]+/[^/]+)$#sU', "$2", $filename);
  }
}
