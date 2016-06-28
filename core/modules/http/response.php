<?php
/**
 * Router Response.
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
final class Response
{
    /**
     * Response content.
     *
     * @var string
     * @access private
     */
    private $content = '';

    /**
     * Response headers.
     *
     * @var array
     * @access private
     */
    private $headers = array();

    /**
     * Init actions.
     *
     * @param string $content Response contents.
     * @param array  $headers Response headers.
     */
    public function __construct($content = '', array $headers = array())
    {
        if ($content) {
            $this->setContent($content);
        }

        if ($headers) {
            $this->addHeaders($headers);
        }
    }

    /**
     * Sets response content.
     *
     * @param string $content Response content.
     *
     * @throws \InvalidArgumentException Response content must be a string.
     *
     * @return void
     */
    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException('Response content must be a string');
        }

        $this->content = $content;
    }

    /**
     * Retrieves response content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Verifies response content presence.
     *
     * @return boolean
     */
    public function hasContent()
    {
        return !empty($this->content);
    }


    /**
     * Adds multiple headers.
     *
     * @param array $headers Array of string representations of HTTP headers.
     *
     * @uses addHeader()
     *
     * @return void
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $header) {
            if (is_array($header)) {
                $header['replace'] = isset($header['replace']) ? $header['replace'] : true;
                $header['code']    = isset($header['code']) ? $header['code'] : null;
                $this->addHeader($header['string'], $header['replace'], $header['code']);
            } else {
                $this->addHeader($header);
            }
        }
    }

    /**
     * Adds a single header.
     *
     * @param string  $string             The header string.
     * @param boolean $replace            Indicates whether the header should replace a previous similar header.
     * @param integer $http_response_code Forces the HTTP response code to the specified value.
     *
     * @throws \InvalidArgumentException Response output header must be a valid string.
     *
     * @return void
     */
    public function addHeader($string, $replace = true, $http_response_code = null)
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException('Response output header must be a valid string.');
        }

        $this->headers[] = array('string' => $string, 'replace' => $replace, 'code' => $http_response_code);
    }

    /**
     * Remove headers.
     *
     * @param array $headers Header string.
     *
     * @uses removeHeader()
     *
     * @return void
     */
    public function removeHeaders(array $headers)
    {
        foreach ($headers as $header) {
            $this->removeHeader($header);
        }
    }

    /**
     * Removes a single header.
     *
     * @param string $header Representation of a HTTP header.
     *
     * @throws \InvalidArgumentException Response output header must be a valid string.
     *
     * @return void
     */
    public function removeHeader($header)
    {
        if (!is_string($header)) {
            throw new \InvalidArgumentException('Response output header must be a valid string.');
        }

        foreach ($this->headers as $key => $_header) {
            if ($header === $_header['string']) {
                unset($this->headers[$key]);
            }
        }
    }

    /**
     * Verifies whether there is at least one header.
     *
     * @return boolean
     */
    public function hasHeaders()
    {
        return !empty($this->headers);
    }

    /**
     * Get Headers.
     *
     * @return array HTTP headers.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets HTTP Response Header by specifying a HTTP code.
     *
     * @param integer $code Valid HTTP Code.
     *
     * @throws \InvalidArgumentException Unknown http status code.
     *
     * @return integer Valid HTTP Code.
     */
    public function setHttpResponseCode($code)
    {
        switch ($code) {
            case 100:
                $text = 'Continue';
                break;
            case 101:
                $text = 'Switching Protocols';
                break;
            case 200:
                $text = 'OK';
                break;
            case 201:
                $text = 'Created';
                break;
            case 202:
                $text = 'Accepted';
                break;
            case 203:
                $text = 'Non-Authoritative Information';
                break;
            case 204:
                $text = 'No Content';
                break;
            case 205:
                $text = 'Reset Content';
                break;
            case 206:
                $text = 'Partial Content';
                break;
            case 300:
                $text = 'Multiple Choices';
                break;
            case 301:
                $text = 'Moved Permanently';
                break;
            case 302:
                $text = 'Moved Temporarily';
                break;
            case 303:
                $text = 'See Other';
                break;
            case 304:
                $text = 'Not Modified';
                break;
            case 305:
                $text = 'Use Proxy';
                break;
            case 400:
                $text = 'Bad Request';
                break;
            case 401:
                $text = 'Unauthorized';
                break;
            case 402:
                $text = 'Payment Required';
                break;
            case 403:
                $text = 'Forbidden';
                break;
            case 404:
                $text = 'Not Found';
                break;
            case 405:
                $text = 'Method Not Allowed';
                break;
            case 406:
                $text = 'Not Acceptable';
                break;
            case 407:
                $text = 'Proxy Authentication Required';
                break;
            case 408:
                $text = 'Request Time-out';
                break;
            case 409:
                $text = 'Conflict';
                break;
            case 410:
                $text = 'Gone';
                break;
            case 411:
                $text = 'Length Required';
                break;
            case 412:
                $text = 'Precondition Failed';
                break;
            case 413:
                $text = 'Request Entity Too Large';
                break;
            case 414:
                $text = 'Request-URI Too Large';
                break;
            case 415:
                $text = 'Unsupported Media Type';
                break;
            case 500:
                $text = 'Internal Server Error';
                break;
            case 501:
                $text = 'Not Implemented';
                break;
            case 502:
                $text = 'Bad Gateway';
                break;
            case 503:
                $text = 'Service Unavailable';
                break;
            case 504:
                $text = 'Gateway Time-out';
                break;
            case 505:
                $text = 'HTTP Version not supported';
                break;
            default:
                throw new \InvalidArgumentException('Unknown http status code "' . htmlentities($code) . '"');
                break;
        }

        $this->addHeader(Core\Router()->request->type() . ' ' . $code . ' ' . $text);
        $GLOBALS['http_response_code'] = $code;

        return $code;
    }
}
