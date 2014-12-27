<?php
/**
 * Utility functions for common use.
 *
 * @package    Silla
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core;

/**
 * Utils class definition.
 */
final class Utils
{
    /**
     * Filters input.
     *
     * @param string $input Input string to be filtered.
     *
     * @static
     *
     * @return string
     */
    public static function cleanInput($input)
    {
        $search = array(
            '@<script[^>]*?>.*?</script>@si',   /* Strip out javascript      */
            '@<[\/\!]*?[^<>]*?>@si',            /* Strip out HTML tags       */
            '@<style[^>]*?>.*?</style>@siU',    /* Strip style tags properly */
            '@<![\s\S]*?--[ \t\n\r]*>@'         /* Strip multi-line comments */
        );

        return preg_replace($search, '', $input);
    }

    /**
     * Searches array for a given element - recursive.
     *
     * @param array $needle   Array of values to search.
     * @param array $haystack Array to be searched.
     *
     * @static
     *
     * @return mixed
     */
    public static function arraySearchRecursive(array $needle, array $haystack)
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;

            if (in_array($value, $needle, true) || (is_array($value) && self::arraySearchRecursive($needle, $value))) {
                return $current_key;
            }
        }

        return false;
    }

    /**
     * Flattens array to one dimension array.
     *
     * @param array $array Array to flatten.
     *
     * @access public
     * @static
     *
     * @return array
     */
    public static function arrayFlatten(array $array)
    {
        $ret_array = array();

        foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array)) as $value) {
            $ret_array[] = $value;
        }

        return $ret_array;
    }

    /**
     * Extends an array with an array.
     *
     * @param array $a Extended array.
     * @param array $b Extension array.
     *
     * @access public
     * @static
     *
     * @return array
     */
    public static function arrayExtend(array $a, array $b)
    {
        foreach ($b as $k => $v) {
            if (is_array($v)) {
                if (!isset($a[$k])) {
                    $a[$k] = $v;
                } else {
                    $a[$k] = self::arrayExtend($a[$k], $v);
                }
            } else {
                $a[$k] = $v;
            }
        }

        return $a;
    }

    /**
     * Determine if SSL is used to request resources.
     *
     * @access public
     * @static
     *
     * @return boolean
     */
    public static function httpRequestIsSsl()
    {
        if (isset($_SERVER['HTTPS'])) {
            if ('on' === strtolower($_SERVER['HTTPS'])) {
                return true;
            }
            if ('1' === $_SERVER['HTTPS']) {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' === $_SERVER['SERVER_PORT'])) {
            return true;
        }

        return false;
    }

    /**
     * Parses raw HTTP GET request array.
     *
     * @param string $request   Request string.
     * @param string $separator Separator of the URL elements.
     *
     * @access public
     * @static
     *
     * @return array
     */
    public static function parseHttpRequestString($request, $separator)
    {
        return explode($separator, rtrim($request, $separator));
    }

    /**
     * Checks whether an array is associative.
     *
     * @param array $arr Array to be checked.
     *
     * @return boolean
     */
    public static function arrayIsAssoc(array $arr)
    {
        return (array_keys($arr) !== range(0, count($arr) - 1));
    }

    /**
     * Replaces first occurrence of the search string with the replacement string.
     *
     * @param string $search  The value being searched for, otherwise known as the needle.
     * @param string $replace The replacement value that replaces found search values.
     * @param string $subject The string or array being searched and replaced on, otherwise known as the haystack.
     *
     * @return string
     */
    public static function replaceFirstOccurance($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}
