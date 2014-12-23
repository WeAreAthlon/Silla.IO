<?php
/**
 * User Can Function.
 *
 * @package    Silla
 * @subpackage Vendor\Athlon\SmartyPlugins
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
 */

/**
 * User can access a specific content helper to use in templates.
 *
 * @param array                    $options  Options example [controller => '', action => ''].
 * @param Smarty_Internal_Template $template Template engine object.
 *
 * @uses  \Core\Helpers\CMSUsers
 *
 * @return string
 */
function smarty_function_user_can(array $options, Smarty_Internal_Template $template)
{
    return \CMS\Helpers\CMSUsers::userCan( $options );
}
