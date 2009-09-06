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

  protected $source_path,
            $source_uri,
            $target_path,
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
   * Returns parsed CSS
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return string
   */
  public function getTargetContent()
  {
    if (!$this->compiler)
    {
      $this->compiler = new lessc($this->getSourcePath());
    }

    return apply_filters('wp-less_stylesheet_target_content', $this->compiler->parse());
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
   * @version 1.0
   * @return string
   */
  public function getTargetUri()
  {
    return $this->target_uri;
  }

  /**
   * Tells if compilation is needed
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.0
   * @return boolean
   */
  public function hasToCompile()
  {
    return !file_exists($this->getTargetPath()) || filemtime($this->getSourcePath()) > filemtime($this->getTargetPath());
  }

  /**
   * Save the current stylesheet as a parsed css file
   * 
   * @author oncletom
   * @since 1.0
   * @version 1.1
   * @throws Exception in case of parsing went bad
   */
  public function save()
  {
    wp_mkdir_p(dirname($this->getTargetPath()));

    try
    {
      do_action('wp-less_stylesheet_save_pre', $this);
      $compiler = new WPLessCompiler($this->getSourcePath());

      $output = apply_filters('wp-less_stylesheet_parse', $compiler->parse(), $this);
      file_put_contents($this->getTargetPath(), $output);
      chmod($this->getTargetPath(), 0666);

      do_action('wp-less_stylesheet_save_post', $this);
    }
    catch(Exception $e)
    {
      wp_die($e->getMessage());
    }
  }
}
