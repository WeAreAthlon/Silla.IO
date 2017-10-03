<?php
/**
 * Core Loader. Configuration and setup.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

/**
 * Define Silla.IO application framework variables.
 */
if (getenv('ENV_SILLA_ENVIRONMENT')) {
    define('SILLA_ENVIRONMENT', getenv('ENV_SILLA_ENVIRONMENT'));
} elseif (isset($_SERVER['ENV_SILLA_ENVIRONMENT']) && !empty($_SERVER['ENV_SILLA_ENVIRONMENT'])) {
    define('SILLA_ENVIRONMENT', $_SERVER['ENV_SILLA_ENVIRONMENT']);
} else {
    define('SILLA_ENVIRONMENT', 'development');
}
