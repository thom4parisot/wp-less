<?php
/**
 * LESS compiler
 * 
 * @author oncletom
 * @extends lessc
 * @package wp-less
 * @subpackage lib
 * @since 1.2
 * @version 1.1
 */
class WPLessCompiler extends lessc
{
	/**
	 * Instantiate a compiler
	 * 
	 * @see	lessc::__construct
	 * @param $file	string [optional]	Additional file to parse
	 */
	public function __construct($file = null)
	{
  	do_action('wp-less_compiler_construct', $this, $file);
		parent::__construct(apply_filters('wp-less_compiler_construct', $file));
	}

  /**
   * Parse a LESS file
   * 
   * @see lessc::parse
   * @throws Exception
   * @param string $text [optional] Custom CSS to parse
   * @return string CSS output
   */
  public function parse($text = null)
  {
  	do_action('wp-less_compiler_parse', $this);
    return apply_filters('wp-less_compiler_parse', parent::parse($text));
  }
  
  /**
   * Returns the LESS buffer
   * 
   * @since 	1.1
   * @return 	string current buffer
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
   */
  public function setBuffer($css)
  {
  	$this->buffer = $css;
  }
}
