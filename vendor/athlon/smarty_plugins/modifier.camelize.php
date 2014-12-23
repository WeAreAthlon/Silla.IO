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
 * Smarty camelize modifier plugin.
 *
 * Camelize words in the string
 * Type:     modifier<br>
 * Name:     camelize<br>
 *
 * @param string $string Input string.
 *
 * @return string
 */
function smarty_modifier_camelize($string)
{
    $inflector = new \Vendor\Athlon\Inflector();

    return $inflector->camelize($string);
}

