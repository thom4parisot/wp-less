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
  public static $match_pattern = '/\.less$/U';

  public function processStylesheets()
  {
    $styles = $this->getQueuedStylesToProcess();

    if (empty($styles))
    {
      return;
    }
    
    var_dump($styles);
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

    foreach ($wp_styles->queue as $style_id)
    {
      if (preg_match(self::$match_pattern, $wp_styles->registered[$style_id]->src))
      {
        $to_process[] = $style_id;
      }
    }

    return $to_process;
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
}
