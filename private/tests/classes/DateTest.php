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

    /**
     * @covers Date::loose_span
     */
    public function testLooseSpan()
    {
        $this->assertEquals('+ 2year', Date::loose_span(Date::YEAR * 2, 0));
        $this->assertEquals('+ 1year', Date::loose_span(Date::YEAR * 1, 0));
        $this->assertEquals('+ 10month', Date::loose_span(Date::MONTH * 10, 0));
        $this->assertEquals('+ 1month', Date::loose_span(Date::MONTH, 0));
        $this->assertEquals('+ 2week', Date::loose_span(Date::WEEK * 2, 0));
        $this->assertEquals('+ 1week', Date::loose_span(Date::WEEK, 0));
        $this->assertEquals('+ 3day', Date::loose_span(Date::DAY * 3, 0));
        $this->assertEquals('+ 1day', Date::loose_span(Date::DAY, 0));
        $this->assertEquals('+ 20hour', Date::loose_span(Date::HOUR * 20, 0));
        $this->assertEquals('+ 1hour', Date::loose_span(Date::HOUR, 0));
        $this->assertEquals('+ 59minute', Date::loose_span(Date::MINUTE * 59, 0));
        $this->assertEquals('+ 1minute', Date::loose_span(Date::MINUTE, 0));
        $this->assertEquals('+ 59second', Date::loose_span(59, 0));
        $this->assertEquals('+ 1second', Date::loose_span(1, 0));
        $this->assertEquals('never', Date::loose_span(0));
    }
}