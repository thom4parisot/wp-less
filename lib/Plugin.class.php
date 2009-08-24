<?php
if (!class_exists('BasePlugin'))
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

  /**
   * @static
   * @var Pattern used to match stylesheet files to process them as pure CSS
   */
  public static $match_pattern = '/\.less$/U';

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

    foreach ($wp_styles->queue as $style_id)
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
   * @version 1.0
   * @return WP_Styles styles instance
   */
  public function getStyles()
  {
    global $wp_styles;
    return $wp_styles;
  }

  /**
   * Process a single stylesheet
   * 
   * @author oncletom
   * @since 1.1
   * @version 1.1
   * @param string $handle
   * @param $force boolean If set to true, rebuild all stylesheets, without considering they are updated or not
   * @return WPLessStylesheet
   */
  public function processStylesheet($handle, $force = false)
  {
    $wp_styles = $this->getStyles();
    $stylesheet = new WPLessStylesheet($wp_styles->registered[$handle]);

    if ($force || $stylesheet->hasToCompile())
    {
      $stylesheet->save();
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
    $upload_dir = $this->configuration->getUploadDir();

    if (empty($styles))
    {
      return;
    }

    if (!wp_mkdir_p($upload_dir))
    {
      throw new WPLessException(sprintf('The upload dir folder (`%s`) is not writable from %s.', $upload_dir, get_class($this)));
    }

    WPLessStylesheet::$upload_dir = $this->configuration->getUploadDir();
    WPLessStylesheet::$upload_uri = $this->configuration->getUploadUrl();

    foreach ($styles as $style_id)
    {
      $this->processStylesheet($style_id, $force);
    }

    do_action('wp-less_plugin_process_stylesheets', $styles);
  }

  /**
   * Method to register hooks (and do it only once)
   * 
   * @author oncletom
   * @since 1.1
   * @version 1.0
   */
  public function registerHooks()
  {
    if ($this->is_hooks_registered)
    {
      return false;
    }

    if (!is_admin())
    {
      do_action('wp-less_init', $this);
      add_action('wp_print_styles', array($this, 'processStylesheets'));
    }
    else
    {
      do_action('wp-less_init_admin', $this);
    }

    return $this->is_hooks_registered = true;
  }
}
