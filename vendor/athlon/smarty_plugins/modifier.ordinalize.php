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
 * Smarty ordinalize modifier plugin.
 *
 * Type:     modifier<br>
 * Name:     ordinalize<br>
 * Purpose:  ordinalize words in the string
 *
 * @param integer $number Number to ordinalize.
 *
 * @return string
 */
function smarty_modifier_ordinalize($number)
{
    $inflector = new \Vendor\Athlon\Inflector();

    return $inflector->ordinalize($number);
}
