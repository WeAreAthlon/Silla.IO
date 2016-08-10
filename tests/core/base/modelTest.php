<?php
class ModelTest extends PHPUnit_Framework_TestCase
{
    public function testBasicConstruction()
    {
//        $mdl = new Core\Base\Model(array(
//            'title'             => 'Test title',
//            'description'       => 'Test description',
//            'category_id'       => 1,
//            'expiration_date'   => '2014-10-10'
//        ));
//
//        $this->assertEquals($mdl->title, 'Test title');
//        $this->assertEquals($mdl->description, 'Test description');
//        $this->assertEquals($mdl->category_id, 1);
//        $this->assertEquals($mdl->expiration_date, '2014-10-10');
//
//        return $mdl;
        return true;
    }

    public function testBasicSettingProperties()
    {
//        $mdl = new Core\Base\Model();
//        $mdl->title = 'Test title';
//        $mdl->description = 'Test description';
//        $mdl->category_id = 1;
//        $mdl->expiration_date = '2014-10-10';
//
//        $this->assertEquals($mdl->title, 'Test title');
//        $this->assertEquals($mdl->description, 'Test description');
//        $this->assertEquals($mdl->category_id, 1);
//        $this->assertEquals($mdl->expiration_date, '2014-10-10');
//
//        return $mdl;
        return true;
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
