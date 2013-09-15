<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_ProjectTestTrigger extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Project::action_trigger
     */
    public function testActionTriggerNotfound()
    {
        $request  = Request::factory('api/project/trigger/999999')->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }

    /**
     * @covers Controller_Api_Project::action_trigger
     */
    public function testActionTriggerError()
    {
        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'ready';
        $project->is_active             = 1;
        $project->scm                   = 'git';
        $project->scm_url               = 'not_used';
        $project->scm_branch            = 'non-existing';
        $project->is_remote             = 1;
        $project->host                  = '127.0.0.1';
        $project->port                  = '22';
        $project->username              = 'tmuguet';
        $project->privatekey_path       = '/non-existing';
        $project->public_host_key       = '';
        $project->path                  = '/';
        $project->phing_path            = '/';
        $project->phing_target_validate = 'doc';
        $project->reports_path          = '/';
        $project->create();
        $this->commit();

        $request  = Request::factory('api/project/trigger/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::FAILURE, $response);

        $this->rollback();

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(0, sizeof($builds));
    }

    /**
     * @covers Controller_Api_Project::action_trigger
     * @covers Task_Queue::_execute
     * @covers Task_Queue::run
     * @covers Task_Queue::queue
     * @covers File::rrmdir
     */
    public function testActionTriggerReadyHg()
    {
        $path = __DIR__ . DIR_SEP . 'Project' . DIR_SEP . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }
        exec('hg clone ' . __DIR__ . DIR_SEP . 'Project' . DIR_SEP . 'mercurial' . ' ' . $path);

        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'ready';
        $project->is_active             = 1;
        $project->scm                   = 'mercurial';
        $project->scm_url               = 'not_used';
        $project->scm_branch            = 'default';
        $project->is_remote             = 0;
        $project->host                  = '';
        $project->port                  = '';
        $project->username              = '';
        $project->privatekey_path       = '';
        $project->public_host_key       = '';
        $project->path                  = $path;
        $project->phing_path            = '/';
        $project->phing_target_validate = 'doc';
        $project->reports_path          = '/';
        $project->create();
        $this->commit();

        $request  = Request::factory('api/project/trigger/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->rollback();

        $this->assertEquals(
                array("project" => $project->id), $apiCall, "Incorrect API result"
        );

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(1, sizeof($builds));
    }

    /**
     * @covers Controller_Api_Project::action_trigger
     * @covers Task_Queue::_execute
     * @covers Task_Queue::run
     * @covers Task_Queue::queue
     * @covers File::rrmdir
     */
    public function testActionTriggerReadyGit()
    {
        $path = __DIR__ . DIR_SEP . 'Project' . DIR_SEP . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }
        exec('git clone ' . __DIR__ . DIR_SEP . 'Project' . DIR_SEP . 'git' . ' ' . $path);

        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'ready';
        $project->is_active             = 1;
        $project->scm                   = 'git';
        $project->scm_url               = 'not_used';
        $project->scm_branch            = 'origin/master';
        $project->is_remote             = 0;
        $project->host                  = '';
        $project->port                  = '';
        $project->username              = '';
        $project->privatekey_path       = '';
        $project->public_host_key       = '';
        $project->path                  = $path;
        $project->phing_path            = '/';
        $project->phing_target_validate = 'doc';
        $project->reports_path          = '/';
        $project->create();
        $this->commit();

        $request  = Request::factory('api/project/trigger/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->rollback();

        $this->assertEquals(
                array("project" => $project->id), $apiCall, "Incorrect API result"
        );

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(1, sizeof($builds));
    }
}