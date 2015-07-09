<?php
/**
 * DB Class ORM.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB;

use Core;
use Zob\Adapters;

/**
 * DB Class ORM definition.
 */
final class DB
{
    /**
     * Stores all executed queries.
     *
     * @var array
     * @access public
     */
    public $queries = array();

    /**
     * @var Core\Modules\DB\DbCache
     */
    public $cache;

    /**
     * @var Core\Modules\DB\Interfaces\Adapter
     */
    protected $adapter;

    /**
     * Creates a DB instance object.
     *
     * @param array                    $dsn          Array containing all of the credentials.
     * @param Core\Modules\Cache\Cache $cacheStorage Cache storage instance.
     * @param boolean                  $persistence  Flag whether to user persistence storage for the query caching.
     *
     * @throws \DomainException Unsupported Database adapter.
     *
     * @return mixed
     */
    public function __construct(array $dsn, Core\Modules\Cache\Cache $cacheStorage, $persistence)
    {
        switch ($dsn['adapter']) {
            case 'mysql':
                $this->connection = new Adapters\MySql\MySql($dsn);
                /*$this->cache = new DbCache($this->connection, $dsn, $cacheStorage, $persistence);
                $this->adapter->setCacheStorage($this->cache);
                $this->cache->setup();

                $this->adapter->setCharset();
                $this->adapter->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);*/
                break;
            case 'mysqli':
                $this->adapter = new Adapters\MySQLi($dsn['host'], $dsn['name'], $dsn['user'], $dsn['password']);
                $this->cache = new DbCache($this->adapter, $dsn, $cacheStorage, $persistence);
                $this->adapter->setCacheStorage($this->cache);
                $this->adapter->setCharset();
                break;
            case 'mysql':
                $this->adapter = new Adapters\MySQL($dsn['host'], $dsn['name'], $dsn['user'], $dsn['password']);
                $this->cache = new DbCache($this->adapter, $dsn, $cacheStorage, $persistence);
                $this->adapter->setCacheStorage($this->cache);
                $this->adapter->setCharset();
                break;
            case 'sqlite':
                $this->adapter = new Adapters\SQLite($dsn['host']);
                $this->cache = new DbCache($this->adapter, $dsn, $cacheStorage, $persistence);
                $this->adapter->setCacheStorage($this->cache);
                $this->adapter->setCharset();
                break;
            default:
                throw new \DomainException('Unsupported Database adapter: ' . $dsn['adapter']);
                break;
        }
    }

    /**
     * Retrieves a cache instance.
     *
     * @final
     * @access public
     *
     * @return Core\Modules\DB\DbCache
     */
    final public function getCache()
    {
        return $this->cache;
    }

    /**
     * Retrieves an connection instance.
     *
     * @return Zob\Adapters\ConncetionInterface
     */
    final public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Create a closure with different DSN.
     *
     * @param array $dsn   Array containing all the credentials.
     * @param mixed $scope Scope of execution.
     *
     * @access public
     * @static
     * @final
     *
     * @return void
     */
    final public static function with(array $dsn, $scope)
    {
//        $default_instance = self::$instance;
//        self::$instance = self::createInstance($dsn);
//
//        if (is_callable($scope)) {
//            $scope(self::$instance);
//        }
//
//        self::$instance = $default_instance;
    }
}
