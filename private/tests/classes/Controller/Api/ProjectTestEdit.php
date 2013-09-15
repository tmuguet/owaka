<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_ProjectTestEdit extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Project::action_edit
     */
    public function testActionEdit()
    {
        $expected       = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $expected->name = 'foo';

        $request  = Request::factory('api/project/edit/' . $this->genNumbers['ProjectFoo'])->login();
        $request->method(Request::POST);
        $request->post('name', $expected->name);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->rollback();
        $this->assertEquals(
                array("project"    => $this->genNumbers['ProjectFoo'], 'scm_status' => $expected->scm_status), $apiCall,
                "Incorrect API result"
        );

        $actual = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($actual->loaded());
        foreach (array_keys($actual->list_columns()) as $column) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of Project does not match'
            );
        }
    }

    /**
     * @covers Controller_Api_Project::action_edit
     */
    public function testActionEditScmStatus1()
    {
        $expected             = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $expected->scm        = 'git';
        $expected->scm_status = 'void';

        $request  = Request::factory('api/project/edit/' . $this->genNumbers['ProjectFoo'])->login();
        $request->method(Request::POST);
        $request->post('scm', $expected->scm);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->rollback();
        $this->assertEquals(
                array("project"    => $this->genNumbers['ProjectFoo'], 'scm_status' => $expected->scm_status), $apiCall,
                "Incorrect API result"
        );

        $actual = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($actual->loaded());
        foreach (array_keys($actual->list_columns()) as $column) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of Project does not match'
            );
        }
    }

    /**
     * @covers Controller_Api_Project::action_edit
     */
    public function testActionEditScmStatus2()
    {
        $expected             = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $expected->scm_url    = 'foobar';
        $expected->scm_status = 'void';

        $request  = Request::factory('api/project/edit/' . $this->genNumbers['ProjectFoo'])->login();
        $request->method(Request::POST);
        $request->post('scm_url', $expected->scm_url);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->rollback();
        $this->assertEquals(
                array("project"    => $this->genNumbers['ProjectFoo'], 'scm_status' => $expected->scm_status), $apiCall,
                "Incorrect API result"
        );

        $actual = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($actual->loaded());
        foreach (array_keys($actual->list_columns()) as $column) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of Project does not match'
            );
        }
    }

    /**
     * @covers Controller_Api_Project::action_edit
     */
    public function testActionEditScmStatus3()
    {
        $expected             = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $expected->scm_branch = 'newbranch';
        $expected->scm_status = 'checkedout';

        $request  = Request::factory('api/project/edit/' . $this->genNumbers['ProjectFoo'])->login();
        $request->method(Request::POST);
        $request->post('scm_branch', $expected->scm_branch);
        $response = $request->execute();
        $this->assertResponseOK($response);
        $apiCall  = json_decode($response->body(), TRUE);

        $this->rollback();
        $this->assertEquals(
                array("project"    => $this->genNumbers['ProjectFoo'], 'scm_status' => $expected->scm_status), $apiCall,
                "Incorrect API result"
        );

        $actual = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $this->assertTrue($actual->loaded());
        foreach (array_keys($actual->list_columns()) as $column) {
            $this->assertEquals(
                    $expected->$column, $actual->$column, 'Column ' . $column . ' of Project does not match'
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
        $request->post('name', '');
        $response = $request->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $this->assertEquals(
                array(
            'errors' => array(
                'name' => 'You must provide a name.',
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