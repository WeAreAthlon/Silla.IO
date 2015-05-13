<?php
/**
 * Production Configuration class.
 *
 * All settings and configuration for the application.
 *
 * @package    Silla.IO
 * @subpackage Configurations\Production
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Configurations\Production;

use Core;

/**
 * Configuration class implementation.
 */
class Configuration extends Core\Base\Configuration
{
    /**
     * @var boolean[] $ASSETS Assets Management options flags.
     *
     * @example cache    Whether to cache all assets groups on the file system.
     * @example combine  Whether to combine all assets groups in one file.
     * @example optimize Whether to minify assets.
     */
    public $ASSETS = array(
        'cache'    => true,
        'combine'  => true,
        'optimize' => true,
    );

    /**
     * @var string[] $ROUTER Router related configuration options.
     *
     * @example rewrite          Whether to support url rewrite or not.
     * @example separator        URL elements separator.
     * @example variables_prefix Routes variables notation prefix. Must be different from the 'separator'.
     */
    public $ROUTER = array(
        'rewrite'          => true,
        'separator'        => '/',
        'variables_prefix' => ':'
    );

    /**
     * @var array $CACHE Cache related configuration options.
     *
     * @example adapter   Caching adapter name.
     * @example routes    Whether to cache Routing routes.
     * @example labels    Whether to cache Localisation labels.
     * @example db_schema Whether to cache Database Entity tables schemas.
     * @example database  Database cache adapter database schema.
     * @example redis     Redis cache adapter connection parameters.
     */
    public $CACHE = array(
        'adapter'       => 'FileSystem',
        'routes'        => true,
        'labels'        => true,
        'db_schema'     => true,
        'database' => array(
            'table_name' => 'cache',
            'fields'     => array(
                'cache_key',
                'value',
                'expire',
            ),
        ),
        'redis' => array(
            'scheme'    => 'tcp',
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'timeout'   => 5.0,
        ),
    );

    /**
     * @var (int|string)[] $MAILER Mailer configuration options.
     *
     * @example type        Type of the mailing infrastructure to use(Sendmail|SMTP).
     * @example identity    Mailer identity signature.
     * @example credentials Mailer service credentials.
     */
    public $MAILER = array(
        'type' => 'Sendmail',
        'identity' => array(
            'name'  => 'Athlon Production',
            'email' => 'hi@athlonproduction.com',
        ),
        'credentials' => array(
            'smtp' => array(
                'host' => 'localhost',
                'port' => '25',
                'user' => '',
                'password' => '',
            ),
        ),
    );

    /**
     * @var (int|string)[] $DB DSN (Data source name).
     *
     * @example adapter        Adapter type (pdo_mysql|mysqli|mysql|sqlite).
     * @example host           Connection host name, or sqlite db file location.
     * @example port           Connection host port.
     * @example user           User name.
     * @example password       Password phrase.
     * @example name           Database name.
     * @example tables_prefix  Storage tables prefix.
     * @example encryption_key Database encryption key.
     * @example crypt_vector   Initialization Vector value.
     */
    public $DB = array(
        'adapter'        => 'pdo_mysql',
        'host'           => '<DB_HOST>',
        'port'           => 3306,
        'user'           => '<DB_USER>',
        'password'       => '<DB_PASSWORD>',
        'name'           => '<DB_NAME>',
        'tables_prefix'  => '',
        'encryption_key' => '25c6c7ff35bd13b0ff9979b151f2136c',
        'crypt_vector'   => 'dasn312321nssa1k',
    );

    /**
     * @var (int|string)[] User cookie authentication data.
     *
     * @example cookie_name       Name of the cookie.
     * @example cookie_salt       Cookie value salt..
     * @example cookie_expiration Cookie expiration time in seconds.
     */
    public $USER_AUTH = array(
        'cookie_name'       => 'ath_login',
        'cookie_salt'       => 'dasxnq20934@*jaa!@sajx',
        'cookie_expiration' => 604800,
    );

    /**
     * @var string[] Captcha credentials.
     *
     * @example public_key  Public key for Captcha.
     * @example private_key Private key for Captcha.
     *
     * @link https://www.google.com/recaptcha/
     */
    public $CAPTCHA = array(
        'public_key'  => '6LfSevQSAAAAAHUbl-gTGwQHi4C9UW219V0Nn6J5',
        'private_key' => '6LfSevQSAAAAAB6H3f9OznBVUGBp0iMMZWX2OSFH',
    );
}
