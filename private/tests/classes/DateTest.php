<?php
defined('SYSPATH') or die('No direct access allowed!');

class DateTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Date::toMySQL
     */
    public function testToMySQL_int()
    {
        $this->assertEquals("2010-03-29 21:03:12", Date::toMySql(mktime(21, 3, 12, 3, 29, 2010)));
        $this->assertEquals("2015-11-01 01:59:01", Date::toMySql(mktime(1, 59, 1, 11, 1, 2015)));
    }

    /**
     * @covers Date::toMySQL
     */
    public function testToMySQL_object()
    {
        $this->assertEquals("2010-03-29 21:03:12", Date::toMySql(new DateTime('20100329t210312')));
        $this->assertEquals("2015-11-01 01:59:01", Date::toMySql(new DateTime('20151101t015901')));
    }
}