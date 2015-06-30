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

/**
 * Define Silla.IO framework variables.
 */
define(
    'SILLA_ENVIRONMENT',
    isset($_SERVER['HTTP_ENV_SILLA_ENVIRONMENT']) ? $_SERVER['HTTP_ENV_SILLA_ENVIRONMENT'] : 'development'
);

/**
 * Require Silla.IO boot loader.
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'silla.php';

//try {

    $application = new Core\Silla('development', $GLOBALS);
    $response = $application->dispatch($_SERVER['REQUEST_URI']);
//    $response->contents();
//

//} catch (\Exception $e) {

//}
