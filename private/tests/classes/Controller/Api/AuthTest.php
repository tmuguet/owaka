<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_AuthTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Auth::action_challenge
     */
    public function testActionChallenge()
    {
        $user = ORM::factory('User', $this->genNumbers['userFoo']);

        $response = Request::factory('api/auth/challenge/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array("challenge" => $user->challenge), json_decode($response->body(), TRUE), "Incorrect API result"
        );


        $responseNon = Request::factory('api/auth/challenge/')
                ->method(Request::POST)
                ->post('user', 'userfogf')
                ->execute();
        $this->assertResponseOK($responseNon);
        $result = json_decode($responseNon->body(), TRUE);
        $this->assertArrayHasKey("challenge", $result, "Incorrect API result");
        
        $responseNon2 = Request::factory('api/auth/challenge/')
                ->method(Request::POST)
                ->post('user', 'userfogf')
                ->execute();
        $this->assertResponseOK($responseNon2);
        $this->assertEquals($result, json_decode($responseNon2->body(), TRUE), "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Auth::action_login
     */
    public function testActionLogin()
    {
        Session::instance()->set('requested_url', 'fooBar');

        $user              = ORM::factory('User', $this->genNumbers['userFoo']);
        $challengeResponse = Auth::instance()->hashKey('test', $user->challenge);
        $response          = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('response', $challengeResponse)
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array("goto" => 'fooBar'), json_decode($response->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_Auth::action_login
     */
    public function testActionLoginBadChallenge()
    {
        Session::instance()->set('requested_url', 'fooBar');

        $challengeResponse = Auth::instance()->hashKey('test', 'bad');
        $response          = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('response', $challengeResponse)
                ->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $this->assertEquals(
                array('errors' => array('user'     => 'Bad credentials', 'password' => '')),
                json_decode($response->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_Auth::action_login
     */
    public function testActionLoginBadPassword()
    {
        Session::instance()->set('requested_url', 'fooBar');

        $user              = ORM::factory('User', $this->genNumbers['userFoo']);
        $challengeResponse = Auth::instance()->hashKey('test2', $user->challenge);
        $response          = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userFoo')
                ->post('response', $challengeResponse)
                ->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $this->assertEquals(
                array('errors' => array('user'     => 'Bad credentials', 'password' => '')),
                json_decode($response->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_Auth::action_login
     */
    public function testActionLoginBadRole()
    {
        Session::instance()->set('requested_url', 'fooBar');

        $user              = ORM::factory('User', $this->genNumbers['userBar']);
        $challengeResponse = Auth::instance()->hashKey('test', $user->challenge);
        $response          = Request::factory('api/auth/login/')
                ->method(Request::POST)
                ->post('user', 'userBar')
                ->post('response', $challengeResponse)
                ->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $this->assertEquals(
                array('errors' => array('user'     => 'Bad credentials', 'password' => '')),
                json_decode($response->body(), TRUE), "Incorrect API result"
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