<?php
/**
 * Wrapper for hashing and encryption using available system methods with fallback for older versions of PHP.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Crypt
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Crypt;

use Core;

/**
 * Crypt Class definition.
 */
class Crypt
{
    /**
     * Two way encryption: encrypt.
     *
     * @param string $data     String to be encrypted.
     * @param string $password Value phrase.
     * @param string $type     Cipher method name.
     *
     * @return string
     */
    public static function encrypt($data, $password, $type)
    {
        if ($data) {
            return openssl_encrypt($data, $type, $password, 0, Core\Config()->DB['crypt_vector']);
        }

        return '';
    }

    /**
     * Two way encryption: decrypt.
     *
     * @param string $data     String to be encrypted.
     * @param string $password Value phrase.
     * @param string $type     Cipher method name.
     *
     * @return string
     */
    public static function decrypt($data, $password, $type)
    {
        if ($data) {
            return openssl_decrypt($data, $type, $password, 0, Core\Config()->DB['crypt_vector']);
        }

        return '';
    }

    /**
     * Encrypts a string.
     *
     * @param string $string String to be encrypted.
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function hash($string)
    {
        $_hash = new PasswordHash(12, false);

        return $_hash->hashPassword($string);
    }

    /**
     * Compare encrypted string with a supplied pattern.
     *
     * @param string $hash   Hash representation.
     * @param string $string Password phrase to check against.
     *
     * @access public
     * @static
     *
     * @return boolean
     */
    public static function hashCompare($hash, $string)
    {
        $_hash = new PasswordHash(12, false);

        return $_hash->checkPassword($string, $hash);
    }
}
