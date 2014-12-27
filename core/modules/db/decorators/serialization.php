<?php
/**
 * Serialization Decorator.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Decorators
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Modules\DB\Decorators;

use Core;
use Core\Base;
use Core\Helpers;
use Core\Modules\DB\Interfaces;

/**
 * Class Serialization Decorator Implementation definition.
 */
abstract class Serialization implements Interfaces\Decorator
{
    /**
     * Fields to serialize.
     *
     * @var array
     * @static
     */
    private static $serializedFields = array();

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
        $_fields = $resource::serializableFields();

        foreach ($_fields as $key => $value) {
            if (!is_numeric($key)) {
                self::$serializedFields[$key] = $value;
            } else {
                self::$serializedFields[$value] = 'serialize';
            }
        }

        $resource->on('afterCreate', array(__CLASS__, 'unserialize'));

        $resource->on('beforeValidate', array(__CLASS__, 'serialize'));
        $resource->on('afterValidate', array(__CLASS__, 'unserialize'));

        $resource->on('beforeSave', array(__CLASS__, 'serialize'));
        $resource->on('afterSave', array(__CLASS__, 'unserialize'));
    }

    /**
     * Un-serialized the fields.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param array      $params   Additional parameters.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function unserialize(Base\Model $resource, array $params = array())
    {
        foreach (self::$serializedFields as $field => $type) {
            switch ($type) {
                case 'json':
                    $resource->{$field} = json_decode($resource->{$field}, true);
                    break;

                case 'yaml':
                    $resource->{$field} = \Spyc::YAMLLoadString($resource->{$field});
                    break;

                case 'serialize':
                    $resource->{$field} = unserialize($resource->{$field});
                    break;
            }
        }
    }

    /**
     * Applies timezone effect.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function serialize(Base\Model $resource)
    {
        foreach (self::$serializedFields as $field => $type) {
            switch ($type) {
                case 'json':
                    $resource->{$field} = json_encode($resource->{$field}, JSON_HEX_APOS | JSON_HEX_QUOT);
                    break;

                case 'yaml':
                    $resource->{$field} = \Spyc::YAMLDump((array)$resource->{$field});
                    break;

                case 'serialize':
                    $resource->{$field} = serialize($resource->{$field});
                    break;
            }
        }
    }
}
