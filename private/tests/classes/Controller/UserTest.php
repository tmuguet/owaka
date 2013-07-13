<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_UserTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_User::action_add
     */
    public function testActionAdd()
    {
        $response = Request::factory('user/add')->login()->execute();
        $this->assertResponseOK($response);

        $expected = View::factory('user/add');
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_User::action_edit
     */
    public function testActionEdit()
    {
        $response = Request::factory('user/edit/' . $this->genNumbers['userFoo'])->login()->execute();
        $this->assertResponseOK($response);

        $expected = View::factory('user/edit')
                ->set('user', ORM::factory('User', $this->genNumbers['userFoo']));
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }
}