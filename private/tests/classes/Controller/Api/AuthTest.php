<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_authTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_auth::action_login
     */
    public function testActionLoginJson()
    {
        Session::instance()->set('requested_url', 'fooBar');
        
        $response = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('password', 'test')
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array("res"  => "ok", "goto" => 'fooBar'), json_decode($response->body(), TRUE),
                                                                                "Incorrect API result"
        );

        $responseBadPassword = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('password', 'bla')
                ->execute();
        $this->assertResponseOK($responseBadPassword);
        $this->assertEquals(
                array("res" => "ko"), json_decode($responseBadPassword->body(), TRUE), "Incorrect API result"
        );


        $responseRole = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userBar')
                ->post('password', 'test')
                ->execute();
        $this->assertResponseOK($responseRole);
        $this->assertEquals(
                array("res" => "ko"), json_decode($responseRole->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_auth::action_login
     */
    public function testActionLoginPlain()
    {
        Session::instance()->set('requested_url', 'fooBar');
        
        $response = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('password', 'test')
                ->post('plain', '1')
                ->execute();
        $this->assertResponseRedirected($response, '/fooBar');

        $responseBadPassword = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('password', 'bla')
                ->post('plain', '1')
                ->execute();
        $this->assertResponseRedirected($responseBadPassword, '/login');

        $responseRole = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userBar')
                ->post('password', 'test')
                ->post('plain', '1')
                ->execute();
        $this->assertResponseRedirected($responseRole, '/login');
    }

    /**
     * @covers Controller_Api_auth::action_logout
     */
    public function testActionLogout()
    {
        $response = Request::factory('api/auth/logout/')
                ->login()
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array("res" => "ok"), json_decode($response->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_auth::action_loggedin
     */
    public function testActionLoggedin()
    {
        $responseKo = Request::factory('api/auth/loggedin/')
                ->execute();
        $this->assertResponseOK($responseKo);
        $this->assertEquals(
                array("res" => "ko"), json_decode($responseKo->body(), TRUE), "Incorrect API result"
        );

        $responseOk = Request::factory('api/auth/loggedin/')
                ->login()
                ->execute();
        $this->assertResponseOK($responseOk);
        $this->assertEquals(
                array("res" => "ok"), json_decode($responseOk->body(), TRUE), "Incorrect API result"
        );
    }
}