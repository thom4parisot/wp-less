<?php
/**
 * LESS compiler
 * 
 * @author oncletom
 * @extends lessc
 * @package wp-less
 * @subpackage lib
 * @since 1.2
 * @version 1.0
 */
class WPLessCompiler extends lessc
{
  /**
   * Parse a LESS file
   * 
   * @see lessc::parse
   * @author oncletom
   * @throws Exception
   * @param string $text [optional] Custom CSS to parse
   * @return string CSS output
   */
  public function parse($text = null)
  {
    $output = parent::parse($text);
    $output = apply_filters('wp-less_compiler_parse', $output);

    return $output;
  }
}
