<?php
/**
 * Timezone Awareness Decorator Interface.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Decorators\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Decorators\Interfaces;

/**
 * Timezones Decorator for management of datetime values
 */
interface TimezoneAwareness
{
    /**
     * Timezone aware fields container.
     *
     * @static
     *
     * @return array
     */
    public static function timezoneAwareFields();
}
