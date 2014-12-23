<?php
use Core\Modules\Router\Request;

/**
 * @covers Core\Modules\Router\Request
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    protected $request;

    protected function setUp()
    {
        $elements = array(
            'controller' => 'controller',
            'action' => 'action',
            'params' => array(
                'controller' => 'controller',
                'action' => 'action',
                'id' => 'id'));

        $scope = array();
        $context = array();
        $this->request = new Request(array(), $elements, $scope, $context);
    }

    /**
     * @covers Core\Modules\Router\Request::controller
     */
    public function testControllerNameIsAccessible()
    {
        $this->assertEquals('controller', $this->request->controller());
    }

    /**
     * @covers Core\Modules\Router\Request::action
     */
    public function testActionNameIsAccessible()
    {
        $this->assertEquals('action', $this->request->action());
    }

    /**
     * @covers Core\Modules\Router\Request::params
     */
    public function testRequestParamsAreAccessible()
    {
        $params = array(
            'controller' => 'controller',
            'action' => 'action',
            'id' => 'id');
        $this->assertEquals($params, $this->request->params());
    }

    /**
     * @covers Core\Modules\Router\Request::params
     */
    public function testRequestParamNameReturnsParamValue()
    {
        $params = array(
            'controller' => 'controller',
            'action' => 'action',
            'id' => 'id');
        $this->assertEquals($params['id'], $this->request->params('id'));
    }

    /**
     * @covers Core\Modules\Router\Request::type
     */
    public function testRequestMethodTypeIsAccessible()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertEquals('get', $this->request->type());
    }

    /**
     * @covers Core\Modules\Router\Request::is
     */
    public function testCheckRequestMethodTypeIsPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue($this->request->is('POST'));
    }

    /**
     * @covers Core\Modules\Router\Request::is
     */
    public function testCheckRequestMethodTypeIsGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue($this->request->is('GET'));
    }

    /**
     * @covers Core\Modules\Router\Request::is
     */
    public function testCheckRequestMethodTypeIsXhr()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($this->request->is('XHR'));
    }
}
