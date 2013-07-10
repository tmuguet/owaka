<?php
defined('SYSPATH') or die('No direct access allowed!');

class URLTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers URL::base
     */
    public function testBase()
    {
        $this->assertEquals('/', URL::base());
    }
}