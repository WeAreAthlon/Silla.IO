<?php
/**
 * Dates and Time Helper.
 *
 * @package    Silla.IO
 * @subpackage Core\Helpers;
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Helpers;

use Core;

/**
 * Contains helper methods concerned with date and time manipulation.
 */
class DateTime
{
    /**
     * Fetches all available timezone codes.
     *
     * @return array Timezones with locations and location offsets from GMT.
     */
    public static function getTimezonesList()
    {
        /* Get an array of all defined timezone identifiers. */
        $timezoneCodes = \DateTimeZone::listIdentifiers();
        $timezones     = array();

        foreach ($timezoneCodes as $timezoneCode) {
            /* Create a datetime object using the currently processed timezone. */
            $now      = new \DateTime('now', new \DateTimeZone($timezoneCode));
            $location = explode('/', $timezoneCode);
            $zone     = $location[0];
            /* Unset the zone. */
            unset($location[0]);
            /* Merge the location back together. */
            $location = implode('/', $location);
            /* Populate the resulting array containing timezones with locations and offsets from GMT. */
            $timezones[$zone][] = array('title' => $location, 'offset' => $now->format('P'));
        }

        return $timezones;
    }

    /**
     * Changes the default PHP time/date functions timezone.
     *
     * @param string $timezone Valid timezone code.
     *
     * @return bool FALSE if the $timezone isn't valid, or TRUE otherwise.
     */
    public static function setEnvironmentTimezone($timezone)
    {
        if (!$timezone) {
            $timezone = date_default_timezone_get();
        }

        return date_default_timezone_set($timezone);
    }

    /**
     * Format a datetime string to currently set timezone according to given format.
     *
     * @param string $datetime String representation of a datetime object.
     * @param string $format   Format of the returned date.
     *
     * @return string Formatted according to the given format using the given Unix timestamp.
     */
    public static function format($datetime, $format = 'Y-m-d H:i:s')
    {
        /* Declare the given datetime string is UTC/GMT. */
        $datetimeUtc = new \DateTime($datetime, new \DateTimeZone('UTC'));

        /* Format datetime string to the currently set timezone. */

        return date($format, $datetimeUtc->format('U'));
    }

    /**
     * Format a datetime string to UTC/GMT according to given format.
     *
     * @param string $datetime String representation of a datetime object.
     * @param string $format   Format of the returned date.
     *
     * @return string
     */
    public static function formatGmt($datetime, $format = 'Y-m-d H:i:s')
    {
        /* The given datetime string is using the currently set timezone. */
        $datetime = new \DateTime($datetime);

        /* Format datetime string to UTC/GMT. */

        return gmdate($format, $datetime->format('U'));
    }
}
