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
     * @example cache    Flag whether to cache all assets groups on the file system.
     * @example combine  Flag whether to combine all assets groups in one file.
     * @example optimize Flag whether to optimize assets.
     */
    public $ASSETS = array(
        'cache'    => true,
        'combine'  => true,
        'optimize' => true,
    );

    /**
     * @var string[] $ROUTER Router related configuration options.
     *
     * @example rewrite Whether to support url rewrite or not.
     */
    public $ROUTER = array(
        'rewrite' => true,
    );

    /**
     * @var array $CACHE Cache related configuration options.
     *
     * @example adapter   Caching adapter name.
     * @example routes    Flag whether to cache Routing routes.
     * @example labels    Flag whether to cache Localisation labels.
     * @example db_schema Flag whether to cache Database Entity tables schemas.
     * @example database  Database cache adapter database schema.
     * @example redis     Redis cache adapter connection parameters.
     */
    public $CACHE = array(
        'adapter'       => 'Core\Modules\Cache\Adapters\FileSystem',
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
