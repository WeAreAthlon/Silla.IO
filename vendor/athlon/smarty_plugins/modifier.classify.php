<?php
/**
 * Smarty plugin.
 *
 * @package    Smarty
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
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
