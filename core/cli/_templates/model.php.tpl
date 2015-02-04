<?php
/**
 * {$model|camelize} Model.
 *
 * @package    Silla.IO
 * @subpackage {$mode|upper}\Models
 * @author     Author <author@website.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
*/

namespace {$mode|upper}\Models;
use Core;
use Core\Base;

/**
 * {$model} class definition.
 */
class {$model} extends Base\Model
{
    /**
     * Table storage name.
     *
     * @var string
     */
    public static $tableName = '{$model|tableize}';
}
