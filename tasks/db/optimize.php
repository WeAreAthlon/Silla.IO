<?php
/**
 * Optimize Database Tables Task.
 *
 * @package    Silla.IO
 * @subpackage Core\CLI
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Tasks\DB;

use Core;

/**
 * Class Optimize Task definition.
 */
final class Optimize extends Core\Base\Task
{
    /**
     * Optimize database tables.
     *
     * @param array $params Additional command line parameters.
     *
     * @example ./silla tasks:db:optimize
     *
     * @return void
     */
    public static function run(array $params = array())
    {
        $tables = Core\DB()->query('SHOW TABLES');
        $tables = Core\Utils::arrayFlatten($tables);

        foreach ($tables as $db => $table) {
            Core\DB()->query("OPTIMIZE TABLE `{$table}`");
        }
    }
}
