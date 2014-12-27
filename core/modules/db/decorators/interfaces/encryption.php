<?php
/**
 * Encryption Decorator Interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Decorators\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Core\Modules\DB\Decorators\Interfaces;

/**
 * Serialize Decorator for encryption of object fields
 */
interface Encryption
{
    /**
     * Encrypted fields container.
     *
     * @static
     *
     * @return array
     */
    public static function encryptedFields();
}
