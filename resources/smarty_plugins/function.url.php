<?php
/**
 * Link Function.
 *
 * @package    Silla.IO
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
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
function smarty_function_url(array $options, Smarty_Internal_Template $template)
{
    return \Core\Config()->urls('relative') . \Core\Router()->toUrl($options);
}
