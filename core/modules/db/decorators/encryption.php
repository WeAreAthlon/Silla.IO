<?php
/**
 * Encryption Decorator.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\DB\Decorators
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\DB\Decorators;

use Core;
use Core\Base;
use Core\Modules\DB;
use Core\Modules\DB\Decorators\Interfaces;
use Core\Modules\Crypt\Crypt;

/**
 * Class Encryption Decorator Implementation definition.
 */
abstract class Encryption implements DB\Interfaces\Decorator
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
        $resource->on('beforeCreate', array(__CLASS__, 'init'));
        $resource->on('afterCreate', array(__CLASS__, 'decrypt'));
        $resource->on('beforeSave', array(__CLASS__, 'encrypt'));
        $resource->on('afterSave', array(__CLASS__, 'decrypt'));
    }

    /**
     * Initialize encryption.
     *
     * @param Interfaces\Encryption $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function init(Interfaces\Encryption $resource)
    {
        foreach ($resource::encryptedFields() as $key => $value) {
            if ($key) {
                self::$encryptedFields[$key] = $value;
            } else {
                self::$encryptedFields[$value] = 'AES-256-CBC';
            }
        }
    }

    /**
     * Decrypt fields.
     *
     * @param Base\Model $resource Currently processed resource.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function decrypt(Base\Model $resource)
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
