<?php
defined('SYSPATH') or die('No direct access allowed!');

class RequestTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Request::login
     */
    public function testLogin()
    {
        $target = new Request('fake');
        $target->login();
        $this->assertTrue(Auth::instance()->logged_in());
    }
}