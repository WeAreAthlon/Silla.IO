<?php
/**
 * Environment settings.
 *
 * @package    Silla
 * @subpackage Configurations\Development
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/* Error reporting */
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'On');
ini_set('log_errors', 'Off');
ini_set('error_log', 'temp/errors.log');

/* Encoding */
mb_internal_encoding('utf-8');

/* Timezone */
date_default_timezone_set('UTC');

/* Locale */
setlocale(LC_ALL, 'en_US.utf8');
