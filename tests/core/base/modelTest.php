<?php
/**
 * Base Model Tests.
 *
 * @package    Silla.IO
 * @subpackage Tests\Core\Base
 * @copyright  Copyright (c) 2015, Silla.io
 * @license    http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3.0 (GPLv3)
 */

namespace Tests\Core\Base;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    protected $model;

    protected function setUp()
    {
        $this->model = $this->getMockForAbstractClass('Core\\Base\\Model', array(), '', false);
        $reflection  = new \ReflectionClass($this->model);
        $reflection->setStaticPropertyValue('tableName', 'tests');
    }

    public function testBasicConstruction()
    {
        $mdl = new $this->model(array(
            'title'           => 'Test title',
            'description'     => 'Test description',
            'category_id'     => 1,
            'expiration_date' => '2014-10-10',
        ));

        $this->assertEquals($mdl->title, 'Test title');
        $this->assertEquals($mdl->description, 'Test description');
        $this->assertEquals($mdl->category_id, 1);
        $this->assertEquals($mdl->expiration_date, '2014-10-10');

        return $mdl;
    }

    public function testBasicSettingProperties()
    {
        $mdl = new $this->model;

        $mdl->title           = 'Test title';
        $mdl->description     = 'Test description';
        $mdl->category_id     = 1;
        $mdl->expiration_date = '2014-10-10';

        $this->assertEquals($mdl->title, 'Test title');
        $this->assertEquals($mdl->description, 'Test description');
        $this->assertEquals($mdl->category_id, 1);
        $this->assertEquals($mdl->expiration_date, '2014-10-10');

        return $mdl;
    }

    /**
     * @depends testBasicConstruction
     * @depends testBasicSettingProperties
     */
    public function testCompareObjects($mdl, $mdl2)
    {
        $this->assertEquals($mdl, $mdl2);
    }

    public function testFindQuery()
    {
    }
}
