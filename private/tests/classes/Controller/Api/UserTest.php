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
                'id'         => $this->genNumbers['userBar'],
                'username'   => 'userBar',
                'email'      => 'ut' . $this->genNumbers['userBar'] . '@thomasmuguet.info',
                'logins'     => 0,
                'last_login' => 0,
                'enabled'    => false,
                'admin'      => false
            ),
            array(
                'id'         => $this->genNumbers['userFoo'],
                'username'   => 'userFoo',
                'email'      => 'ut' . $this->genNumbers['userFoo'] . '@thomasmuguet.info',
                'logins'     => 0,
                'last_login' => 0,
                'enabled'    => true,
                'admin'      => false
            ),
            array(
                'id'         => Auth::instance()->get_user()->id,
                'username'   => 'ut-admin',
                'email'      => 'ut-admin@thomasmuguet.info',
                'logins'     => 1,
                'last_login' => Auth::instance()->get_user()->last_login,
                'enabled'    => false,
                'admin'      => true
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

        $this->rollback();

        $actual              = ORM::factory('User')->where('username', '=', 'ut')->find();
        $this->assertTrue($actual->loaded());
        $expected->id        = $actual->id;
        $expected->challenge = $actual->challenge;
        $expected->password  = $expected->_generateNewPassword($actual->challenge, 'test');

        $this->assertEquals(array("user" => $actual->id), $apiCall, "Incorrect API result");
        foreach (array_keys($actual->list_columns()) as $column) {
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

        $this->rollback();

        $actual              = ORM::factory('User')->where('username', '=', 'ut')->find();
        $this->assertTrue($actual->loaded());
        $expected->id        = $actual->id;
        $expected->challenge = $actual->challenge;
        $expected->password  = $expected->_generateNewPassword($actual->challenge, 'test');
        $this->assertEquals(array("user" => $actual->id), $apiCall, "Incorrect API result");
        foreach (array_keys($actual->list_columns()) as $column) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of User does not match'
            );
        }
        $this->assertTrue($actual->has('roles', Model_Role::getRole(Owaka::AUTH_ROLE_ADMIN)));
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
        $expected = ORM::factory('User', $this->genNumbers['userFoo']);

        $request  = Request::factory('api/user/edit/' . $this->genNumbers['userFoo'])->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $request->post('password', 'new-password');
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("user" => $this->genNumbers['userFoo']), $apiCall, "Incorrect API result");

        $this->rollback();

        $actual              = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertTrue($actual->loaded());
        $expected->challenge = $actual->challenge;
        $expected->password  = $expected->_generateNewPassword($actual->challenge, 'new-password');
        foreach (array_keys($actual->list_columns()) as $column) {
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
                array("errors" => array('password' => 'You must provide a password.')), $apiCall, "Incorrect API result"
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
        $role     = Model_Role::getRole(Owaka::AUTH_ROLE_LOGIN);
        $expected = ORM::factory('User', $this->genNumbers['userBar']);
        $this->assertFalse($expected->has('roles', $role));

        $request  = Request::factory('api/user/enable/' . $this->genNumbers['userBar'])->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("user" => $this->genNumbers['userBar']), $apiCall, "Incorrect API result");

        $this->rollback();

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
        $role     = Model_Role::getRole(Owaka::AUTH_ROLE_LOGIN);
        $expected = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertTrue($expected->has('roles', $role));

        $request  = Request::factory('api/user/disable/' . $this->genNumbers['userFoo'])->login(Owaka::AUTH_ROLE_ADMIN);
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("user" => $this->genNumbers['userFoo']), $apiCall, "Incorrect API result");

        $this->rollback();

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

        $this->rollback();

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