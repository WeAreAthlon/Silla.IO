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

namespace Core\Modules\Router;

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
     * Retrives Response contents.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Verifies response precense.
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
                $header['code'] = isset($header['code']) ? $header['code'] : null;
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
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
