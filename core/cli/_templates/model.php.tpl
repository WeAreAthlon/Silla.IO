<?php
/**
 * {$model|camelize} Model.
 *
 * @package    Silla
 * @subpackage {$mode}\Models
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
*/

namespace {$mode}\Models;
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
