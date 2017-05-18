<?php
/**
 * Smarty function.
 *
 * @package    Silla.IO
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

/**
 * Displays the remaining time.
 *
 * @param string $string Text representation of the time.
 *
 * @return integer Time in minutes.
 */
function smarty_modifier_time_remain($string)
{
    $original_date = strtotime($string);
    $remain        = time() - ($original_date + 3600 * 48);

    return abs(floor($remain / 3600));
}
