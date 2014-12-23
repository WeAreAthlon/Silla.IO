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
 * Smarty classify modifier plugin.
 *
 * Type:     modifier<br>
 * Name:     classify<br>
 * Purpose:  classify words in the string
 *
 * @param string $string Input string.
 *
 * @return string
 */
function smarty_modifier_classify($string)
{
    $inflector = new \Vendor\Athlon\Inflector();

    return $inflector->classify($string);
}
