<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_AuthTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Auth::action_login
     */
    public function testActionLogin()
    {
        $response = Request::factory('login')->execute();
        $this->assertResponseOK($response);

        $expected = View::factory('login');
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Auth::action_login
     */
    public function testActionLoginFrom()
    {
        $response = Request::factory('login')->execute();
        $this->assertResponseOK($response);

        $expected = View::factory('login');
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Auth::action_login
     */
    public function testActionLoginLoggedin()
    {
        $response = Request::factory('login')->login()->execute();
        $this->assertResponseStatusEquals(302, $response);
    }

    /**
     * @covers Controller_Auth::action_logout
     */
    public function testActionLogout()
    {
        $response = Request::factory('logout')->login()->execute();
        $this->assertResponseStatusEquals(302, $response);
    }
}