<?php
/**
 * Link Function.
 *
 * @package    Silla
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * URL helper to use in templates
 *
 * @param array                    $options  Provided options.
 * @param Smarty_Internal_Template $template Smarty template object.
 *
 * @uses   Core\Router()
 * @uses   Core\Config()
 *
 * @return string
 */
function smarty_function_link_to(array $options, Smarty_Internal_Template $template)
{
	return \Core\Config()->urls('relative') . \Core\Router()->toUrl($options);
}
