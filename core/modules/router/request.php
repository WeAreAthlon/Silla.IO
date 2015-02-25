<?php
/**
 * Router Request.
 *
 * @package    Silla.IO
 * @subpackage Core\Modules\Router
 * @author     Plamen Nikolov <plamen@athlonsofia.com>
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Core\Modules\Router;

use Core;

/**
 * Class Request definition.
 */
final class Request
{
    /**
     * Request elements.
     *
     * @var array
     * @access private
     */
    private $elements = array();

    /**
     * Request context.
     *
     * @var array
     * @access private
     */
    private $context;

    /**
     * Token value.
     *
     * Used for validation of the request(e.g prevent CSRF).
     *
     * @var string
     */
    private $token;

    /**
     * Request mode.
     *
     * @var array
     */
    private $mode;

    /**
     * Init actions.
     *
     * Renew token on each request.
     *
     * @param array $mode     Request Mode.
     * @param array $elements Request Elements. All Routed variables.
     * @param array $context  Request Context Server data.
     */
    public function __construct(array $mode, array $elements, array &$context)
    {
        $this->elements = $elements;
        $this->context  = $context;
        $this->mode     = $mode;
        $this->token    = Core\Session()->get('_token');

        if (!$this->token) {
            $this->regenerateToken();
        }

        /* Make all routed variables accessible as a GET variables */
        $this->context['_GET'] = array_merge($this->context['_GET'], $elements);
    }

    /**
     * Retrieves the controller action name.
     *
     * @access public
     *
     * @return string
     */
    public function controller()
    {
        return $this->elements['controller'];
    }

    /**
     * Retrieves the request action name.
     *
     * @access public
     *
     * @return string
     */
    public function action()
    {
        return $this->elements['action'];
    }

    /**
     * Retrives request mode or part of it.
     *
     * @param string $segment Mode segment name.
     *
     * @return array
     */
    public function mode($segment = null)
    {
        if ($segment && isset($this->mode[$segment])) {
            return $this->mode[$segment];
        }

        return $this->mode;
    }

    /**
     * Retrieves the GET request params - $context[_GET] contents.
     *
     * @param string $key Name of the parameter(optional).
     *
     * @access public
     *
     * @return mixed
     */
    public function get($key = null)
    {
        if ($key) {
            return isset($this->context['_GET'][$key]) ? $this->context['_GET'][$key] : null;
        }

        return $this->context['_GET'];
    }

    /**
     * Retrieves the POST request params - $context[_POST] contents.
     *
     * @param string $key Name of the parameter(optional).
     *
     * @access public
     *
     * @return mixed
     */
    public function post($key = null)
    {
        if ($key) {
            return isset($this->context['_POST'][$key]) ? $this->context['_POST'][$key] : null;
        }

        return $this->context['_POST'];
    }

    /**
     * Retrieves the FILES request params - $context[_FILES] contents.
     *
     * @param string $key Name of the parameter(optional).
     *
     * @access public
     *
     * @return mixed
     */
    public function files($key = null)
    {
        if ($key) {
            return isset($this->context['_FILES'][$key]) ? $this->context['_FILES'][$key] : null;
        }

        return $this->context['_FILES'];
    }

    /**
     * Retrieves the Request meta params - $context[_SERVER] contents.
     *
     * @param string $key Name of the parameter(optional).
     *
     * @return mixed
     */
    public function meta($key = null)
    {
        if ($key) {
            return isset($this->context['_SERVER'][$key]) ? $this->context['_SERVER'][$key] : null;
        }

        return $this->context['_SERVER'];
    }

    /**
     * Retrieves all Request variables params - $context[_REQUEST] contents.
     *
     * @param string $key Name of the parameter(optional).
     *
     * @return mixed
     */
    public function variables($key = null)
    {
        if ($key) {
            return isset($this->context['_REQUEST'][$key]) ? $this->context['_REQUEST'][$key] : null;
        }

        return $this->context['_REQUEST'];
    }

    /**
     * Retrieves all Request variables params - $context[_COOKIE] contents.
     *
     * @param string $key Name of the parameter(optional).
     *
     * @return mixed
     */
    public function cookies($key = null)
    {
        if ($key) {
            return isset($this->context['_COOKIE'][$key]) ? $this->context['_COOKIE'][$key] : null;
        }

        return $this->context['_COOKIE'];
    }

    /**
     * Type of the request.
     *
     * @access public
     *
     * @return string
     */
    public function type()
    {
        return isset($this->context['_SERVER']['SERVER_PROTOCOL']) ?
            $this->context['_SERVER']['SERVER_PROTOCOL'] : null;
    }

    /**
     * Method of the request.
     *
     * @access public
     *
     * @return string
     */
    public function method()
    {
        return isset($this->context['_SERVER']['REQUEST_METHOD']) ?
            strtoupper($this->context['_SERVER']['REQUEST_METHOD']) : null;
    }

    /**
     * Get current Request token.
     *
     * @return string
     */
    public function token()
    {
        return $this->token;
    }

    /**
     * Regenerates Request token.
     *
     * @return void
     */
    public function regenerateToken()
    {
        $token = self::generateToken();
        Core\Session()->set('_token', $token);

        $this->token = $token;
    }

    /**
     * Generates a unique token.
     *
     * @return string
     */
    public static function generateToken()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * Redirects the browser to a specified target.
     *
     * @param mixed   $url    Array/String representation of url.
     * @param integer $status Redirect status code according to HTTP specification (301, 302, 303, 307).
     *
     * @access public
     * @uses   Core\Config()
     * @uses   Core\Router()
     * @example
     * <code>
     *  redirectTo(array('action' => 'show', 'id' => 5))
     *  redirectTo('http://www.athlonproduction.com')
     *  redirectTo('back') - Only current controller action name.
     * </code>
     *
     * @return void
     */
    public function redirectTo($url, $status = 302)
    {
        if (is_array($url)) {
            $url = Core\Config()->urls('relative') . Core\Router()->toUrl($url);
        } elseif ($url === 'back') {
            $url = $this->context['_SERVER']['HTTP_REFERER'];
        } elseif (strpos($url, '/') === false) {
            $url = Core\Config()->urls('relative') .
                Core\Router()->toUrl(array('controller' => $this->controller(), 'action' => $url));
        }

        if (headers_sent() || $this->is('xhr')) {
            echo '<script type="text/javascript">' .
                 "setTimeout(function() { location.href = '{$url}'; }, 0);" .
                 '</script>';
            exit;
        }

        switch ($status) {
            case 301:
                $status = '301 Moved Permanently';
                break;

            case 303:
                $status = '303 See Other';
                break;

            case 307:
                $status = '307 Temporary Redirect';
                break;

            default:
                $status = '302 Found';
                break;
        }

        header($this->type() . ' ' . $status);
        header('Location: ' . str_replace('&amp;', '&', $url));

        exit;
    }

    /**
     * Checks the type of the request.
     *
     * @param string $type Type of the request to check.
     *
     * @access public
     *
     * @return boolean
     */
    public function is($type)
    {
        $type = strtoupper($type);

        if ($type === 'XHR') {
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
        }

        return strtoupper($type) === $this->method();
    }
}
