<?php
/**
 * Captcha Helper.
 *
 * @package    Silla.IO
 * @subpackage CMS\Helpers
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace CMS\Helpers;

use Core;

/**
 * Captcha Message Helper Class definition.
 */
class Captcha
{
    /**
     * Fetches the HTML for the captcha.
     *
     * @param array $configuration Configuration data.
     *
     * @static
     * @uses   Core\Session()
     *
     * @return \Captcha\Captcha
     */
    public static function get(array $configuration)
    {
        $captcha = Core\Session()->get('captcha');

        if (!$captcha && Core\Session()->get('login_attempts') >= Core\Config()->CAPTCHA_LOGIN_ATT) {
            $captcha = new \Captcha\Captcha();
            $captcha->setPublicKey($configuration['public_key']);
            $captcha->setPrivateKey($configuration['private_key']);

            Core\Session()->set('captcha', $captcha);
        }

        return $captcha;
    }

    /**
     * Check if submitted captcha is valid.
     *
     * @param \Captcha\Captcha $captcha Captcha instance.
     *
     * @return boolean
     */
    public static function isValid(\Captcha\Captcha $captcha)
    {
        return $captcha->check()->isValid();
    }

    /**
     * Retrieve captcha Template.
     *
     * @param \Captcha\Captcha $captcha Captcha instance.
     *
     * @return string
     */
    public static function getTemplate(\Captcha\Captcha $captcha)
    {
        return $captcha->html();
    }
}
