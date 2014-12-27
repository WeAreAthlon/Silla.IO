<?php
/**
 * Database Driver Interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Interfaces
 * @author     Kalin Stefanov <kalin@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Modules\DB\Interfaces;

use Core\Modules\DB;

/**
 * Database driver interface definition.
 */
interface Adapter
{
    /**
     * Queries storage method.
     *
     * @param string $query  Query string.
     * @param array  $params Query parameters.
     *
     * @return mixed
     */
    public function storeQueries($query, array $params = array());

    /**
     * Run method.
     *
     * @param DB\Query $query Current query object.
     *
     * @return mixed
     */
    public function run(DB\Query $query);

    /**
     * Execution method.
     *
     * @param string $sql         Query string.
     * @param array  $bind_params Query parameters.
     *
     * @return mixed
     */
    public function execute($sql, array $bind_params = array());

    /**
     * Character set method.
     *
     * @param string $charset Charset type string.
     *
     * @return mixed
     */
    public function setCharset($charset);

    /**
     * Retrieval of tables method.
     *
     * @param string $schema Schema contents.
     *
     * @return mixed
     */
    public function getTables($schema);

    /**
     * Retrieval of table schema method.
     *
     * @param string $table  Table name.
     * @param mixed  $schema Table schema contents.
     *
     * @return mixed
     */
    public function getTableSchema($table, $schema);

    /**
     * Retrieval of last instered id from the storage engine.
     *
     * @return integer
     */
    public function getLastInsertId();

    /**
     * Table clearance method.
     *
     * @param string $table Table name.
     *
     * @return mixed
     */
    public function clearTable($table);

    /**
     * Retrieves all supported types of JOIN.
     *
     * @return array
     */
    public static function getSupportedJoinTypes();
}
