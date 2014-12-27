<?php
/**
 * Smarty function.
 *
 * @package    Silla
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
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
	$remain = time()  - ($original_date + 3600*48);

	return abs(floor($remain/3600));
}
