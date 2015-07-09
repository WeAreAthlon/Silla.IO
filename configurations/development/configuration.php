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
     * @example cache    Flag whether to cache all assets groups on the file system.
     * @example combine  Flag whether to combine all assets groups in one file.
     * @example optimize Flag whether to optimize assets.
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
     * @example routes    Flag whether to cache Routing routes.
     * @example labels    Flag whether to cache Localisation labels.
     * @example db_schema FLag whether to cache Database Entity tables schemas.
     * @example database  Database cache adapter database schema.
     * @example redis     Redis cache adapter connection parameters.
     */
    public $CACHE = array(
        'adapter'       => 'Core\Modules\Cache\Adapters\FileSystem',
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
        'adapter'        => 'mysql',
        'host'           => 'localhost',
        'port'           => 3306,
        'user'           => 'root',
        'password'       => '',
        'name'           => 'silla',
        'tables_prefix'  => '',
        'encryption_key' => '25c6c7ff35bd13b0ff9979b151f2136c',
        'crypt_vector'   => 'dasn312321nssa1k',
    );
}
