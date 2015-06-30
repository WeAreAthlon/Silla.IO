<?php
/**
 * Debug functions.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

/**
 * Prints human-readable information about a variable.
 *
 * @param mixed   $what  Input data.
 * @param boolean $trace Whether to trace invocation place or not.
 *
 * @see    debug_backtrace()
 *
 * @return void
 */
function d($what, $trace = true)
{
    if ($trace) {
        $trace = debug_backtrace();
        $trace = "<br/><small><em>called in {$trace[0]['file']} on {$trace[0]['line']} line</em></small>";
        echo '<pre>', print_r($what, true) . $trace, '</pre>';
    } else {
        echo '<pre>', print_r($what, true), '</pre>';
    }
}

/**
 * Stop the script and prints human-readable information about a variable.
 *
 * @param mixed $what Input data.
 *
 * @uses   d()
 * @see    debug_backtrace()
 *
 * @return void
 */
function dd($what)
{
    $trace = debug_backtrace();
    $trace = "<small><em>called in {$trace[0]['file']} on {$trace[0]['line']} line</em></small>";

    die(d($what, false) . $trace);
}

/**
 * Prints all Database queries executed.
 *
 * @uses   d()
 *
 * @return void
 */
function ds()
{
    d(Core\Modules\DB\DB::$queries);
}

/**
 * Prints human-readable information about a variable.
 *
 * @param mixed $what Input data.
 *
 * @see    var_dump()
 *
 * @return void
 */
function vd($what)
{
    echo '<pre>';
    var_dump($what);
    echo '</pre>';
}
