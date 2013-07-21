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
        $this->assertEquals(200, $response->status(), "Request failed");
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Project::action_add
     */
    public function testActionAdd()
    {
        $expected                        = ORM::factory('Project');
        $expected->name                  = 'foo';
        $expected->is_active             = 1;
        $expected->scm                   = 'mercurial';
        $expected->path                  = '/path';
        $expected->phing_path            = '/phing/path';
        $expected->phing_target_validate = 'target_validate';
        $expected->phing_target_nightly  = 'target_nightly';
        $expected->reports_path          = '/reports/path';

        $expected2        = ORM::factory('Project_Report');
        $expected2->type  = 'processor1_xml';
        $expected2->value = 'result.xml';

        $request  = Request::factory('api/project/add')->login();
        $request->method(Request::POST);
        $request->post('name', $expected->name);
        $request->post('is_active', $expected->is_active);
        $request->post('scm', $expected->scm);
        $request->post('path', $expected->path);
        $request->post('phing_path', $expected->phing_path);
        $request->post('phing_target_validate', $expected->phing_target_validate);
        $request->post('phing_target_nightly', $expected->phing_target_nightly);
        $request->post('reports_path', $expected->reports_path);
        $request->post($expected2->type, $expected2->value);
        $response = $request->execute();
        $this->assertEquals(200, $response->status(), "Request failed");
        $apiCall  = json_decode($response->body(), TRUE);

        $actual       = ORM::factory('Project')->where('name', '=', 'foo')->find();
        $this->assertTrue($actual->loaded());
        $expected->id = $actual->id;
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of Project does not match'
            );
        }
        $this->assertEquals(array("res" => "ok", "project" => $actual->id), $apiCall, "Incorrect API result");

        $actual2               = ORM::factory('Project_Report')->where('project_id', '=', $expected->id)->find_all();
        $this->assertEquals(1, sizeof($actual2));
        $expected2->project_id = $actual->id;
        $expected2->id         = $actual2[0]->id;
        foreach ($actual2[0]->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected2->$column, $actual2[0]->$column, 'Column ' . $column . ' of Project_Report does not match'
            );
        }
    }

    /**
     * @covers Controller_Api_Project::action_edit
     */
    public function testActionEdit()
    {
        $expected                        = ORM::factory('Project');
        $expected->id                    = $this->genNumbers['ProjectFoo'];
        $expected->name                  = 'foo';
        $expected->is_active             = 1;
        $expected->scm                   = 'mercurial';
        $expected->path                  = '/path';
        $expected->phing_path            = '/phing/path';
        $expected->phing_target_validate = 'target_validate';
        $expected->phing_target_nightly  = 'target_nightly';
        $expected->reports_path          = '/reports/path';
        $expected->lastrevision          = 42;

        $expected2             = ORM::factory('Project_Report');
        $expected2->project_id = $this->genNumbers['ProjectFoo'];
        $expected2->type       = 'processor1_xml';
        $expected2->value      = 'result.xml';

        $request  = Request::factory('api/project/edit/' . $this->genNumbers['ProjectFoo'])->login();
        $request->method(Request::POST);
        $request->post('name', $expected->name);
        $request->post('is_active', $expected->is_active);
        $request->post('scm', $expected->scm);
        $request->post('path', $expected->path);
        $request->post('phing_path', $expected->phing_path);
        $request->post('phing_target_validate', $expected->phing_target_validate);
        $request->post('phing_target_nightly', $expected->phing_target_nightly);
        $request->post('reports_path', $expected->reports_path);
        $request->post($expected2->type, $expected2->value);
        $response = $request->execute();
        $this->assertEquals(200, $response->status(), "Request failed");
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("res" => "ok", "project" => $this->genNumbers['ProjectFoo']), $apiCall, "Incorrect API result");

        $actual = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($actual->loaded());
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of Project does not match'
            );
        }

        $actual2       = ORM::factory('Project_Report')->where('project_id', '=', $expected->id)->find();
        $this->assertTrue($actual2->loaded());
        $expected2->id = $actual2->id;
        foreach ($actual2->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected2->$column, $actual2->$column, 'Column ' . $column . ' of Project_Report does not match'
            );
        }
    }
}