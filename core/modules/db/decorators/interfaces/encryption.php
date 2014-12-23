<?php
/**
 * Encryption Decorator Interface.
 *
 * @package    Silla
 * @subpackage Core\Modules\DB\Decorators\Interfaces
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  none
 * @licence    GPL http://www.gnu.org/copyleft/gpl.html
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
