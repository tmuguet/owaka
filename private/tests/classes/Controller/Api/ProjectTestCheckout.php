<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_ProjectTestCheckout extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Project::action_checkout
     */
    public function testActionCheckoutNotFound()
    {
        $request  = Request::factory('api/project/checkout/99999')->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     */
    public function testActionCheckoutError()
    {
        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'checkedout';
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

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::FAILURE, $response);

        $project->reload();
        $this->assertEquals('checkedout', $project->scm_status);
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     * @covers Task_Forcequeue::_execute
     * @covers File::rrmdir
     */
    public function testActionCheckoutReadyHg()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }
        exec('hg clone ' . __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'mercurial' . ' ' . $path);

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

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->assertEquals(
                array("project"    => $project->id, 'scm_status' => 'ready'), $apiCall, "Incorrect API result"
        );

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(1, sizeof($builds));
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     * @covers Task_Forcequeue::_execute
     * @covers File::rrmdir
     */
    public function testActionCheckoutReadyGit()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }
        exec('git clone ' . __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'git' . ' ' . $path);

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

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->assertEquals(
                array("project"    => $project->id, 'scm_status' => 'ready'), $apiCall, "Incorrect API result"
        );

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(1, sizeof($builds));
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     * @covers Task_Switch::_execute
     * @covers File::rrmdir
     */
    public function testActionCheckoutCheckedoutHg()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }
        exec('hg clone ' . __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'mercurial' . ' ' . $path);

        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'checkedout';
        $project->is_active             = 0;
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

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->assertEquals(
                array("project"    => $project->id, 'scm_status' => 'ready'), $apiCall, "Incorrect API result"
        );
        $project->reload();
        $this->assertEquals('ready', $project->scm_status);

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(0, sizeof($builds));
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     * @covers Task_Switch::_execute
     * @covers File::rrmdir
     */
    public function testActionCheckoutCheckedoutGit()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }
        exec('git clone ' . __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'git' . ' ' . $path);

        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'checkedout';
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

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->assertEquals(
                array("project"    => $project->id, 'scm_status' => 'ready'), $apiCall, "Incorrect API result"
        );
        $project->reload();
        $this->assertEquals('ready', $project->scm_status);

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(1, sizeof($builds));
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     * @covers Task_Switch::_execute
     * @covers File::rrmdir
     */
    public function testActionCheckoutCheckedoutError()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }
        exec('git clone ' . __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'git' . ' ' . $path);

        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'checkedout';
        $project->is_active             = 1;
        $project->scm                   = 'git';
        $project->scm_url               = 'not_used';
        $project->scm_branch            = 'non-existing';
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

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::FAILURE, $response);

        $project->reload();
        $this->assertEquals('checkedout', $project->scm_status);
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     * @covers Task_Checkout::_execute
     * @covers File::rrmdir
     */
    public function testActionCheckoutVoidHg()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }

        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'void';
        $project->is_active             = 1;
        $project->scm                   = 'mercurial';
        $project->scm_url               = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'mercurial';
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

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->assertEquals(
                array("project"    => $project->id, 'scm_status' => 'ready'), $apiCall, "Incorrect API result"
        );
        $project->reload();
        $this->assertEquals('ready', $project->scm_status);

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(1, sizeof($builds));
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     * @covers Task_Checkout::_execute
     * @covers File::rrmdir
     */
    public function testActionCheckoutVoidGit()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'test';
        if (is_dir($path)) {
            File::rrmdir($path);
        }

        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'void';
        $project->is_active             = 0;
        $project->scm                   = 'git';
        $project->scm_url               = __DIR__ . DIRECTORY_SEPARATOR . 'Project' . DIRECTORY_SEPARATOR . 'git';
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

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->assertEquals(
                array("project"    => $project->id, 'scm_status' => 'ready'), $apiCall, "Incorrect API result"
        );
        $project->reload();
        $this->assertEquals('ready', $project->scm_status);

        $builds = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->where('project_id', '=', $project->id)
                ->find_all();
        $this->assertEquals(0, sizeof($builds));
    }

    /**
     * @covers Controller_Api_Project::action_checkout
     * @covers Task_Checkout::_execute
     */
    public function testActionCheckoutVoidError()
    {
        $project                        = ORM::factory('Project');
        $project->name                  = 'utest';
        $project->scm_status            = 'void';
        $project->is_active             = 0;
        $project->scm                   = 'git';
        $project->scm_url               = 'non-existing';
        $project->scm_branch            = 'origin/master';
        $project->is_remote             = 0;
        $project->host                  = '';
        $project->port                  = '';
        $project->username              = '';
        $project->privatekey_path       = '';
        $project->public_host_key       = '';
        $project->path                  = '/';
        $project->phing_path            = '/';
        $project->phing_target_validate = 'doc';
        $project->reports_path          = '/';
        $project->create();

        $request  = Request::factory('api/project/checkout/' . $project->id)->login();
        $request->method(Request::POST);
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::FAILURE, $response);

        $project->reload();
        $this->assertEquals('void', $project->scm_status);
    }
}