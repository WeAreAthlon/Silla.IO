<?php
/**
 * Development Configuration class.
 *
 * @package    Silla.IO
 * @subpackage Configurations\Development
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Configurations\Development;

use Configurations;

/**
 * Configuration class implementation.
 */
class Configuration extends Configurations\Staging\Configuration
{
    /**
     * @var boolean[] $ASSETS Assets Management options flags.
     *
     * @example cache    Whether to cache all assets groups on the file system.
     * @example combine  Whether to combine all assets groups in one file.
     * @example optimize Whether to minify assets.
     */
    public $ASSETS = array(
        'cache'    => false,
        'combine'  => false,
        'optimize' => false,
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
        'routes'        => false,
        'labels'        => false,
        'db_schema'     => false,
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
        'debug' => true,
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
     * @example adapter        Adapter type (pdo_mysql|mysql|sqllite).
     * @example host           Connection host name.
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
        'host'           => 'localhost',
        'port'           => 3306,
        'user'           => 'athlon',
        'password'       => 'athlon_sofia',
        'name'           => 'athlon_framework',
        'tables_prefix'  => '',
        'encryption_key' => '25c6c7ff35bd13b0ff9979b151f2136c',
        'crypt_vector'   => 'dasn312321nssa1k',
    );

    /**
     * @var string[] Captcha credentials.
     *
     * @example enabled     Flag whether to use Captcha or Not.
     * @example public_key  Public key for Captcha.
     * @example private_key Private key for Captcha.
     *
     * @link https://www.google.com/recaptcha/
     */
    public $CAPTCHA = array(
        'enabled'     => true,
        'public_key'  => '6LfSevQSAAAAAHUbl-gTGwQHi4C9UW219V0Nn6J5',
        'private_key' => '6LfSevQSAAAAAB6H3f9OznBVUGBp0iMMZWX2OSFH',
    );
}
