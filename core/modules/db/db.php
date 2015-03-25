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

/**
 * DB Class ORM definition.
 */
final class DB
{
    /**
     * Instance of the singleton.
     *
     * @var DB
     * @static
     * @access public
     */
    public static $instance = null;

    /**
     * Stores all executed queries.
     *
     * @var array
     * @static
     * @access public
     */
    public static $queries = array();

    /**
     * Stores all instances.
     *
     * @var array
     * @static
     * @access private
     */
    private static $instances = array();

    /**
     * Cloning of DB is disallowed.
     *
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error(__CLASS__ . ' cannot be cloned! It is a singleton.', E_USER_ERROR);
    }

    /**
     * Constructor, does nothing.
     *
     * @access private
     */
    private function __construct()
    {
    }

    /**
     * If we have instance return it, else create it.
     *
     * @param array $dsn Array containing all the credentials.
     *
     * @throws \LogicException Cannot establish a database connection.
     * @access public
     * @static
     * @final
     *
     * @return DB
     */
    final public static function getInstance(array $dsn)
    {
        if (null === self::$instance) {
            try {
                self::$instance = self::createInstance($dsn);
            } catch (\Exception $e) {
                throw new \LogicException('Cannot establish a database connection');
            }
        }

        return self::$instance;
    }

    /**
     * Creates a DB instance object.
     *
     * @param array $dsn Array containing all of the credentials.
     *
     * @static
     * @throws \DomainException Unsupported Database adapter.
     *
     * @return mixed
     */
    private static function createInstance(array $dsn)
    {
        $hash = md5(serialize($dsn));
        $instance = null;

        if (!isset(self::$instances[$hash])) {
            switch ($dsn['adapter']) {
                case 'pdo_mysql':
                    $instance = new Adapters\PdoMySql(
                        'mysql:host=' . $dsn['host'] . ';dbname=' . $dsn['name'],
                        $dsn['user'],
                        $dsn['password']
                    );
                    $instance->setCharset();
                    $instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    break;
                case 'mysqli':
                    $instance = new Adapters\MySQLi($dsn['host'], $dsn['name'], $dsn['user'], $dsn['password']);
                    $instance->setCharset();
                    break;
                case 'mysql':
                    $instance = new Adapters\MySQL($dsn['host'], $dsn['name'], $dsn['user'], $dsn['password']);
                    $instance->setCharset();
                    break;
                case 'slqlite':
                    $instance = new Adapters\Sqlite($dsn['host'] . '.db3');
                    $instance->setCharset();
                    break;
                default:
                    throw new \DomainException('Unsupported Database adapter: ' . $dsn['adapter']);
                    break;
            }

            self::$instances[$hash] = $instance;
            $instance->cache = new DbCache();
        }

        return self::$instances[$hash];
    }

    /**
     * Retrieves cache.
     *
     * @param array $dsn Array containing all the credentials.
     *
     * @static
     * @final
     * @access public
     *
     * @return mixed
     */
    final public static function getCache(array $dsn)
    {
        if (!self::$instance) {
            self::$instance = self::createInstance($dsn);
        }

        return self::$instance->cache;
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
        $default_instance = self::$instance;
        self::$instance = self::createInstance($dsn);

        if (is_callable($scope)) {
            $scope(self::$instance);
        }

        self::$instance = $default_instance;
    }
}
