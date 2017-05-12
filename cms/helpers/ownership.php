<?php
/**
 * Resource Ownership Management Helper.
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

/**
 * Resource Ownership Management Helper Class definition.
 */
class Ownership
{
    /**
     * Get Global Application CMS resource ownership scope.
     *
     * @param array $modules Available modules.
     *
     * @return array
     */
    public static function getScope(array $modules)
    {
        $result = array();

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
     * @param string                $resourceModel Name of the resource model.
     * @param \Core\Base\Model|null $owner         User Owner.
     *
     * @return \Core\Modules\DB\Query
     */
    public static function filter($resourceModel, Base\Model $owner = null)
    {
        if (!$owner) {
            $owner = Core\Registry()->get('current_cms_user');
        }

        $query = new DB\Query($resourceModel);
        $ownershipTable = 'cms_ownership';
        $resourceTable = Core\Config()->DB['tables_prefix'] . $resourceModel::$tableName;
        $ownershipTablePrefixed = Core\Config()->DB['tables_prefix'] . 'cms_ownership';
        $resourcePrimaryKey = $resourceModel::primaryKeyField();

        return $query->select("{$resourceTable}.*")->from($resourceModel::$tableName)
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
    public static function assign(Base\Model $resource, Base\Model $owner = null)
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
    public static function retract(Base\Model $resource, Base\Model $owner = null)
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
    public static function check(Base\Model $resource, Base\Model $owner = null)
    {
        if (!$owner) {
            $owner = Core\Registry()->get('current_cms_user');
        }

        return self::checkById($resource->getPrimaryKeyValue(), get_class($resource), $owner);
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
    public static function checkIds($resourceIds, $resourceModel, Base\Model $owner = null)
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
                array($resourceId, $owner->getPrimaryKeyValue(), $resourceModel)
            )->exists();

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * Retracts a resource ownership data.
     *
     * @param \Core\Base\Model $resource Resource instance.
     *
     * @return void
     */
    public static function resetResource(Base\Model $resource)
    {
        $query = new Core\Modules\DB\Query;

        Core\DB()->run($query->remove()->from('cms_ownership')->where(
            'resource_id = ? AND model = ?',
            array($resource->getPrimaryKeyValue(), get_class($resource))
        ));
    }

    /**
     * Remove Ownership Data for an Owner.
     *
     * @param \Core\Base\Model|null $owner Owner instance.
     *
     * @return void
     */
    public static function resetOwner(Base\Model $owner = null)
    {
        if (!$owner) {
            $owner = Core\Registry()->get('current_cms_user');
        }

        $query = new Core\Modules\DB\Query;

        Core\DB()->run($query->remove()->from('cms_ownership')->where(
            'owner_id = ? AND model = ?',
            array($owner->getPrimaryKeyValue(), get_class($resource))
        ));
    }
}
