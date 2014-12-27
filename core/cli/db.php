<?php
/**
 * DB functionality.
 *
 * @package    Silla
 * @subpackage Core\CLI
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\CLI;

use Core;
use Core\Modules\DB\Query;

/**
 * Class DB definition.
 */
final class DB
{
    /**
     * Migrations container.
     *
     * @var array
     */
    public static $migrations = array();

    /**
     * Generic call method.
     *
     * @param string $name      Method name.
     * @param array  $arguments Method parameters.
     *
     * @return void
     */
    public static function __callStatic($name, array $arguments)
    {
        list($class, $command) = explode(':', $name);
        call_user_func_array(array('Core\CLI\DB\\' . $class, $command), $arguments);
    }

    /**
     * Create action.
     *
     * @return void
     */
    public static function create()
    {
        /* @TODO create DB */
        $query = new Query();
        $query->createTable('migrations')->columns(array(
            'version' => array(
                'type' => 'string',
                'length' => '10',
                'not_null' => true
            )
        ))->tableEngine('MyISAM')->run();
    }

    /**
     * Migrate method.
     *
     * @param boolean $version Version string.
     *
     * @return void
     */
    public static function migrate($version = null)
    {
        $query = new Query();
        $executed_migrations = Core\Utils::arrayFlatten($query->select('*')->from('migrations')->all());
        $migrations_to_execute = array();
        $dir = 'up';

        if ($version) {
            /* Migrate to specific version */
            preg_match('/[0-9]{10}$/', $version, $matches);

            if ($execute_to = $matches[0]) {
                $migrations_to_execute = $query
                    ->select('*')
                    ->from('migrations')
                    ->where('version > ?', array($execute_to))->order('version', 'desc')->all();

                if (count($migrations_to_execute) == 0) {
                    $migrations_to_execute = array_filter(
                        self::$migrations,
                        function ($item) use ($executed_migrations, $execute_to) {
                            return !in_array($item['version'], $executed_migrations) && $item['version'] <= $execute_to;
                        }
                    );
                } else {
                    $dir = 'down';
                }
            }
        } else {
            /* Execute all new migrations */
            $migrations_to_execute = array_reverse(array_filter(
                self::$migrations,
                function ($item) use ($executed_migrations) {
                    return !in_array($item['version'], $executed_migrations);
                }
            ));
        }

        foreach ($migrations_to_execute as $item) {
            DB\Migrate::$dir($item['version']);
        }
    }

    /**
     * Rollback method.
     *
     * @param integer $step Count of steps to rollback.
     *
     * @return void
     */
    public static function rollback($step = 1)
    {
        $query = new Query();
        $migrations_to_execute = $query->select('*')->from('migrations')->order('version', 'desc')->limit($step)->all();

        foreach ($migrations_to_execute as $item) {
            DB\Migrate::down($item['version']);
        }
    }
}
