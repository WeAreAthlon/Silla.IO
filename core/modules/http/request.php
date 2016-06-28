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

namespace Core\Modules\Http;

use Core;

/**
 * Class Request definition.
 */
final class Request
{
    /**
     * Request string path.
     *
     * @var string
     */
    private $path = '';

    /**
     * Request elements.
     *
     * @var array
     */
    private $elements = array();

    /**
     * Request context.
     *
     * @var array
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
     * @param array  $mode     Request Mode.
     * @param string $path     Request string path.
     * @param array  $context  Request Context Server data.
     */
    public function __construct(array $mode, $path, array $context)
    {
        $this->elements = array();
        $this->context  = $context;
        $this->mode     = $mode;
        $this->token    = '';
        $this->path     = $path ? Core\Utils::replaceFirstOccurrence($this->mode['url'], '', $path) : '/';

        /* Make all routed variables accessible as a GET variables */
        $this->context['_GET'] = array_merge($this->context['_GET'], $this->elements);
    }

    /**
     * Retrieves the controller action name.
     *
     * @return string
     */
    public function controller()
    {
        return isset($this->elements['controller']) ? $this->elements['controller'] : '';
    }

    /**
     * Retrieves the request action name.
     *
     * @return string
     */
    public function action()
    {
        return isset($this->elements['action']) ? $this->elements['action'] : '';
    }

    /**
     * Retrieves the request query string path.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Sets routed elements.
     *
     * @param mixed $elements Routed elements.
     *
     * @return void
     */
    public function setRoutedElements($elements)
    {
        if (isset($elements['action']) && strpos($elements['action'], '.')) {
            $action = explode('.', $elements['action']);
            $elements['controller'] = isset($action[0]) ? Core\Inflection()->pluralize($action[0]) : '';
            $elements['action'] = isset($action[1]) ? $action[1] : '';
        }

        $this->elements = $elements;

        /* Make all routed variables accessible as a GET variables */
        $this->context['_GET'] = array_merge($this->context['_GET'], $elements);
    }

    /**
     * Retrieves request mode or part of it.
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
        $this->token = self::generateToken();
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
     * @param mixed   $path        Array/String representation of a url.
     * @param array   $parameters  Additional path parameters.
     * @param integer $status      Redirect status code according to HTTP specification (301, 302, 303, 307).
     *
     * @uses   Core\Config()
     * @uses   Core\Router()
     * @example
     * <code>
     *  redirectTo('route_name')
     *  redirectTo('route_name', array(params))
     *  redirectTo(array('for' => 'route_name', 'param' => 'value')
     *  redirectTo('http://www.silla.io')
     *  redirectTo('back') - Redirects back.
     * </code>
     *
     * @return void
     */
    public function redirectTo($path, array $parameters = array(), $status = 302)
    {
        $url = '';

        if (is_array($path)) {
            $url = Core\Config()->urls('relative') . Core\Router()->toUrl($path);
        } elseif ($path === 'back') {
            $url = $this->context['_SERVER']['HTTP_REFERER'];
        } elseif (strpos($path, '/') === false) {
            $url = Core\Config()->urls('relative') . Core\Router()->toUrl(array_merge(array('for' => $path), $parameters));
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
     * @return boolean
     */
    public function is($type)
    {
        $type = strtoupper($type);

        if ($type === 'XHR') {
            return 'XMLHttpRequest' === $this->meta('HTTP_X_REQUESTED_WITH');
        }

        return $type === $this->method();
    }

    /**
     * Specify request token.
     *
     * @param string $token Request token.
     *
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Verifies validity of the request.
     *
     * @return boolean
     */
    public function isValid()
    {
        if ($this->is('post') || $this->is('put') || $this->is('delete') || $this->is('patch')) {
            return $this->variables('_token') && ($this->variables('_token') === $this->token);
        }

        return true;
    }

    /**
     * Modifies the Request method name.
     *
     * @param string $method Request method name.
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->context['_SERVER']['REQUEST_METHOD'] = strtoupper($method);
    }
}
