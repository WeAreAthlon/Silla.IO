<?php
use Core\Modules\DB;

class FilterTest extends PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $filter = new DB\Filter('id', DB\FilterOp::EQ, 3);

        $this->assertEquals('id', $filter->property);
        $this->assertEquals(DB\FilterOp::EQ, $filter->operator);
        $this->assertEquals(3, $filter->value);

        $parsed_filter = new DB\Filter('id = 3');

        $this->assertEquals($filter, $parsed_filter);
    }

    public function testLogicalComparisons()
    {
        /* Create simple filters */
        $filter = new DB\Filter('id', DB\FilterOp::EQ, 3);
        $filter2 = new DB\Filter('name', DB\FilterOp::EQ, 'test name');
        $filter3 = new DB\Filter('date', DB\FilterOp::GT, '10.10.2014');

        /* Test simple `AND` comparisons */
        $combined_filter = DB\Filter::combine([$filter, $filter2], 'all');
        $this->assertEquals($filter, $combined_filter->expressions['and'][0]);
        $this->assertEquals($filter2, $combined_filter->expressions['and'][1]);

        /* Test comaprison by extendig existing Filter */
        $cloned_filter = clone $filter;
        $cloned_filter->all($filter2);
        $this->assertEquals($combined_filter, $cloned_filter);

        /* Test comaprison by extendig existing Filter with multiple objects */
        $combined_filter = DB\Filter::combine([$filter, $filter2, $filter3], 'all');
        $cloned_filter = clone $filter;
        $cloned_filter->all(array($filter2, $filter3));
        $this->assertEquals($combined_filter, $cloned_filter);

        /* Test simple `OR` comparisons */
        $combined_filter = DB\Filter::combine([$filter, $filter2], 'any');
        $this->assertEquals($filter, $combined_filter->expressions['or'][0]);
        $this->assertEquals($filter2, $combined_filter->expressions['or'][1]);

        /* Test comaprison by extendig existing Filter */
        $cloned_filter = clone $filter;
        $cloned_filter->any($filter2);
        $this->assertEquals($combined_filter, $cloned_filter);

        /* Test comaprison by extendig existing Filter with multiple objects */
        $combined_filter = DB\Filter::combine([$filter, $filter2, $filter3], 'any');
        $cloned_filter = clone $filter;
        $cloned_filter->any(array($filter2, $filter3));
        $this->assertEquals($combined_filter, $cloned_filter);
    }

    public function testComplexCreation()
    {
        $filter = new DB\Filter('id = 3 && name = test name || date > 20.10.2014');

        $combined_filter = DB\Filter::combine([new DB\Filter('id', DB\FilterOp::EQ, 3), new DB\Filter('name', DB\FilterOp::EQ, 'test name')], 'all')->any(new DB\Filter('date', DB\FilterOp::GT, '20.10.2014'));

        $this->assertEquals($filter, $combined_filter, 'Complex string combination of expressions');
    }
}
