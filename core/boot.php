<?php
/**
 * Silla.IO Boot loader and entry point.
 *
 * @package    Silla.IO
 * @subpackage Core
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core;

if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Silla.IO framework will only run on PHP version 5.3.7 or greater!\n");
}

if (!defined('SILLA_ENVIRONMENT')) {
    define('SILLA_ENVIRONMENT', 'development');
}

chdir(dirname(__DIR__));

require dirname(__DIR__)
    . DIRECTORY_SEPARATOR
    . 'configurations'
    . DIRECTORY_SEPARATOR
    . SILLA_ENVIRONMENT
    . DIRECTORY_SEPARATOR
    . 'environment.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'core.php';

Registry()->set('locale', Config()->I18N['default']);
