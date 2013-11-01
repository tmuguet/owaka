<?php
defined('SYSPATH') or die('No direct access allowed!');

class OwakaTestGetPostactionParameters extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Owaka::getPostactionParameters
     */
    public function testGetPostactionParameters()
    {
        $expected = array(
            'recipients'  => '',
            'on_error'    => 1,
            'on_unstable' => 1,
            'on_ok'       => 1
        );

        $this->assertEquals($expected, Owaka::getPostactionParameters(42, 'mail'));
    }

    /**
     * @covers Owaka::getPostactionParameters
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cannot find post-action nonexisting
     */
    public function testGetPostactionParameters_errorPostaction()
    {
        Owaka::getPostactionParameters(42, 'nonexisting');
    }
}