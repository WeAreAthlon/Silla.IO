<?php
/**
 * Silla.IO loader.
 *
 * Execution entry point.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core;

use Core;
use Core\Modules\Http;

/**
 * Require Silla.IO boot loader.
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'loader.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'boot.php';

try {
    /**
     * Detect Silla.IO Mode.
     */
    $requestString = Http\Router::normalizePath($_SERVER['REQUEST_URI']);
    $mode = Http\Router::getMode($requestString);
    Config()->setMode($mode);

    /**
     * Setup Router variables.
     */
    $namespace = $mode['namespace'];
    $routes = "\\{$namespace}\\Configuration\\Routes";
    $routes  = new $routes();
    $request = new Http\Request($mode, $requestString, $GLOBALS);

    /**
     * Dispatch Request.
     */
    Core\Router()->dispatch($request, $routes->getAll());

} catch(\Exception $e) {
    if (!Core\Router()->response) {
        Core\Router()->response = new Modules\Http\Response;
    }

    if (!Core\Router()->response->hasContent()) {
        Core\Router()->response->setHttpResponseCode(500);
    }

    $message = $e->getMessage() . PHP_EOL . $e->getTraceAsString();

    if ('on' === strtolower(ini_get('display_errors'))) {
        Core\Router()->response->setContent("<pre>{$message}</pre>");
    } else {
        error_log($e->__toString());
    }
}

unset($request, $routes);

/**
 * Output Response headers.
 */
if (!headers_sent() && Core\Router()->response->hasHeaders()) {
    $headers = Core\Router()->response->getHeaders();
    foreach ($headers as $header) {
        if (isset($headers['code']) && $headers['code']) {
            header($header['string'], $header['replace'], $header['code']);
        } else {
            header($header['string'], $header['replace']);
        }
    }
}

/**
 * Output Response content.
 */
if (Core\Router()->response->hasContent()) {
    echo Core\Router()->response->getContent();
}
