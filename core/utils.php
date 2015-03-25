<?php
/**
 * Utility functions for common use.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
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
     * Executes a shell command.
     *
     * @param string $command Shell code to execute.
     *
     * @access public
     * @see    putenv
     * @static
     *
     * @return string The result from the command execution.
     */
    public static function executeShellCommand($command)
    {
        /* remove newlines and convert single quotes to double to prevent errors */
        $command = str_replace(array("\n", "'"), array('', '"'), $command);

        /* replace multiple spaces with one */
        $command = preg_replace('#(\s){2,}#is', ' ', $command);

        /* escape shell meta characters */
        $command = escapeshellcmd($command);

        /* Export used system paths */
        putenv('PATH="/usr/lib/qt-3.3/bin:/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:$PATH"');

        return shell_exec($command);
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
    public static function replaceFirstOccurrence($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * Convert the $_FILES array to the cleaner (IMHO) array.
     *
     * @param array $files Array of files ($_FILES compatible array).
     *
     * @return array Reformatted array.
     */
    public static function formatArrayOfFiles(array &$files)
    {
        $result = array();
        $count = count($files['name']);
        $keys = array_keys($files);

        for ($i = 0; $i < $count; $i++) {
            foreach ($keys as $key) {
                $result[$i][$key] = $files[$key][$i];
            }
        }

        return $result;
    }

    /**
     * Convert PHP defined Size to Bytes.
     *
     * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case).
     *
     * @param mixed $sSize Size notation.
     *
     * @return integer
     */
    public static function convertPHPSizeToBytes($sSize)
    {
        if (is_numeric($sSize)) {
            return $sSize;
        }

        $sSuffix = substr($sSize, -1);
        $iValue = substr($sSize, 0, -1);

        switch (strtoupper($sSuffix)) {
            /* Fall through the next value */
            case 'P':
                $iValue *= 1024;
            /* Fall through the next value */
            case 'T':
                $iValue *= 1024;
            /* Fall through the next value */
            case 'G':
                $iValue *= 1024;
            /* Fall through the next value */
            case 'M':
                $iValue *= 1024;
            /* Fall through the next value */
            case 'K':
                $iValue *= 1024;
                break;
        }

        return $iValue;
    }

    /**
     * Convert array of values to array of refs.
     *
     * @param array $array Array to be converted.
     *
     * @return array Refs.
     */
    public static function arrayToRefValues(array $array)
    {
        $refs = array();

        foreach ($array as $key => $value) {
            $refs[$key] = &$array[$key];
        }
        return $refs;
    }
}
