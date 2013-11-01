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
        $c = Kohana::$config->load('owaka');
        $this->assertEquals($c->get('base') . '/', URL::base());
    }

    /**
     * @covers URL::site
     */
    public function testSite()
    {
        $c = Kohana::$config->load('owaka');
        $this->assertEquals($c->get('base') . '/foo/bar', URL::site('foo/bar'));
    }
}