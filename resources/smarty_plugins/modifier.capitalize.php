<?php
/**
 * Smarty plugin.
 *
 * @package    Smarty
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Monte Ohrt <monte at ohrt dot com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

/**
 * Smarty capitalize modifier plugin.
 *
 * Type:     modifier<br>
 * Name:     capitalize<br>
 * Purpose:  capitalize words in the string
 *
 * @param string  $string    Input string.
 * @param boolean $uc_digits Whether to use digits.
 *
 * @return string
 */
function smarty_modifier_capitalize($string, $uc_digits = false)
{
    $upper_string = ucwords($string);

    $upper_string = preg_replace("!(^|[^\p{L}'])([\p{Ll}])!ue", "'\\1'.ucfirst('\\2')", $upper_string);

    if (!$uc_digits) {
        if (preg_match_all("!\b([\p{L}]*[\p{N}]+[\p{L}]*)\b!u", $string, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[1] as $match) {
                $upper_string = substr_replace($upper_string, $match[0], $match[1], strlen($match[0]));
            }
        }
    }

    return $upper_string;
}
