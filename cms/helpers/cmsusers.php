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

/**
 * Users Helper Class Definition.
 */
class CMSUsers
{
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

        foreach ($builtin_actions as $key => $builtin_action) {
            if (in_array($builtin_action, array('create', 'show', 'edit', 'delete', 'export'), true)) {
                unset($builtin_actions[$key]);
            }
        }

        foreach ($scope as $resource) {
            $resource = basename(str_replace('.php', '', $resource));

            if ($resource !== 'cms') {
                $controller_name = '\CMS\Controllers\\' . $resource;
                $controller_object = new $controller_name;

                if ($controller_object) {
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
     * Generate random string suitable for password.
     *
     * @param integer $length Number of chars.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function generatePassword($length = 8)
    {
        $password = '';
        $possible = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZbcdfghjkmnpqrstvwxyz';

        $i = 0;

        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);

            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }

        return $password;
    }

    /**
     * Checks if a user can access a specific resource.
     *
     * @param array $scope Example ['controller':'' , 'action':''].
     * @param mixed $user  User object.
     *
     * @access public
     * @static
     *
     * @return boolean
     */
    public static function userCan(array $scope, $user = null)
    {
        if (!$user) {
            $user = Core\Registry()->get('current_user');
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
     * @param string  $d     Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ].
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
}
