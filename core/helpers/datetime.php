<?php
/**
 * Dates and Times Helper.
 *
 * @package    Silla
 * @subpackage Core\Helpers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Helpers;

use Core;

/**
 * Class DateTime Helper definition.
 */
class DateTime
{
    /**
     * Fetches all available timezone codes.
     *
     * @access public
     * @static
     *
     * @return array
     */
    public static function getTimezonesList()
    {
        $result = array();
        $timezone_codes = \DateTimeZone::listIdentifiers();

        foreach ($timezone_codes as $timezone_code) {
            $now = new \DateTime('now', new \DateTimeZone($timezone_code));
            $location = explode('/', $timezone_code);
            $zone = $location[0];

            unset($location[0]);

            $location = implode('/', $location);

            $result[$zone][] = array('title' => $location, 'offset' => $now->format('P'));
        }

        return $result;
    }

    /**
     * Changes the default PHP time/date functions timezone.
     *
     * @param string $timezone Valid timezone code.
     *
     * @access public
     * @static
     *
     * @return void
     */
    public static function setEnvironmentTimezone($timezone)
    {
        date_default_timezone_set($timezone ? $timezone : self::getCurrentTimezone());
    }

    /**
     * Gets current environment timezone.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function getCurrentTimezone()
    {
        return date_default_timezone_get();
    }

    /**
     * Apply timezone offset.
     *
     * @param string $datetime String representation of a datetime object.
     * @param string $format   Format of the returned date.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function applyTimezoneOffset($datetime, $format = 'Y-m-d H:i:s')
    {
        $datetime_with_timezone = new \DateTime($datetime, new \DateTimeZone('UTC'));

        return date($format, $datetime_with_timezone->format('U'));
    }

    /**
     * Converts the datetime to UTC.
     *
     * @param string $datetime String representation of a datetime object.
     * @param string $format   Format of the returned date.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function removeTimezoneOffset($datetime, $format = 'Y-m-d H:i:s')
    {
        $datetime_with_timezone = new \DateTime($datetime);

        return gmdate($format, $datetime_with_timezone->format('U'));
    }
}
