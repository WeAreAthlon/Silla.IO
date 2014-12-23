<?php
/**
 * Smarty plugin.
 *
 * @package    Smarty
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Formats String with format.
 *
 * @param string $string Input string.
 * @param string $format Format string.
 *
 * @return string
 */
function smarty_modifier_string_format($string, $format)
{
	return sprintf($format, $string);
}
