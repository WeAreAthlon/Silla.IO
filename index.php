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
use Core\Modules\Router;

/**
 * Require Silla.IO boot loader.
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'loader.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'boot.php';

try {
    /**
     * Detect Silla.IO Mode.
     */
    $requestString = Router\Router::normalizePath(urldecode($_SERVER['REQUEST_URI']));
    $mode          = Router\Router::getMode($requestString);
    Config()->setMode($mode);

    /**
     * Setup Router variables.
     */
    $routes  = new Router\Routes($mode);
    $request = new Router\Request($mode, Router\Router::parseRequestQueryString($requestString, $routes), $GLOBALS);

    /**
     * Dispatch Request.
     */
    Core\Router()->dispatch($request, $routes);
} catch (\Exception $e) {
    if (!Core\Router()->response) {
        Core\Router()->response = new Modules\Router\Response;
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
