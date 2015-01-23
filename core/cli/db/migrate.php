<?php
/**
 * Migrate functionality.
 *
 * @package    Silla.IO
 * @subpackage Core\CLI\DB
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\CLI\DB;

use Core;
use Core\CLI\DB;

/**
 * Class Migrate definition.
 */
final class Migrate
{
    /**
     * Creates a migration.
     *
     * @param string $name Migration name.
     *
     * @return void
     */
    public static function create($name)
    {
        $migration_name = strtolower($name) . '_' . time();
        $migration_tpl = file_get_contents(
            Core\Config()->paths('root') . 'core' . DIRECTORY_SEPARATOR .
            'cli' .  DIRECTORY_SEPARATOR . '_templates' .  DIRECTORY_SEPARATOR . 'migration.php.tpl'
        );

        $name = self::getMigrationName($migration_name);
        $path = Core\Config()->paths('root') . 'db' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;

        file_put_contents(
            $path . $migration_name . '.php',
            str_replace('{migration_name}', $name, $migration_tpl)
        );
    }

    /**
     * Shows a migration.
     *
     * @return void
     */
    public static function show()
    {
        $migrations = DB::$migrations;

        d($migrations);
        echo "\n";
    }

    /**
     * Up action.
     *
     * @param string $version Version string.
     *
     * @return void
     */
    public static function up($version)
    {
        $migration = self::getMigration($version);
        $migrationName = self::getMigrationName($migration['name']);
        $name = 'DB\Migrations\\' . $migrationName;
        require_once(
            Core\Config()->paths('root') . implode(
                DIRECTORY_SEPARATOR,
                array('db', 'migrations', $migration['name'])
            ) . '.php'
        );
        $migration = new $name($version);
        $migration->runUp();
    }

    /**
     * Down action.
     *
     * @param string $version Version string.
     *
     * @return void
     */
    public static function down($version)
    {
        $migration = self::getMigration($version);
        $migrationName = self::getMigrationName($migration['name']);
        $name = 'DB\Migrations\\' . $migrationName;
        require_once(
            Core\Config()->paths('root') . implode(
                DIRECTORY_SEPARATOR,
                array('db', 'migrations', $migration['name'])
            ) . '.php'
        );
        $migration = new $name($version);
        $migration->runDown();
    }

    /**
     * Migration status.
     *
     * @return void
     */
    public static function status()
    {

    }

    /**
     * Retrieves a migration name.
     *
     * @param string $migration Version string.
     *
     * @return string
     */
    private static function getMigrationName($migration)
    {
        return preg_replace('/[0-9]{10}$/', '', implode('', array_map('ucfirst', explode('_', $migration))));
    }

    /**
     * Retrieves a migration.
     *
     * @param string $version Version string.
     *
     * @return string
     */
    private static function getMigration($version)
    {
        foreach (DB::$migrations as $migration) {
            if ($migration['version'] == $version) {
                return $migration;
            }
        }
    }
}
