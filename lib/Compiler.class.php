<?php
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
   * Returns the LESS buffer
   * 
   * @since 	1.1
   * @return 	string current buffer
   * @deprecated
   */
  public function getBuffer()
  {
  	return $this->buffer;
  }

  /**
   * Enables to overload the current LESS buffer
   * Use at your own risks.
   * 
   * @since		1.1
   * @param 	$css	string CSS you'd like to see in the buffer, before being parse
   * @deprecated
   */
  public function setBuffer($css)
  {
  	$this->buffer = $css;
  }
}
