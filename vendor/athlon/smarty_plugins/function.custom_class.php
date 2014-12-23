<?php
/**
 * Custom Class Function.
 *
 * @package    Silla
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Custom class instances maker.
 *
 * @param array                    $params   Provided options.
 * @param Smarty_Internal_Template $template Smarty template object.
 *
 * @return void
 */
function smarty_function_custom_class(array $params, Smarty_Internal_Template $template)
{
    $class = $params['class'];
    $template->assign($params['var'], new $class());
}
