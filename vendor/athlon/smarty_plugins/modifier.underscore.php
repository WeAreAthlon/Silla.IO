<?php
/**
 * Smarty plugin.
 *
 * @package    Smarty
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
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
