<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_InstallTest extends TestCase
{

    protected $xmlDataSet = 'install';

    /**
     * @covers Controller_Api_Install::action_do
     */
    public function testActionDo()
    {
        $expected             = ORM::factory('User');
        $expected->email      = 'test@thomasmuguet.info';
        $expected->username   = 'ut';
        $expected->password   = 'test';
        $expected->logins     = 0;
        $expected->last_login = NULL;

        $request  = Request::factory('api/install/do');
        $request->method(Request::POST);
        $request->post('email', $expected->email);
        $request->post('username', $expected->username);
        $request->post('password', 'test');
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array(), $apiCall, "Incorrect API result");

        $actual              = ORM::factory('User')->where('username', '=', 'ut')->find();
        $this->assertTrue($actual->loaded());
        $expected->id        = $actual->id;
        $expected->challenge = $actual->challenge;
        $expected->password  = $expected->generateNewPassword($actual->challenge, 'test');
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of User does not match'
            );
        }
        $this->assertTrue($actual->has('roles', ORM::factory('Role', array('name' => Owaka::AUTH_ROLE_ADMIN))));
        $this->assertTrue($actual->has('roles', ORM::factory('Role', array('name' => Owaka::AUTH_ROLE_LOGIN))));
    }

    /**
     * @covers Controller_Api_Install::action_do
     */
    public function testActionDoFailInstalled()
    {
        $expected             = ORM::factory('User');
        $expected->email      = 'test@thomasmuguet.info';
        $expected->username   = 'ut';
        $expected->password   = 'test';
        $expected->logins     = 0;
        $expected->last_login = NULL;
        $expected->create();

        $request  = Request::factory('api/install/do');
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::GONE, $response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("error" => "Already installed"), $apiCall, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Install::action_do
     */
    public function testActionDoFail()
    {
        $expected           = ORM::factory('User');
        $expected->email    = 'non-valid';
        $expected->username = 'ut';

        $request  = Request::factory('api/install/do');
        $request->method(Request::POST);
        $request->post('email', $expected->email);
        $request->post('username', $expected->username);
        $request->post('password', 'test');
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(
                array("errors" => array('email' => 'You must provide a valid email address.')), $apiCall,
                "Incorrect API result"
        );
    }
}