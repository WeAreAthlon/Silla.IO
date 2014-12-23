<?php
/**
 * Link Function.
 *
 * @package    Silla
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
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
