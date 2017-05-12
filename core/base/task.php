<?php
/**
 * Base Task.
 *
 * @package    Silla.IO
 * @subpackage Core\Base
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Base;

use Core;
use Core\Modules;

/**
 * Class Task definition.
 */
abstract class Task
{
    /**
     * Initializes environment.
     *
     * @param array $params Additional command line parameters.
     *
     * @return void
     */
    public static function init(array $params = array())
    {
        if (!Core\Router()->request) {
            try {
                $requestString = '/';
                $mode = Core\Config()->mode();

                /*
                 * Setup Router variables.
                 */
                Core\Router()->routes = new Modules\Router\Routes($mode);
                Core\Router()->request = new Modules\Router\Request(
                    $mode,
                    Modules\Router\Router::parseRequestQueryString($requestString, Core\Router()->routes),
                    $GLOBALS
                );
            } catch (\Exception $e) {
                $message = $e->getMessage() . PHP_EOL . $e->getTraceAsString();

                if ('on' === strtolower(ini_get('display_errors'))) {
                    Core\Router()->response->setContent("<pre>{$message}</pre>");
                } else {
                    error_log($e->__toString());
                }
            }
        }
    }

    /**
     * Executes the task.
     *
     * @param array $params Additional command line parameters.
     *
     * @return void
     */
    public static function run(array $params = array())
    {
        echo 'Missing ' . get_called_class() . '::run() method.';
    }
}
