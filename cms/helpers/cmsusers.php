<?php
/**
 * CMS Users Helper.
 *
 * @package    Silla.IO
 * @subpackage CMS\Helpers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Helpers;

use Core;
use CMS;

/**
 * Users Helper Class Definition.
 */
class CMSUsers
{
    const NOTIFY = 'notify';
    
    /**
     * Get Global Application CMS accessibility scope.
     *
     * @access public
     * @static
     * @uses   Core\Config()
     *
     * @return array
     */
    public static function getAccessibilityScope()
    {
        $scope = glob(Core\Config()->paths('mode') . 'controllers' . DIRECTORY_SEPARATOR . '*.php');

        $builtin_scope = array('CMS\Controllers\CMS');
        $builtin_actions = array();
        $accessibility_scope = array();

        foreach ($builtin_scope as $resource) {
            $builtin_actions = array_merge($builtin_actions, get_class_methods($resource));
        }

        $builtin_actions = array_filter($builtin_actions, function ($action) {
            return !in_array($action, array('create', 'show', 'edit', 'delete', 'export'), true);
        });

        foreach ($scope as $resource) {
            $resource = basename(str_replace('.php', '', $resource));

            if ($resource !== 'cms') {
                $controller_name = '\CMS\Controllers\\' . $resource;
                $controller_class = new \ReflectionClass($controller_name);

                if (!$controller_class->isInstantiable()) {
                    continue;
                }

                /* Create instance only if the controller class is instantiable */
                $controller_object = new $controller_name;

                if ($controller_object instanceof CMS\Controllers\CMS) {
                    $accessibility_scope[$resource] = array_diff(get_class_methods($controller_name), $builtin_actions);
                    array_push($accessibility_scope[$resource], 'index');

                    foreach ($accessibility_scope[$resource] as $key => $action_with_acl) {
                        if (in_array($action_with_acl, $controller_object->skipAclFor, true)) {
                            unset($accessibility_scope[$resource][$key]);
                        }
                    }
                }
            }
        }

        return $accessibility_scope;
    }

    /**
     * Verifies whether a user can access a specific resource scope.
     *
     * @param array                    $scope Example array('controller' => '' , 'action' =>'').
     * @param \CMS\Models\CMSUser|null $user  User instance to verify.
     *
     * @access public
     * @static
     *
     * @return boolean
     */
    public static function userCan(array $scope, CMS\Models\CMSUser $user = null)
    {
        if (!$user) {
            $user = Core\Registry()->get('current_cms_user');
        }

        $_cache_key = md5(serialize($user) . serialize($scope));

        static $user_access_scope_cache;

        if (!isset($user_access_scope_cache[$_cache_key])) {
            $user_access_scope = $user->role()->permissions;
            $user_access_scope_cache[$_cache_key] = array_key_exists($scope['controller'], $user_access_scope) &&
                in_array($scope['action'], $user_access_scope[$scope['controller']], true);
        }

        return $user_access_scope_cache[$_cache_key];
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string  $email The email address.
     * @param integer $s     Size in pixels, defaults to 80px [ 1 - 2048 ].
     * @param string  $d     Default image-set to use [ 404 | mm | identicon | monsterid | wavatar ].
     * @param string  $r     Maximum rating (inclusive) [ g | pg | r | x ].
     *
     * @access public
     * @static
     * @uses   Core\Config()
     *
     * @return string String containing either just a URL or a complete image tag.
     */
    public static function getGravatar($email, $s = 70, $d = 'mm', $r = 'g')
    {
        $url = Core\Config()->urls('protocol') . '://gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= '?' . http_build_query(array('s' => $s, 'd' => $d, 'r' => $r), '', '&amp;');

        return $url;
    }

    public static function block(CMS\Models\CMSUser $user, $action = 'block')
    {
        if ($action == self::NOTIFY) {
            // Send mail that the user profile has reached max login attempts
            Core\Helpers\Mailer::send(array(
                'to' => '',
                'from' => '',
                'content' => '',
                'subject' => 'Exceeden login attempts limit',
            ));

            echo "Administrator has been notified!";
            return;
        }

        $user->save(array('is_active' => 0), true);
    }
}
