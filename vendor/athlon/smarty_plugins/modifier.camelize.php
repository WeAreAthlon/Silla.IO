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

