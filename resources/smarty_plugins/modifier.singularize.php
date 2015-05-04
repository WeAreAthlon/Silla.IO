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

use ICanBoogie\Inflector;

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
    $inflector = Inflector::get();

    return $inflector->singularize($string);
}
