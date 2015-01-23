<?php
/**
 * Migrations.
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
 * Class Migration definition.
 */
abstract class Migration
{
    /**
     * Verison number container.
     *
     * @var string
     */
    public $version;

    /**
     * Whether to skip execution flag.
     *
     * @var boolean
     */
    private $skip = false;

    /**
     * Init actions.
     *
     * @param string $version Version string.
     */
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Executes next migration.
     *
     * @return void
     */
    public function runUp()
    {
        $this->beforeUp();

        if (!$this->skip) {
            $this->up();
            $this->afterUp();
        }
    }

    /**
     * Executes previous migration.
     *
     * @return void
     */
    public function runDown()
    {
        $this->beforeDown();

        if (!$this->skip) {
            $this->down();
            $this->afterDown();
        }
    }

    /**
     * After next migration execution hook.
     *
     * @return void
     */
    private function afterUp()
    {
        $query = new Query();
        $query->insert(array('version'), array($this->version))->into('migrations')->run();
    }

    /**
     * After previous migration execution hook.
     *
     * @return void
     */
    private function afterDown()
    {
        $query = new Query();
        $query->remove()->from('migrations')->where('version = ?', array($this->version))->run();
    }

    /**
     * Before next migration execution hook.
     *
     * @return void
     */
    private function beforeUp()
    {
        $query = new Query();
        $migration = $query->select('*')->from('migrations')->where('version = ?', array($this->version))->first();

        if ($migration) {
            $this->skip = true;
        }
    }

    /**
     * Before previous migration execution hook.
     *
     * @return void
     */
    private function beforeDown()
    {
        $query = new Query();
        $migration = $query->select('*')->from('migrations')->where('version = ?', array($this->version))->first();

        if (!$migration) {
            $this->skip = true;
        }
    }

    /**
     * Up (next) method definition.
     *
     * @return void
     */
    abstract protected function up();

    /**
     * Down (previous) method definition.
     *
     * @return void
     */
    abstract protected function down();

    /**
     * Creates a table.
     *
     * @param string $tableName Table name.
     * @param array  $columns   Array of column names.
     * @param string $engine    Storage engine type.
     *
     * @return void
     */
    protected function createTable($tableName, array $columns, $engine = 'MyISAM')
    {
        $query = new Query();
        $query = $query->createTable($tableName);

        $cols = array();

        $columns['id'] = 'type:int; unsigned; not_null; ai; pk';
        foreach ($columns as $name => $options) {
            $cols[$name] = $this->parseColumn($options);
        }

        $query->columns($cols)->tableEngine($engine)->run();
    }

    /**
     * Drops a table.
     *
     * @param string $tableName Table name.
     *
     * @return void
     */
    protected function dropTable($tableName)
    {
        $query = new Query();
        $query->dropTable($tableName)->run();
    }

    /**
     * Creates columns.
     *
     * @param string $tableName Table name.
     * @param array  $columns   Array of column names.
     *
     * @return void
     */
    protected function createColumns($tableName, array $columns)
    {
        $query = new Query();

        $cols = array();
        foreach ($columns as $name => $options) {
            $cols[$name] = $this->parseColumn($options);
        }

        $query->addColumns($tableName)->columns($cols)->run();
    }

    /**
     * Drop columns.
     *
     * @param string $tableName Table name.
     * @param array  $columns   Array of column names.
     *
     * @return void
     */
    protected function dropColumns($tableName, array $columns)
    {
        $query = new Query();
        $query->dropColumns($tableName)->columns($columns)->run();
    }

    /**
     * Creation of index method.
     *
     * @return void
     */
    protected function createIndex()
    {
    }

    /**
     * Removal of index method.
     *
     * @return void
     */
    protected function dropIndex()
    {
    }

    /**
     * Parses a column options.
     *
     * @param string $options Options to parse.
     *
     * @return array
     */
    private function parseColumn($options)
    {
        $attributes = explode(';', $options);
        $result = array();

        foreach ($attributes as $attr) {
            if (strpos($attr, ':') !== false) {
                list($key, $value) = array_map(function ($item) {
                    return trim($item);
                }, explode(':', $attr));

                $result[$key] = $value;
            } else {
                $result[trim($attr)] = true;
            }
        }

        return $result;
    }
}
