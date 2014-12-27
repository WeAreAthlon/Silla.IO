<?php
/**
 * Encryption Decorator.
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
use Core\Modules\Crypt\Crypt;

/**
 * Class Encryption Decorator Implementation definition.
 */
abstract class Encryption implements Interfaces\Decorator
{
    /**
     * Fields to encrypt.
     *
     * @var array
     * @static
     */
    private static $encryptedFields = array();

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
        $_fields = $resource::encryptedFields();

        foreach ($_fields as $key => $value) {
            if ($key) {
                self::$encryptedFields[$key] = $value;
            } else {
                self::$encryptedFields[$value] = 'AES-256-CBC';
            }
        }

        $resource->on('afterCreate', array(__CLASS__, 'decrypt'));
        $resource->on('beforeSave', array(__CLASS__, 'encrypt'));
        $resource->on('afterSave', array(__CLASS__, 'decrypt'));
    }

    /**
     * Decrypt fields.
     *
     * @param Base\Model $resource Currently processed resource.
     * @param array      $params   Additional parameters.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function decrypt(Base\Model $resource, array $params = array())
    {
        foreach (self::$encryptedFields as $field => $type) {
            $resource->{$field} = Crypt::decrypt($resource->{$field}, Core\Config()->DB['encryption_key'], $type);
        }
    }

    /**
     * Encrypt fields.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function encrypt(Base\Model $resource)
    {
        foreach (self::$encryptedFields as $field => $type) {
            $resource->{$field} = Crypt::encrypt($resource->{$field}, Core\Config()->DB['encryption_key'], $type);
        }
    }
}
