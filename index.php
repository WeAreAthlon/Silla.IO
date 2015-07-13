<?php
/**
 * Silla.IO HTTP Requests dispatcher loader.
 *
 * Execution entry point.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */
namespace Silla;

use Core;

/**
 * Define Silla.IO framework variables.
 */
$environment = isset($_SERVER['HTTP_ENV_SILLA_ENVIRONMENT']) ? $_SERVER['HTTP_ENV_SILLA_ENVIRONMENT'] : 'development';

/* Hook the default auto-load class function. */
spl_autoload_extensions('.php');
spl_autoload_register('spl_autoload');

/**
 * Require Silla.IO boot loader.
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'silla.php';

$application = null;

/* Error reporting */
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'On');
ini_set('log_errors', 'On');
ini_set('error_log', 'temp/errors.log');

/* Encoding */
mb_internal_encoding('utf-8');

/* Timezone */
date_default_timezone_set('UTC');

/* Locale */
setlocale(LC_ALL, 'en_US.utf8');

//try {
    $configuration = Core\Silla::getConfigurationClass($environment);

    $application = new Core\Silla(new $configuration($GLOBALS));

    $response = $application->dispatch($_SERVER['REQUEST_URI']);
//} catch (\Exception $e) {
//    $response = new Core\Modules\Http\Response;
//    $response->setProtocol($application->request()->type());
//    $response->setHttpResponseCode(500);
//
//    if ('on' === strtolower(ini_get('display_errors'))) {
//        $response->setContent('<pre>' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . '</pre>');
//    } else {
//        error_log($e->__toString());
//    }
//}

/**
 * Output Response headers.
 */
if (!headers_sent() && $response->hasHeaders()) {
    foreach ($response->getHeaders() as $header) {
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
if ($response->hasContent()) {
    echo $response->getContent();
}
