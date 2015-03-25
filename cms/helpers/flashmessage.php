<?php
/**
 * Flash Helper.
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
 * Flash Message Helper Class definition.
 */
class FlashMessage
{
    /**
     * Flash message container.
     *
     * @var array
     */
    private static $message = null;

    /**
     * Fetches the flash message and caches it into the static property $message.
     *
     * @access private
     * @static
     * @uses   Core\Session()
     *
     * @return array
     */
    private static function fetch()
    {
        if (null === self::$message) {
            $message = Core\Session()->get('flash_message');

            if (isset($message) && !empty($message)) {
                self::$message = (array)$message;
                Core\Session()->remove('flash_message');
            } else {
                self::$message = array();
            }
        }

        return self::$message;
    }

    /**
     * Returns flash message data.
     *
     * @uses fetch() To fetch the flash message from the user session.
     * @access public
     * @static
     *
     * @return string
     */
    public static function get()
    {
        $message_types = array(
            'success',
            'warning',
            'danger',
        );

        $message = self::fetch();

        if (isset($message) && !empty($message)) {
            $layout = in_array($message['context'], $message_types, true) ? $message['context'] : 'info';

            $flash_data = array(
                'layout' => $layout,
                'message' => $message['message'],
            );

            if ($message['additional'] && is_array($message['additional'])) {
                $flash_data['additional'] = $message['additional'];
            }

            return $flash_data;
        }

        return false;
    }

    /**
     * Save a message in the session array.
     *
     * @param string $message    Message string.
     * @param string $context    Context(optional).
     * @param array  $additional Additional info string(optional).
     *
     * @access public
     * @static
     * @uses   Core\Session()
     *
     * @return void
     */
    public static function set($message, $context = 'default', array $additional = array())
    {
        Core\Session()->set(
            'flash_message',
            array('message' => $message, 'context' => $context, 'additional' => $additional)
        );
    }
}
