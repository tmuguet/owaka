<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_ManagerTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Manager::action_add
     */
    public function testActionAdd()
    {
        $response = Request::factory('manager/add')->login()->execute();
        $this->assertResponseOK($response);

        $reports               = array();
        $reports['processor1'] = Controller_Processor_processor1::getInputReports();
        $reports['processor2'] = Controller_Processor_processor2::getInputReports();

        $expected = View::factory('manager')
                ->set('project', ORM::factory('Project'))
                ->set('reports', $reports);
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Manager::action_edit
     */
    public function testActionEdit()
    {
        $response = Request::factory('manager/edit/' . $this->genNumbers['ProjectFoo'])->login()->execute();
        $this->assertResponseOK($response);

        $reports               = array();
        $reports['processor1'] = Controller_Processor_processor1::getInputReports();
        $reports['processor2'] = Controller_Processor_processor2::getInputReports();

        $expected = View::factory('manager')
                ->set('project', ORM::factory('Project', $this->genNumbers['ProjectFoo']))
                ->set('reports', $reports);
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }
}