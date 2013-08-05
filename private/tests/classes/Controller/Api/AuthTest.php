<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_AuthTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Auth::action_login
     */
    public function testActionLogin()
    {
        Session::instance()->set('requested_url', 'fooBar');

        $response = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('password', 'test')
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array("goto" => 'fooBar'), json_decode($response->body(), TRUE), "Incorrect API result"
        );

        $responseBadPassword = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('password', 'bla')
                ->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $responseBadPassword);
        $this->assertEquals(
                array("error" => "Bad credentials"), json_decode($responseBadPassword->body(), TRUE),
                                                                 "Incorrect API result"
        );


        $responseRole = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userBar')
                ->post('password', 'test')
                ->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $responseRole);
        $this->assertEquals(
                array("error" => "Bad credentials"), json_decode($responseRole->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_Auth::action_logout
     */
    public function testActionLogout()
    {
        $response = Request::factory('api/auth/logout/')
                ->login()
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array(), json_decode($response->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_Auth::action_loggedin
     */
    public function testActionLoggedin()
    {
        $responseKo = Request::factory('api/auth/loggedin/')
                ->execute();
        $this->assertResponseOK($responseKo);
        $this->assertEquals(
                array("loggedin" => "ko"), json_decode($responseKo->body(), TRUE), "Incorrect API result"
        );

        $responseOk = Request::factory('api/auth/loggedin/')
                ->login()
                ->execute();
        $this->assertResponseOK($responseOk);
        $this->assertEquals(
                array("loggedin" => "ok"), json_decode($responseOk->body(), TRUE), "Incorrect API result"
        );
    }
}