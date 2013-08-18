<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_ProjectTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Project::action_list
     */
    public function testActionList()
    {
        $expected = array(
            array('id'   => $this->genNumbers['ProjectBar'], 'name' => 'active-git'),
            array('id'   => $this->genNumbers['ProjectFoo'], 'name' => 'active-hg'),
        );

        $response = Request::factory('api/project/list/')->login()->execute();
        $this->assertResponseOK($response);
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Project::editReports
     */
    public function testEditReports_replace()
    {
        $target  = new Controller_Api_Project();
        $post    = array('processor2_raw' => 'value2');
        $target->request->post($post);
        $project = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $target->editReports($project);
        $actual  = ORM::factory('Project_Report')
                ->where('project_id', '=', $project->id)
                ->order_by('type', 'ASC')
                ->find_all();
        $this->assertEquals(1, sizeof($actual));

        $expected                = array();
        $expected[0]             = ORM::factory('Project_Report');
        $expected[0]->project_id = $project->id;
        $expected[0]->id         = $actual[0]->id;
        $expected[0]->type       = 'processor2_raw';
        $expected[0]->value      = 'value2';

        for ($i = 0; $i < sizeof($actual); $i++) {
            foreach ($actual[0]->list_columns() as $column => $info) {
                $this->assertEquals(
                        $expected[$i]->$column, $actual[$i]->$column,
                        'Column ' . $column . ' of Project_Report does not match'
                );
            }
        }
    }

    /**
     * @covers Controller_Api_Project::editReports
     */
    public function testEditReports_add()
    {
        $target  = new Controller_Api_Project();
        $post    = array('processor1_xml' => 'value1');
        $target->request->post($post);
        $project = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $target->editReports($project);
        $actual  = ORM::factory('Project_Report')
                ->where('project_id', '=', $project->id)
                ->order_by('type', 'ASC')
                ->find_all();
        $this->assertEquals(2, sizeof($actual));

        $expected                = array();
        $expected[0]             = ORM::factory('Project_Report');
        $expected[0]->project_id = $project->id;
        $expected[0]->id         = $actual[0]->id;
        $expected[0]->type       = 'processor1_xml';
        $expected[0]->value      = 'value1';

        $expected[1]             = ORM::factory('Project_Report');
        $expected[1]->project_id = $project->id;
        $expected[1]->id         = $actual[1]->id;
        $expected[1]->type       = 'processor2_raw';
        $expected[1]->value      = 'report.html';

        for ($i = 0; $i < sizeof($actual); $i++) {
            foreach ($actual[0]->list_columns() as $column => $info) {
                $this->assertEquals(
                        $expected[$i]->$column, $actual[$i]->$column,
                        'Column ' . $column . ' of Project_Report does not match'
                );
            }
        }
    }

    /**
     * @covers Controller_Api_Project::editReports
     */
    public function testEditReports_delete()
    {
        $target  = new Controller_Api_Project();
        $post    = array('processor2_raw' => '');
        $target->request->post($post);
        $project = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $target->editReports($project);
        $actual  = ORM::factory('Project_Report')
                ->where('project_id', '=', $project->id)
                ->order_by('type', 'ASC')
                ->find_all();
        $this->assertEquals(0, sizeof($actual));
    }
}