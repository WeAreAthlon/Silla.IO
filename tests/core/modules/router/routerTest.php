<?php
use Core\Modules\Router\Router;

/**
 * @covers Core\Modules\Router\Router
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Core\Modules\Router\Router
     */
    protected $router;

    protected function setUp()
    {
        $this->router = Router::getInstance();
    }

    /**
     * @covers Core\Modules\Router\Router::__clone
     * @expectedException PHPUnit_Framework_Error
     */
    public function testClonningIsDisallowed()
    {
        clone $this->router;
    }

    /**
     * @covers Core\Modules\Router\Router::setCookie
     */
    public function testSettingCookieForSessionLifetime()
    {
        $this->AssertTrue($this->router->setCookie('test', 'value'));
    }

    /**
     * @covers Core\Modules\Router\Router::setCookie
     */
    public function testSettingCookieForArbitraryLifetime()
    {
        $this->AssertTrue($this->router->setCookie('test', 'value', 3600));
    }

    /**
     * @covers Core\Modules\Router\Router::setCookie
     */
    public function testSettingCookieWithRawValue()
    {
        $this->AssertTrue($this->router->setCookie('test', 'value', true));
    }

    /**
     * @covers Core\Modules\Router\Router::deleteCookie
     */
    public function testSettingCookieForDeletion()
    {
        $this->AssertTrue($this->router->deleteCookie('test'));
    }
}
