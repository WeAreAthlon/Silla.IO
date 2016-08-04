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
use Core\Base;
use Core\Modules\DB;
use CMS;

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

        $builtin_actions = array_filter($builtin_actions, function($action) {
            return !in_array($action, array('create', 'show', 'edit', 'delete', 'export'), true);
        });

        foreach ($scope as $resource) {
            $resource = basename(str_replace('.php', '', $resource));

            if ($resource !== 'cms') {
                $controller_name = '\CMS\Controllers\\' . $resource;
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
     * Get Global Application CMS resource ownership scope.
     *
     * @return array
     */
    public static function getOwnershipScope()
    {
        $result = array();
        $modules = Core\Helpers\YAML::get('modules');

        foreach ($modules as $name => $module) {
            if (isset($module['ownership']) && $module['ownership']) {
                $controller_name = '\CMS\Controllers\\' . $name;
                $controller_object = new $controller_name;

                $result[] = array(
                    'name' => $name,
                    'model' => $controller_object->resourceModel
                );
            }
        }

        return $result;
    }

    /**
     * Filter owner resources.
     *
     * @param \Core\Modules\DB\Query $query         Resource query.
     * @param string                 $resourceModel Name of the resource model.
     * @param \Core\Base\Model|null  $owner         User Owner.
     *
     * @return \Core\Modules\DB\Query
     */
    public static function filterOwnResources(DB\Query $query, $resourceModel, Base\Model $owner = null)
    {
        if (!$owner) {
            $owner = Core\Registry()->get('current_cms_user');
        }

        $ownershipTable = 'cms_ownership';
        $resourceTable = Core\Config()->DB['tables_prefix'] . $resourceModel::$tableName;
        $ownershipTablePrefixed = Core\Config()->DB['tables_prefix'] . 'cms_ownership';
        $resourcePrimaryKey = $resourceModel::primaryKeyField();

        return $query->select("{$resourceTable}.*")
            ->join($ownershipTable, "{$resourceTable}.{$resourcePrimaryKey} = {$ownershipTablePrefixed}.resource_id")
            ->where(
                "{$ownershipTablePrefixed}.owner_id = ? AND {$ownershipTablePrefixed}.model = ?",
                array($owner->getPrimaryKeyValue(), $resourceModel)
            );
    }

    /**
     * Assigns resource ownership to a user.
     *
     * @param \Core\Base\Model $resource Resource Instance.
     * @param \Core\Base\Model $owner    User Owner.
     *
     * @return void
     */
    public static function assignResource(Base\Model $resource, Base\Model $owner = null)
    {
        if (!$owner) {
            $owner = Core\Registry()->get('current_cms_user');
        }

        $query = new Core\Modules\DB\Query;
        Core\DB()->run($query->insert(
            array('owner_id', 'resource_id', 'model'),
            array($owner->getPrimaryKeyValue(), $resource->getPrimaryKeyValue(), get_class($resource))
        )->into('cms_ownership'));
    }

    /**
     * Retracts a resource from an owner.
     *
     * @param \Core\Base\Model      $resource Resource instance.
     * @param \Core\Base\Model|null $owner    Owner instance.
     *
     * @return void
     */
    public static function retractResource(Base\Model $resource, Base\Model $owner = null)
    {
        if (!$owner) {
            $owner = Core\Registry()->get('current_cms_user');
        }

        $query = new Core\Modules\DB\Query;

        Core\DB()->run($query->remove()->from('cms_ownership')->where(
            'resource_id = ? AND owner_id = ? AND model = ?',
            array($resource->getPrimaryKeyValue(), $owner->getPrimaryKeyValue(), get_class($resource))
        ));
    }

    /**
     * Verifies whether a user owns a specific resource instance.
     *
     * @param \Core\Base\Model      $resource Resource Instance.
     * @param \Core\Base\Model|null $owner    Owner instance to verify.
     *
     * @access public
     * @static
     *
     * @return boolean Whether the user owns the resource or not.
     */
    public static function userOwnsResource(Base\Model $resource, Base\Model $owner = null)
    {
        if (!$owner) {
            $owner = Core\Registry()->get('current_cms_user');
        }

        return self::userOwns($resource->getPrimaryKeyValue(), get_class($resource), $owner);
    }

    /**
     * Verifies whether a user owns a specific resource by resource data.
     *
     * @param mixed                 $resourceIds   Resource ID values.
     * @param string                $resourceModel Resource Model name.
     * @param \Core\Base\Model|null $owner         Owner instance to check.
     *
     * @access public
     * @static
     *
     * @return boolean Whether the user owns the resource or not.
     */
    public static function userOwns($resourceIds, $resourceModel, Base\Model $owner = null)
    {
        if (!$owner) {
            $owner = Core\Registry()->get('current_cms_user');
        }

        $result = false;
        $resourceIds = is_array($resourceIds) ? array_filter($resourceIds) : array($resourceIds);

        foreach ($resourceIds as $resourceId) {
            $query = new Core\Modules\DB\Query;
            $result = $query->select('owner_id')->from('cms_ownership')->where(
                'resource_id = ? AND owner_id = ? AND model = ?',
                array($resourceId, $owner->getPrimaryKeyValue(), $resourceModel))
                ->exists();

            if (!$result) {
                break;
            }
        }

        return $result;
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
}
