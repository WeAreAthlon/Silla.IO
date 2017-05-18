<?php
/**
 * Request Module Tests.
 *
 * @package    Silla.IO
 * @subpackage Tests\Modules\Router
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Tests\Modules\Router;

use Core\Modules\Router\Request;

/**
 * @covers \Core\Modules\Router\Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Core\Modules\Router\Request
     */
    protected $request;

    protected function setUp()
    {
        $elements = array(
            'controller' => 'controller',
            'action'     => 'action',
            'id'         => 'id',
        );

        $context       = $GLOBALS;
        $this->request = new Request(array(), $elements, $context);
    }

    /**
     * @covers \Core\Modules\Router\Request::controller
     */
    public function testControllerNameIsAccessible()
    {
        $this->assertEquals('controller', $this->request->controller());
    }

    /**
     * @covers \Core\Modules\Router\Request::action
     */
    public function testActionNameIsAccessible()
    {
        $this->assertEquals('action', $this->request->action());
    }

    /**
     * @covers \Core\Modules\Router\Request::get
     */
    public function testRequestParamsAreAccessible()
    {
        $params = array(
            'controller' => 'controller',
            'action'     => 'action',
            'id'         => 'id',
        );

        $this->assertEquals($params, $this->request->get());
    }

    /**
     * @covers \Core\Modules\Router\Request::get
     */
    public function testRequestParamNameReturnsParamValue()
    {
        $params = array(
            'controller' => 'controller',
            'action'     => 'action',
            'id'         => 'id',
        );
        $this->assertEquals($params['id'], $this->request->get('id'));
    }

    /**
     * @covers \Core\Modules\Router\Request::type
     */
    public function testRequestMethodTypeIsAccessible()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->assertEquals('GET', $this->request->method());
    }

    /**
     * @covers \Core\Modules\Router\Request::is
     */
    public function testCheckRequestMethodTypeIsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue($this->request->is('POST'));
    }

    /**
     * @covers \Core\Modules\Router\Request::is
     */
    public function testCheckRequestMethodTypeIsGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue($this->request->is('GET'));
    }

    /**
     * @covers \Core\Modules\Router\Request::is
     */
    public function testCheckRequestMethodTypeIsXhr()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->request->is('XHR'));
    }
}
