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
 * Smarty singularize modifier plugin.
 *
 * Type:     modifier<br>
 * Name:     singularize<br>
 * Purpose:  singularize words in the string
 *
 * @param string $string Input string to singularize.
 *
 * @return string
 */
function smarty_modifier_singularize($string)
{
    $inflector = new \Vendor\Athlon\Inflector();

    return $inflector->singularize($string);
}
