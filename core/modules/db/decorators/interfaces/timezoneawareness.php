<?php
/**
 * Timezone Awareness Decorator Interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Decorators\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
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
