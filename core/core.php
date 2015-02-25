<?php
/**
 * Core. Setup all settings and configurations for the application.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

/**
 * Framework Core namespace
 */
namespace Core {

    /**
     * Hook the default auto-load class function.
     */
    spl_autoload_extensions('.php');
    spl_autoload_register('spl_autoload');

    /**
     * For easier access to Config.
     *
     * @return Base\Configuration
     */
    function Config()
    {
        $configuration = 'Configurations\\' . SILLA_ENVIRONMENT . '\\Configuration';

        return $configuration::getInstance();
    }

    /**
     * For easier access to Router.
     *
     * @return Modules\Router\Router
     */
    function Router()
    {
        return Modules\Router\Router::getInstance();
    }

    /**
     * For easier access to Registry.
     *
     * @return Modules\Registry\Registry
     */
    function Registry()
    {
        return Modules\Registry\Registry::getInstance();
    }

    /**
     * For easier access to Session.
     *
     * @return Modules\Session\Session
     */
    function Session()
    {
        return Modules\Session\Session::getInstance(Config()->SESSION['adapter']);
    }

    /**
     * For easier access to Cache.
     *
     * @return Modules\Cache\Cache
     */
    function Cache()
    {
        return Modules\Cache\Cache::getInstance(Config()->CACHE['adapter']);
    }

    /**
     * For easier access to DB.
     *
     * @return Modules\DB\DB
     */
    function DB()
    {
        return Modules\DB\DB::getInstance(Config()->DB);
    }

    /**
     * For easier access to DBCache.
     *
     * @return Modules\DB\DbCache
     */
    function DbCache()
    {
        return Modules\DB\DB::getCache(Config()->DB);
    }
}

/**
 * Global namespace.
 */
namespace {

    /**
     * Prints human-readable information about a variable.
     *
     * @param mixed   $what  Input data.
     * @param boolean $trace Whether to trace invokation place or not.
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

    /**
     * Logs debug data to a log file.
     *
     * @param mixed $what Input data.
     *
     * @uses   Core\Config()
     * @see    file_put_contents()
     *
     * @return void
     */
    function var_log($what)
    {
        if (!is_scalar($what)) {
            $what = print_r($what, true);
        }

        file_put_contents(
            Core\Config()->paths('tmp') . 'debug.log',
            gmdate('[Y-m-d H:i:s e] ') . $what . "\n",
            FILE_APPEND
        );
    }

    /**
     * Registers vendors auto-loaders.
     */
    require_once Core\Config()->paths('vendor') . 'autoload.php';
}
