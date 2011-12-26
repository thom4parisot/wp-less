<?php

class WPLessConfiguration extends WPPluginToolkitConfiguration
{
  /**
   * Refers to the name of the plugin
   */
  const UNIX_NAME = 'wp-less';

  /**
   * Refers to the version of the plugin
   */
  const VERSION =   '1.4';

  /**
   * @protected
   */
  protected $variables = array();


  protected function configure()
  {
    $this->configureOptions();
  }

  protected function configureOptions()
  {
    $this->setVariables(array());
  }

  /**
   * Set global Less variables
   * 
   * @since 1.4
   */
  public function addVariable($name, $value)
  {
    $this->variables[$name] = $value;
  }

  /**
   * Returns the registered variables
   * 
   * @since 1.4
   * @return array
   */
  public function getVariables()
  {
    return $this->variables;
  }

  /**
   * Set global Less variables
   * 
   * @since 1.4
   */
  public function setVariables(array $variables)
  {
    $this->variables = $variables;
  }
}
