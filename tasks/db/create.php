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
 * Class Create Task definition.
 */
final class Create extends Core\Base\Task
{
    /**
     * Optimize database tables.
     *
     * @param array $params Additional command line parameters.
     *
     * @throws \InvalidArgumentException When missing dump file is passed.
     * @throws \LogicException           When cannot execute queries.
     *
     * @return void
     */
    public static function run(array $params = array())
    {
        $filename = isset($params[0]) && is_file($params[0]) ? $params[0] : null;

        if (!$filename) {
            throw new \InvalidArgumentException('The passed dump file cannot be found!');
        }

        $tempLine     = '';
        $lines        = file($filename);
        $queriesCount = 0;

        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            $tempLine .= $line;

            if (substr(trim($line), -1, 1) == ';') {
                Core\DB()->query($tempLine);
                $tempLine = '';
                $queriesCount++;
            }
        }

        echo $queriesCount . ' Queries imported successfully!' . PHP_EOL;
    }
}
