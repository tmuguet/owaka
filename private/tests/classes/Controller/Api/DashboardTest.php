<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_DashboardTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Dashboard::action_duplicate
     */
    public function testActionDuplicateProject()
    {
        $response = Request::factory('api/dashboard/duplicate/project/' . $this->genNumbers['ProjectFoo'] . '/' . $this->genNumbers['ProjectBaz'])
                        ->login()->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(array(), json_decode($response->body(), TRUE), "Incorrect API result");

        $expected = array(
            ORM::factory('Project_Widget', $this->genNumbers['projectFooBackground']),
            ORM::factory('Project_Widget', $this->genNumbers['projectFooLog']),
        );

        $actual = ORM::factory('Project_Widget')
                ->where('project_id', '=', $this->genNumbers['ProjectBaz'])
                ->order_by('type', 'ASC')
                ->find_all();
        for ($i = 0; $i < sizeof($actual); $i++) {
            $expected[$i]->id         = $actual[$i]->id;
            $expected[$i]->project_id = $this->genNumbers['ProjectBaz'];
            foreach (array_keys($actual[$i]->list_columns()) as $column) {
                $this->assertEquals(
                        $expected[$i]->$column, $actual[$i]->$column,
                        'Column ' . $column . ' of Project_Widget[' . $i . '] does not match'
                );
            }
        }
    }

    /**
     * @covers Controller_Api_Dashboard::action_duplicate
     */
    public function testActionDuplicateBuild()
    {
        $response = Request::factory('api/dashboard/duplicate/build/' . $this->genNumbers['ProjectFoo'] . '/' . $this->genNumbers['ProjectBaz'])
                        ->login()->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(array(), json_decode($response->body(), TRUE), "Incorrect API result");

        $expected = array(
            ORM::factory('Build_Widget', $this->genNumbers['buildFooBackground']),
            ORM::factory('Build_Widget', $this->genNumbers['buildFooLog']),
        );

        $actual = ORM::factory('Build_Widget')
                ->where('project_id', '=', $this->genNumbers['ProjectBaz'])
                ->order_by('type', 'ASC')
                ->find_all();
        for ($i = 0; $i < sizeof($actual); $i++) {
            $expected[$i]->id         = $actual[$i]->id;
            $expected[$i]->project_id = $this->genNumbers['ProjectBaz'];
            foreach (array_keys($actual[$i]->list_columns()) as $column) {
                $this->assertEquals(
                        $expected[$i]->$column, $actual[$i]->$column,
                        'Column ' . $column . ' of Build_Widget[' . $i . '] does not match'
                );
            }
        }
    }
}