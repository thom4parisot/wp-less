<?php
require dirname(__FILE__).'/vendor/lessphp/lessc.inc.php';

/**
 * LESS compiler
 *
 * @author oncletom
 * @extends lessc
 * @package wp-less
 * @subpackage lib
 * @since 1.2
 * @version 1.3
 */
class WPLessCompiler extends lessc
{
	/**
	 * Instantiate a compiler
	 *
   * @api
	 * @see	lessc::__construct
	 * @param $file	string [optional]	Additional file to parse
	 */
	public function __construct($file = null)
	{
  	do_action('wp-less_compiler_construct_pre', $this, $file);
		parent::__construct(apply_filters('wp-less_compiler_construct', $file));
	}

  /**
   * Parse a LESS file
   *
   * @api
   * @see lessc::parse
   * @deprecated
   * @throws Exception
   * @param string $text [optional] Custom CSS to parse
   * @param array $variables [optional] Variables to inject in the stylesheet
   * @return string CSS output
   */
  public function parse($text = null, $variables = null)
  {
  	do_action('wp-less_compiler_parse_pre', $this, $text, $variables);
    return apply_filters('wp-less_compiler_parse', parent::parse($text, $variables));
  }

  /**
   * Registers a set of functions
   *
   * @param array $functions
   */
  public function registerFunctions(array $functions = array())
  {
    foreach ($functions as $name => $args)
    {
      $this->registerFunction($name, $args['callback']);
    }
  }

	/**
	 * Returns available variables
	 *
	 * @since 1.5
	 * @return array Already defined variables
	 */
	public function getVariables()
	{
		return $this->registeredVars;
	}

	public function setVariable($name, $value)
	{
		$this->registeredVars[ $name ] = $value;
	}

  /**
   * Process a WPLessStylesheet
   *
   * This logic was previously held in WPLessStylesheet::save()
   *
   * @since 1.4.2
   */
  public function saveStylesheet(WPLessStylesheet $stylesheet)
  {
    wp_mkdir_p(dirname($stylesheet->getTargetPath()));

    try
    {
      do_action('wp-less_stylesheet_save_pre', $stylesheet, $this->getVariables());

	    $this->compileFile($stylesheet->getSourcePath(), $stylesheet->getTargetPath());

      chmod($stylesheet->getTargetPath(), 0666);

      $stylesheet->save();
      do_action('wp-less_stylesheet_save_post', $stylesheet);
    }
    catch(Exception $e)
    {
      wp_die($e->getMessage());
    }
  }
}
