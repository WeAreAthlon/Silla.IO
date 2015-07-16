<?php
/**
 * Formatting Decorator.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Decorators
 * @author     Rozaliya Stoilova <rozalia@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Decorators;

use Core;
use Core\Base;
use Core\Helpers;
use Core\Modules\DB\Interfaces;
use Core\Modules\DB\Decorators;

/**
 * Class Formatting Decorator Implementation definition.
 */
abstract class Formatting implements Interfaces\Decorator
{
    /**
     * Fields to formalize.
     *
     * @var array
     * @static
     */
    private static $formalizeFields = array();

    /**
     * Decorator entry point.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function decorate(Base\Model $resource)
    {
        $_fields = $resource::formalizeFields();

        foreach ($_fields as $key => $value) {
            self::$formalizeFields[$key] = $value;
        }

        $resource->on('afterCreate', array(__CLASS__, 'unformat'));

        $resource->on('beforeValidate', array(__CLASS__, 'format'));
        $resource->on('afterValidate', array(__CLASS__, 'unformat'));

        $resource->on('beforeSave', array(__CLASS__, 'format'));
        $resource->on('afterSave', array(__CLASS__, 'unformat'));
    }

    /**
     * Unformats the fields.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function unformat(Base\Model $resource)
    {
        foreach (self::$formalizeFields as $field) {
            $resource->{$field} = unserialize($resource->{$field});
        }
    }

    /**
     * Formats the fields.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function format(Base\Model $resource)
    {
        foreach (self::$formalizeFields as $field) {
            $parsedown =  new \Parsedown();
            if (is_array($resource->{$field})) {
                $resource->{$field} =  serialize(array('formatted' => $parsedown->parse($resource->{$field}['raw']),
                'raw' => $resource->{$field}['raw']));
            } else {
                $resource->{$field} =  serialize(array('formatted' => $parsedown->parse($resource->{$field}),
                    'raw' => $resource->{$field}));
            }
        }
    }
}
