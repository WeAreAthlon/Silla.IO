<?php
/**
 * Smarty Function.
 *
 * @package    Silla
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Checks whether a template exists.
 *
 * @param string $template Text representation of the template name.
 *
 * @uses   Core\Config()
 * @return boolean
 */
function smarty_modifier_template_exists($template)
{
	$viewsPaths = Core\Config()->paths('views');

    return is_file($viewsPaths['templates'] . $template);
}
