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

	/**
	 * @protected
	 * @deprecated
	 * @var bool
	 */
	protected $alwaysRecompile = false;


  protected function configure()
  {
    $this->configureOptions();
  }

  protected function configureOptions()
  {
    $this->setVariables(array());
	  $this->alwaysRecompile((defined('WP_DEBUG') && WP_DEBUG) || (defined('WP_LESS_ALWAYS_RECOMPILE') && WP_LESS_ALWAYS_RECOMPILE));
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

	/**
	 * Set compilation strategy
	 *
	 * @param $bFlag bool
	 * @return bool Actual compilation "strategy"
	 */
	public function alwaysRecompile($bFlag = null)
	{
		if (!is_null($bFlag))
		{
			$this->alwaysRecompile = !!$bFlag;
		}

		return $this->alwaysRecompile;
	}

  /**
   * Return LESS functions
   *
   * @since 1.4.2
   * @return array
   */
  public function getFunctions()
  {
    return $this->functions;
	}
}
