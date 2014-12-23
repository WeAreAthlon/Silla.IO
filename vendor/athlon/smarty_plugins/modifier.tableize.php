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
 * Smarty tableize modifier plugin.
 *
 * Type:     modifier<br>
 * Name:     tableize<br>
 * Purpose:  tableize words in the string
 *
 * @param string $string Input string to tableize.
 *
 * @return string
 */
function smarty_modifier_tableize($string)
{
    $inflector = new \Vendor\Athlon\Inflector();

    return $inflector->tableize($string);
}
