<?php
use Core\Modules\Router\Routes;

/**
 * @covers Core\Modules\Router\Routes
 */
class RoutesTest extends PHPUnit_Framework_TestCase
{
    protected $routes;

    protected function setUp()
    {
        $this->routes = Routes::getInstance();
    }

    /**
     * @covers Core\Modules\Router\Routes::__clone
     * @expectedException PHPUnit_Framework_Error
     */
    public function testClonningIsDisallowed()
    {
        clone $this->routes;
    }

    /**
     * @covers Core\Modules\Router\Routes::add
     */
    public function testAddingRoutesAcceptsArray()
    {
        $route = array ('url' => 'url', 'maps_to' => 'maps_to');
        $this->assertTrue($this->routes->add($route));

        return $route;
    }

    /**
     * @covers Core\Modules\Router\Routes::add
     * @depends testAddingRoutesAcceptsArray
     */
    public function testAddingRoutesReturnsTrue(array $route)
    {
        $this->assertTrue($this->routes->add($route));
    }

    /**
     * @covers Core\Modules\Router\Routes::getAll
     */
    public function testGettingAllRoutesReturnsAnArray()
    {
        $this->assertTrue(is_array($this->routes->getAll()));
    }

    /**
     * @covers Core\Modules\Router\Routes::toRoute
     */
    public function testPreparingUrlSplitsAndTrimsBySlash()
    {
        $this->assertEquals(
            array('controller', 'action', 'id'),
            $this->routes->toRoute('/controller/action/id/')
        );
    }
}
