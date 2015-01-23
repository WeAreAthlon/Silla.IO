<?php
/**
 * Timezone Awareness Decorator.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Decorators
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Decorators;

use Core;
use Core\Base;
use Core\Helpers;
use Core\Modules\DB\Interfaces;

/**
 * Class Timezones Decorator Implementation definition.
 */
abstract class TimezoneAwareness implements Interfaces\Decorator
{
    /**
     * Timezone aware fields.
     *
     * @var array
     * @static
     */
    private static $timezoneAwareFields = array();

    /**
     * Decorator entry point.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function decorate(Base\Model $resource)
    {
        if (date_default_timezone_get() !== 'UTC') {
            self::$timezoneAwareFields = $resource::timezoneAwareFields();

            $resource->on('beforeSave', array(__CLASS__, 'remove'));
            $resource->on('afterSave', array(__CLASS__, 'add'));
            $resource->on('afterCreate', array(__CLASS__, 'add'));
        }
    }

    /**
     * Removes timezone effect.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param array      $params   Additional parameters.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function remove(Base\Model $resource, array $params)
    {
        if (date_default_timezone_get() !== 'UTC' && $params && is_array($params)) {
            foreach ($params as $field => $value) {
                if ($value && in_array($field, self::$timezoneAwareFields, true)) {
                    $resource->{$field} = Helpers\DateTime::removeTimezoneOffset($value);
                }
            }
        }
    }

    /**
     * Applies timezone effect.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function add(Base\Model $resource)
    {
        if (date_default_timezone_get() !== 'UTC') {
            self::applyTimezoneEffect($resource);
        }
    }

    /**
     * Calculates timezone offset.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access private
     *
     * @return void
     */
    private static function applyTimezoneEffect(Base\Model $resource)
    {
        foreach (self::$timezoneAwareFields as $datetime_field) {
            if ($resource->{$datetime_field}) {
                $resource->{$datetime_field} = Helpers\DateTime::applyTimezoneOffset($resource->{$datetime_field});
            }
        }
    }
}
