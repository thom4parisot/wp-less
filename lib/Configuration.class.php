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
  const VERSION =   '1.5-dev';

  /**
   * @protected
   * @see http://leafo.net/lessphp/docs/index.html#custom_functions
   */
  protected $functions = array();


  protected function configure()
  {
	  $this->configureOptions();
  }

	protected function configureOptions()
	{
		//
	}
}
