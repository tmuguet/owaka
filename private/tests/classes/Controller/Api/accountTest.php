<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_accountTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_account::action_edit
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
        $this->assertEquals(array("res" => "ok"), $apiCall, "Incorrect API result");

        $actual = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertTrue($actual->loaded());
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of User does not match'
            );
        }
    }

    /**
     * @covers Controller_Api_account::action_delete
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
        $this->assertEquals(array("res" => "ok"), $apiCall, "Incorrect API result");

        $actual = ORM::factory('User', $this->genNumbers['userFoo']);
        $this->assertFalse($actual->loaded());
    }
}