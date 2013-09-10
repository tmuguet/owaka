<?php
defined('SYSPATH') or die('No direct access allowed!');

class NumTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Num::percent
     */
    public function testPercent()
    {
        $this->assertEquals(0, Num::percent(0, 5));
        $this->assertEquals(100, Num::percent(0, 0));
        $this->assertEquals(50, Num::percent(1, 2));
    }
}