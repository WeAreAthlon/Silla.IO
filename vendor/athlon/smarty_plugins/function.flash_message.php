<?php
/**
 * Flash Message Function.
 *
 * @package    Silla
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Kristian Arsov <kris.arsov@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
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
