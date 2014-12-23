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
 * Smarty underscore modifier plugin.
 *
 * Type:     modifier<br>
 * Name:     underscore<br>
 * Purpose:  underscore words in the string
 *
 * @param string $string Input string.
 *
 * @return string
 */
function smarty_modifier_underscore($string)
{
    $inflector = new \Vendor\Athlon\Inflector();

    return $inflector->underscore($string);
}
