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
     * @covers Controller_Api_Project::action_add
     */
    public function testActionAdd()
    {
        $expected                        = ORM::factory('Project');
        $expected->name                  = 'foo';
        $expected->is_active             = 1;
        $expected->is_ready              = 0;
        $expected->scm                   = 'mercurial';
        $expected->scm_url               = 'scm_url';
        $expected->scm_branch            = 'branch';
        $expected->is_remote             = 1;
        $expected->host                  = "blabla";
        $expected->port                  = 22;
        $expected->username              = "foo";
        $expected->privatekey_path       = "/privatekey";
        $expected->public_host_key       = "blabla";
        $expected->path                  = '/usr';
        $expected->phing_path            = '/usr/lib';
        $expected->phing_target_validate = 'target_validate target2';
        $expected->reports_path          = '/reports/path';

        $expected2        = ORM::factory('Project_Report');
        $expected2->type  = 'processor1_xml';
        $expected2->value = 'result.xml';

        $request  = Request::factory('api/project/add')->login();
        $request->method(Request::POST);
        $request->post('name', $expected->name);
        $request->post('is_active', $expected->is_active);
        $request->post('scm', $expected->scm);
        $request->post('scm_url', $expected->scm_url);
        $request->post('scm_branch', $expected->scm_branch);
        $request->post('is_remote', $expected->is_remote);
        $request->post('host', $expected->host);
        $request->post('port', $expected->port);
        $request->post('username', $expected->username);
        $request->post('privatekey_path', $expected->privatekey_path);
        $request->post('public_host_key', $expected->public_host_key);
        $request->post('path', $expected->path);
        $request->post('phing_path', $expected->phing_path);
        $request->post('phing_target_validate', $expected->phing_target_validate);
        $request->post('reports_path', $expected->reports_path);
        $request->post($expected2->type, $expected2->value);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $actual       = ORM::factory('Project')->where('name', '=', 'foo')->find();
        $this->assertTrue($actual->loaded());
        $expected->id = $actual->id;
        foreach ($actual->list_columns() as $column => $info) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of Project does not match'
            );
        }
        $this->assertEquals(array("project" => $actual->id), $apiCall, "Incorrect API result");

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
     * @covers Controller_Api_Project::action_add
     */
    public function testActionAddFail()
    {
        $request  = Request::factory('api/project/add')->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $this->assertEquals(
                array(
            'errors' => array(
                'name'                  => 'You must provide a name.',
                'scm'                   => 'You must provide a SCM.',
                'scm_url'               => 'You must provide a URL for checking out your project.',
                'scm_branch'            => 'You must provide a branch for checking out your project.',
                'path'                  => 'You must provide a path.',
                'phing_path'            => 'You must provide a path.',
                'phing_target_validate' => 'You must provide at least one target.',
                'reports_path'          => 'You must provide a path.',
            )
                ), json_decode($response->body(), TRUE), "Incorrect API result"
        );
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
        $expected->is_ready              = 0;
        $expected->scm                   = 'mercurial';
        $expected->scm_url               = 'scm_url';
        $expected->scm_branch            = 'branch';
        $expected->is_remote             = 1;
        $expected->host                  = "blabla";
        $expected->port                  = 22;
        $expected->username              = "foo";
        $expected->privatekey_path       = "/privatekey";
        $expected->public_host_key       = "blabla";
        $expected->path                  = '/usr';
        $expected->phing_path            = '/usr/lib';
        $expected->phing_target_validate = 'target_validate target2';
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
        $request->post('scm_url', $expected->scm_url);
        $request->post('scm_branch', $expected->scm_branch);
        $request->post('is_remote', $expected->is_remote);
        $request->post('host', $expected->host);
        $request->post('port', $expected->port);
        $request->post('username', $expected->username);
        $request->post('privatekey_path', $expected->privatekey_path);
        $request->post('public_host_key', $expected->public_host_key);
        $request->post('path', $expected->path);
        $request->post('phing_path', $expected->phing_path);
        $request->post('phing_target_validate', $expected->phing_target_validate);
        $request->post('reports_path', $expected->reports_path);
        $request->post($expected2->type, $expected2->value);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);
        $this->assertEquals(array("project" => $this->genNumbers['ProjectFoo']), $apiCall, "Incorrect API result");

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

    /**
     * @covers Controller_Api_Project::action_edit
     */
    public function testActionEditFail()
    {
        $request  = Request::factory('api/project/edit/' . $this->genNumbers['ProjectFoo'])->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $this->assertEquals(
                array(
            'errors' => array(
                'name'                  => 'You must provide a name.',
                'scm'                   => 'You must provide a SCM.',
                'scm_url'               => 'You must provide a URL for checking out your project.',
                'scm_branch'            => 'You must provide a branch for checking out your project.',
                'path'                  => 'You must provide a path.',
                'phing_path'            => 'You must provide a path.',
                'phing_target_validate' => 'You must provide at least one target.',
                'reports_path'          => 'You must provide a path.',
            )
                ), json_decode($response->body(), TRUE), "Incorrect API result"
        );
    }

    /**
     * @covers Controller_Api_Project::action_edit
     */
    public function testActionEditNotFound()
    {
        $request  = Request::factory('api/project/edit/99999')->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }
}