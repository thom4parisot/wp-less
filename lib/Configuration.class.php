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
	  $this->alwaysRecompile((defined('WP_DEBUG') && WP_DEBUG) || (defined('WP_LESS_ALWAYS_RECOMPILE') && WP_LESS_ALWAYS_RECOMPILE));
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
}
