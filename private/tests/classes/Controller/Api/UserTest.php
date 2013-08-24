<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_UserTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_User::action_list
     */
    public function testActionList()
    {
        $request  = Request::factory('api/user/list')->login(Owaka::AUTH_ROLE_ADMIN);
        $response = $request->execute();
        $this->assertResponseOK($response);

        $expected = array(
            array(
                'id'       => $this->genNumbers['userBar'],
                'username' => 'userBar',
                'enabled'  => false,
                'admin'    => false
            ),
            array(
                'id'       => $this->genNumbers['userFoo'],
                'username' => 'userFoo',
                'enabled'  => true,
                'admin'    => false
            ),
            array(
                'id'       => Auth::instance()->get_user()->id,
                'username' => 'ut-admin',
                'enabled'  => false,
                'admin'    => true
            ),
        );
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $apiCall, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_User::action_add
     */
    public function testActionAdd()
    {
        $expected             = ORM::factory('User');
        $expected->email      = 'test@thomasmuguet.info';
        $expected->username   = 'ut';
        $expected->logins     = 0;
        $expected->last_login = NULL;

        $request  = Request::factory('api/user/add')->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $request->post('email', $expected->email);
        $request->post('username', $expected->username);
        $request->post('password', 'test');
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $actual       = ORM::factory('User')->where('username', '=', 'ut')->find();
        $this->assertTrue($actual->loaded());
        $expected->id = $actual->id;
        $expected->challenge = $actual->challenge;
        $expected->password = $expected->generateNewPassword($actual->challenge, 'test');
        
        $this->assertEquals(array("user" => $actual->id), $apiCall, "Incorrect API result");
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of User does not match'
            );
        }
    }

    /**
     * @covers Controller_Api_User::action_add
     */
    public function testActionAddAdmin()
    {
        $expected             = ORM::factory('User');
        $expected->email      = 'test@thomasmuguet.info';
        $expected->username   = 'ut';
        $expected->logins     = 0;
        $expected->last_login = NULL;

        $request  = Request::factory('api/user/add')->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $request->post('email', $expected->email);
        $request->post('username', $expected->username);
        $request->post('password', 'test');
        $request->post('admin', '1');
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $actual       = ORM::factory('User')->where('username', '=', 'ut')->find();
        $this->assertTrue($actual->loaded());
        $expected->id = $actual->id;
        $expected->challenge = $actual->challenge;
        $expected->password = $expected->generateNewPassword($actual->challenge, 'test');
        $this->assertEquals(array("user" => $actual->id), $apiCall, "Incorrect API result");
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of User does not match'
            );
        }
        $this->assertTrue($actual->has('roles', ORM::factory('Role', array('name' => Owaka::AUTH_ROLE_ADMIN))));
    }

    /**
     * @covers Controller_Api_User::action_add
     */
    public function testActionAddFail()
    {
        $expected           = ORM::factory('User');
        $expected->email    = 'non-valid';
        $expected->username = 'ut';

        $request  = Request::factory('api/user/add')->login(Owaka::AUTH_ROLE_ADMIN);
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

    /**
     * @covers Controller_Api_User::action_add
     */
    public function testActionAddExists()
    {
        $expected           = ORM::factory('User');
        $expected->email    = 'ut' . $this->genNumbers['userFoo'] . '@thomasmuguet.info';
        $expected->username = 'ut';

        $request  = Request::factory('api/user/add')->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $request->post('email', $expected->email);
        $request->post('username', $expected->username);
        $request->post('password', 'test');
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(
                array("errors" => array('email' => 'This email address is already used.')), $apiCall,
                "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_User::action_edit
     */
    public function testActionEdit()
    {
        $expected           = ORM::factory('User', $this->genNumbers['userFoo']);

        $request  = Request::factory('api/user/edit/' . $this->genNumbers['userFoo'])->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $request->post('password', 'new-password');
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("user" => $this->genNumbers['userFoo']), $apiCall, "Incorrect API result");

        $actual = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertTrue($actual->loaded());
        $expected->challenge = $actual->challenge;
        $expected->password = $expected->generateNewPassword($actual->challenge, 'new-password');
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of User does not match'
            );
        }
    }

    /**
     * @covers Controller_Api_User::action_edit
     */
    public function testActionEditFail()
    {
        $request  = Request::factory('api/user/edit/' . $this->genNumbers['userFoo'])->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $request->post('password', '');
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(
                array("errors" => array('password' => 'You must provide a password.')), $apiCall,
                "Incorrect API result"
        ); 
    }

    /**
     * @covers Controller_Api_User::action_edit
     */
    public function testActionEditNotFound()
    {
        $request  = Request::factory('api/user/edit/99999')->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $request->post('password', 'new-password');
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }

    /**
     * @covers Controller_Api_User::action_enable
     */
    public function testActionEnable()
    {
        $role     = ORM::factory('Role', array("name" => Owaka::AUTH_ROLE_LOGIN));
        $expected = ORM::factory('User', $this->genNumbers['userBar']);
        $this->assertFalse($expected->has('roles', $role));

        $request  = Request::factory('api/user/enable/' . $this->genNumbers['userBar'])->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("user" => $this->genNumbers['userBar']), $apiCall, "Incorrect API result");

        $expected->reload();
        $this->assertTrue($expected->has('roles', $role));
    }

    /**
     * @covers Controller_Api_User::action_enable
     */
    public function testActionEnableNotFound()
    {
        $request  = Request::factory('api/user/enable/99999')->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }

    /**
     * @covers Controller_Api_User::action_disable
     */
    public function testActionDisable()
    {
        $role     = ORM::factory('Role', array("name" => Owaka::AUTH_ROLE_LOGIN));
        $expected = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertTrue($expected->has('roles', $role));

        $request  = Request::factory('api/user/disable/' . $this->genNumbers['userFoo'])->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("user" => $this->genNumbers['userFoo']), $apiCall, "Incorrect API result");

        $expected->reload();
        $this->assertFalse($expected->has('roles', $role));
    }

    /**
     * @covers Controller_Api_User::action_disable
     */
    public function testActionDisableNotFound()
    {
        $request  = Request::factory('api/user/disable/99999')->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }

    /**
     * @covers Controller_Api_User::action_delete
     */
    public function testActionDelete()
    {
        $request  = Request::factory('api/user/delete/' . $this->genNumbers['userBar'])->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("user" => $this->genNumbers['userBar']), $apiCall, "Incorrect API result");

        $actual = ORM::factory('User', $this->genNumbers['userBar']);
        $this->assertFalse($actual->loaded());
    }

    /**
     * @covers Controller_Api_User::action_delete
     */
    public function testActionDeleteNotFound()
    {
        $request  = Request::factory('api/user/delete/99999')->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }
}