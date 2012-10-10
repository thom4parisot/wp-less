<?php
if (!class_exists('WPPluginToolkitPlugin'))
{
  require dirname(__FILE__).'/vendor/plugin-toolkit/BasePlugin.class.php';
}

/**
 * WP LESS Plugin class
 *
 * @author oncletom
 * @package wp-less
 * @subpackage lib
 */
class WPLessPlugin extends WPPluginToolkitPlugin
{
  protected $is_filters_registered = false;
  protected $is_hooks_registered = false;
	protected $compiler = null;

  /**
   * @static
   * @var Pattern used to match stylesheet files to process them as pure CSS
   */
  public static $match_pattern = '/\.less$/U';

	public function __construct(WPLessConfiguration $configuration)
	{
		parent::__construct($configuration);

		$this->compiler = new WPLessCompiler;
		$this->compiler->setVariable('stylesheet_directory_uri', "'".get_stylesheet_directory_uri()."'");
		$this->compiler->setVariable('template_directory_uri', "'".get_template_directory_uri()."'");
	}

  /**
   * Dispatches all events of the plugin
   *
   * @author  oncletom
   * @since   1.3
   */
  public function dispatch()
  {
	  if ($this->is_hooks_registered)
	  {
		  return false;
	  }

	  /*
	   * Garbage Collection Registration
	   */
	  $gc = new WPLessGarbagecollector($this->configuration);
	  add_action('wp-less_garbage_collection', array($gc, 'clean'));

	  /*
	   * Last Hooks
	   */
	  $this->registerHooks();
  }

	/**
	 * Performs plugin install actions
	 *
	 * @since 1.5
	 */
	public function install()
	{
		wp_schedule_event(time(), 'daily', 'wp-less_garbage_collection');
	}

	/**
	 * Performs plugin uninstall actions
	 *
	 * @since 1.5
	 */
	public function uninstall()
	{
		wp_clear_scheduled_hook('wp-less_garbage_collection');
	}

  /**
   * Correct Stylesheet URI
   *
   * It enables the cache without loosing reference to URI
   *
   * @author oncletom
   * @since 1.2
   * @version 1.1
   * @param string $css parsed CSS
   * @param WPLessStylesheet Stylesheet currently processed
   * @return string parsed and fixed CSS
   */
  public function filterStylesheetUri($css, WPLessStylesheet $stylesheet)
  {
    $token = '@'.uniqid('wpless', true).'@';
    $css = preg_replace('#url\s*\(([\'"]{0,1})([^\'"\)]+)\1\)#siU', 'url(\1'.$token.'\2\1)', $css);

    /*
     * Token replacement:
     * - preserve data URI
     * - prefix file URI with absolute path to the theme
     */
    $css = str_replace(
      array($token.'data:', $token),
      array('data:', dirname($stylesheet->getSourceUri()).'/'),
    $css);

    return $css;
  }

  /**
   * Find any style to process
   *
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return array styles to process
   */
  protected function getQueuedStylesToProcess()
  {
    $wp_styles =  $this->getStyles();
    $to_process = array();

    foreach ((array)$wp_styles->queue as $style_id)
    {
      if (preg_match(self::$match_pattern, $wp_styles->registered[$style_id]->src))
      {
        $to_process[] = $style_id;
      }
    }

    return apply_filters('wp-less_get_queued_styles_to_process', $to_process);
  }

  /**
   * Returns WordPress Styles manager
   *
   * @author oncletom
   * @uses WP_Styles
   * @since 1.0
   * @version 1.1
   * @return WP_Styles styles instance
   */
  public function getStyles()
  {
    global $wp_styles;

    //because if someone never registers through `wp_(enqueue|register)_stylesheet`,
    //$wp_styles is never initialized, and thus, equals NULL
    return null === $wp_styles || !$wp_styles instanceof WP_Styles ? new WP_Styles() : $wp_styles;
  }

  /**
   * Process a single stylesheet
   *
   * @author oncletom
   * @since 1.1
   * @version 1.3
   * @param string $handle
   * @param $force boolean If set to true, rebuild all stylesheets, without considering they are updated or not
   * @return WPLessStylesheet
   */
  public function processStylesheet($handle, $force = false)
  {
	  $force = !!$force ? $force : $this->configuration->alwaysRecompile();

    $wp_styles = $this->getStyles();
    $stylesheet = new WPLessStylesheet($wp_styles->registered[$handle], $this->compiler->getVariables());

	  if ($this->configuration->getCompilationStrategy() === 'legacy' && $stylesheet->hasToCompile())
	  {
			$this->compiler->saveStylesheet($stylesheet);
	  }
    elseif ($this->configuration->getCompilationStrategy() !== 'legacy')
    {
	    $this->compiler->cacheStylesheet($stylesheet, $force);
    }

    $wp_styles->registered[$handle]->src = $stylesheet->getTargetUri();

    return $stylesheet;
  }

  /**
   * Process all stylesheets to compile just in time
   *
   * @author oncletom
   * @since 1.0
   * @version 1.1
   * @param $force boolean If set to true, rebuild all stylesheets, without considering they are updated or not
   */
  public function processStylesheets($force = false)
  {
    $styles =     $this->getQueuedStylesToProcess();
    $wp_styles =  $this->getStyles();
    $force = 			is_bool($force) && $force ? !!$force : false;

    WPLessStylesheet::$upload_dir = $this->configuration->getUploadDir();
    WPLessStylesheet::$upload_uri = $this->configuration->getUploadUrl();

    if (empty($styles))
    {
      return;
    }

    if (!wp_mkdir_p(WPLessStylesheet::$upload_dir))
    {
      throw new WPLessException(sprintf('The upload dir folder (`%s`) is not writable from %s.', WPLessStylesheet::$upload_dir, get_class($this)));
    }

    foreach ($styles as $style_id)
    {
      $this->processStylesheet($style_id, $force);
    }

    do_action('wp-less_plugin_process_stylesheets', $styles);
  }

  /**
   * Method to register hooks (and do it only once)
   *
   * @protected
   * @author oncletom
   * @since 1.1
   * @version 1.1
   */
  protected function registerHooks()
  {
    if ($this->is_hooks_registered)
    {
      return false;
    }

    if (!is_admin())
    {
      do_action('wp-less_init', $this);
      add_action('wp', array($this, 'processStylesheets'), 999, 0);
      add_filter('wp-less_stylesheet_save', array($this, 'filterStylesheetUri'), 10, 2);
    }
    else
    {
      do_action('wp-less_init_admin', $this);
    }

    return $this->is_hooks_registered = true;
  }

  /**
   * Proxy method
   *
   * @see http://leafo.net/lessphp/docs/#setting_variables_from_php
   * @since 1.4
   */
  public function addVariable($name, $value)
  {
    $this->compiler->setVariables(array( $name => $value ));
  }

  /**
   * Proxy method
   *
   * @see http://leafo.net/lessphp/docs/#setting_variables_from_php
   * @since 1.4
   */
  public function setVariables(array $variables)
  {
    $this->compiler->setVariables($variables);
  }

  /**
   * Proxy method
   *
   * @see http://leafo.net/lessphp/docs/#custom_functions
   * @since 1.4.2
   */
  public function registerFunction($name, $callback)
  {
    $this->compiler->registerFunction($name, $callback);
  }

  /**
   * Proxy method
   *
   * @see WPLessConfiguration::unregisterFunction()
   * @since 1.4.2
   */
  public function unregisterFunction($name)
  {
    $this->compiler->unregisterFunction($name);
  }
}
