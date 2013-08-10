<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_AccountTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Account::action_edit
     */
    public function testActionEdit()
    {
        $expected           = ORM::factory('User', $this->genNumbers['userFoo']);
        Auth::instance()->force_login($expected);
        $expected->password = 'new-password';
        $expected->logins   = 1;

        $request  = Request::factory('api/account/edit');
        $request->method(Request::POST);
        $request->post('password', 'new-password');
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array(), $apiCall, "Incorrect API result");

        $actual = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertTrue($actual->loaded());
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of User does not match'
            );
        }
    }

    /**
     * @covers Controller_Api_Account::action_edit
     */
    public function testActionEditFail()
    {
        $expected = ORM::factory('User', $this->genNumbers['userFoo']);
        Auth::instance()->force_login($expected);

        $request  = Request::factory('api/account/edit');
        $request->method(Request::POST);
        $request->post('password', '');
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $this->assertEquals(
                array('errors' => array('password' => 'You must provide a password.')),
                json_decode($response->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_Account::action_delete
     */
    public function testActionDelete()
    {
        $expected = ORM::factory('User', $this->genNumbers['userFoo']);
        Auth::instance()->force_login($expected);

        $request  = Request::factory('api/account/delete');
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array(), $apiCall, "Incorrect API result");

        $actual = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertFalse($actual->loaded());
    }
}