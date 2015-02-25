<?php
/**
 * Flash Message Function.
 *
 * @package    Silla.IO
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Kristian Arsov <kris.arsov@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

/**
 * Flash Message helper to use in templates.
 *
 * @param array                    $options  Provided Options.
 * @param Smarty_Internal_Template $template Smarty template object.
 *
 * @uses   \CMS\Helpers\FlashMessage
 *
 * @return string
 */
function smarty_function_flash_message(array $options, Smarty_Internal_Template $template)
{
    $template->assign('flash', \CMS\Helpers\FlashMessage::getMessage());
    $template->display('_shared/flash-message.html.tpl');
}
