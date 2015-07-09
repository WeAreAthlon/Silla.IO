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
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function remove(Base\Model $resource)
    {
        if (date_default_timezone_get() !== 'UTC') {
            self::removeTimezoneEffect($resource);
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
     * Calculates timezone offset which needs to be added.
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
                $resource->{$datetime_field} = Helpers\DateTime::format($resource->{$datetime_field});
            }
        }
    }

    /**
     * Calculates timezone offset which needs to be removed.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access private
     *
     * @return void
     */
    private static function removeTimezoneEffect(Base\Model $resource)
    {
        foreach (self::$timezoneAwareFields as $datetime_field) {
            if ($resource->{$datetime_field}) {
                $resource->{$datetime_field} = Helpers\DateTime::formatGmt($resource->{$datetime_field});
            }
        }
    }
}
