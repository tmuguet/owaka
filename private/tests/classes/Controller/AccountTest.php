<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_AccountTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Account::action_edit
     */
    public function testActionEdit()
    {
        $response = Request::factory('account/edit')->login()->execute();
        $this->assertResponseOK($response);

        $expected = View::factory('account/edit');
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Account::action_delete
     */
    public function testActionDelete()
    {
        $response = Request::factory('account/delete')->login()->execute();
        $this->assertResponseOK($response);

        $expected = View::factory('account/delete');
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }
}