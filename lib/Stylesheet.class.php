<?php
require dirname(__FILE__).'/vendor/lessphp/lessc.inc.php';

/**
 * Stylesheet management
 *
 * @author oncletom
 * @package wp-less
 * @subpackage lib
 */
class WPLessStylesheet
{
  protected $compiler,
            $stylesheet;

  protected $is_new = true,
            $source_path,
            $source_uri,
            $target_path,
            $target_timestamp,
            $target_uri;

  public static $upload_dir,
                $upload_uri;

  /**
   * Constructs the object, paths and all
   *
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @throws WPLessException if something is not properly configured
   * @param _WP_Dependency $stylesheet
   */
  public function __construct(_WP_Dependency $stylesheet)
  {
    $this->stylesheet = $stylesheet;

    if (!self::$upload_dir || !self::$upload_uri)
    {
      throw new WPLessException('You must configure `upload_dir` and `upload_uri` static attributes before constructing this object.');
    }

    $this->configurePath();
    $this->configureVersion();

    do_action('wp-less_stylesheet_construct', $this);
  }

  /**
   * Returns the computed path for a given dependency
   *
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function computeTargetPath()
  {
    $target_path = preg_replace('#^'.get_theme_root_uri().'#U', '', $this->stylesheet->src);
    $target_path = preg_replace('/.less$/U', '', $target_path);

    $target_path .= '.css';

    return apply_filters('wp-less_stylesheet_compute_target_path', $target_path);
  }

  /**
   * Configure paths for the stylesheet
   * Since this moment, everything is configured to be usable
   *
   * @protected
   * @author oncletom
   * @since 1.0
   * @version 1.0
   */
  protected function configurePath()
  {
    $target_file =          $this->computeTargetPath();

    $this->source_path =    WP_CONTENT_DIR.preg_replace('#^'.WP_CONTENT_URL.'#U', '', $this->stylesheet->src);
    $this->source_uri =     $this->stylesheet->src;
    $this->target_path =    self::$upload_dir.$target_file;
    $this->target_uri =     self::$upload_uri.$target_file;
  }

  /**
   * Configures version and timestamp
   *
   * It can be run only after paths have been configured. Otherwise (or if the calculation went wrong),
   * an exception will be thrown.
   *
   * @author oncletom
   * @since 1.2
   * @version 1.0
   * @throws WPLessException
   */
  public function configureVersion()
  {
    if (!$this->getTargetPath())
    {
      throw new WPLessException("Can't configure any version if there is no target path.");
    }

    if (file_exists($this->getTargetPath()))
    {
      $this->is_new = false;
      $this->target_timestamp = filemtime($this->getTargetPath());
    }
    else
    {
      $this->is_new = true;
      $this->target_timestamp = time();
    }

    $this->stylesheet->ver = $this->target_timestamp;
  }

  /**
   * Returns source content (CSS to parse)
   *
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getSourceContent()
  {
    return apply_filters('wp-less_stylesheet_source_content', file_get_contents($this->source_path));
  }

  /**
   * Returns source path
   *
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getSourcePath()
  {
    return $this->source_path;
  }

  /**
   * Returns source URI
   *
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getSourceUri()
  {
    return $this->source_uri;
  }

  /**
   * Returns target path
   *
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getTargetPath()
  {
    return $this->target_path;
  }

  /**
   * Returns target URI
   *
   * @author oncletom
   * @since 1.0
   * @version 1.1
   * @param boolean $append_version
   * @param string  $version_prefix
   * @return string
   */
  public function getTargetUri($append_version = false, $version_prefix = '?ver=')
  {
    return $this->target_uri.(!!$append_version ? $version_prefix.$this->target_timestamp : '');
  }

  /**
   * Tells if compilation is needed
   *
   * @author oncletom
   * @since 1.0
   * @version 1.1
   * @return boolean
   */
  public function hasToCompile()
  {
    //context compilation
    if ($this->is_new || (defined('WP_DEBUG') && WP_DEBUG))
    {
      return true;
    }

    //registered stylesheet has been updated
    if (filemtime($this->getSourcePath()) > $this->target_timestamp)
    {
      return true;
    }

    return false;
  }

  /**
   * Save the current stylesheet as a parsed css file
   *
   * @author oncletom
   * @since 1.0
   * @version 1.3
   * @throws Exception in case of parsing went bad
   */
  public function save(array $variables = array())
  {
    wp_mkdir_p(dirname($this->getTargetPath()));

    try
    {
      do_action('wp-less_stylesheet_save_pre', $this, $variables);
      $compiler = new WPLessCompiler($this->getSourcePath());

      file_put_contents($this->getTargetPath(), apply_filters('wp-less_stylesheet_save', $compiler->parse(null, $variables), $this));
      chmod($this->getTargetPath(), 0666);

      $this->is_new = false;
      do_action('wp-less_stylesheet_save_post', $this);
    }
    catch(Exception $e)
    {
      wp_die($e->getMessage());
    }
  }
}
