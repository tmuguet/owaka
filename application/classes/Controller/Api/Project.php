<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing projects
 * 
 * @api
 * @package   Api
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 * 
 * @todo Separate parameters
 */
class Controller_Api_Project extends Controller_Api
{

    /**
     * Returns all active projects
     * 
     * Returns an array of objects, orderered alphabetically by their name:
     * {id: int, name: string}
     * 
     * @url http://example.com/api/project/list
     */
    public function action_list()
    {
        $projects = ORM::factory('Project')
                ->where('is_active', '=', 1)
                ->order_by('name', 'ASC')
                ->find_all();

        $output = array();
        foreach ($projects as $project) {
            $output[] = array(
                'id'   => $project->id,
                'name' => $project->name,
            );
        }
        $this->respondOk($output);
    }

    /**
     * Adds a new project
     * 
     * @url http://example.com/api/project/add
     * @postparam name string Name of the project
     * @postparam is_active bool Indicates whether the project is active or not
     * @postparam scm string SCM used
     * @postparam scm_url string URL of distant repository for checkout
     * @postparam scm_branch string Branch for checkout
     * @postparam is_remote string SCM Indicates whether the project is build remotely or locally
     * @postparam host string Host if built remotely
     * @postparam port string SSH port if built remotely
     * @postparam username string Username if built remotely
     * @postparam privatekey_path string Path to the RSA private key if built remotely
     * @postparam public_host_key string RSA public key of the remote host if built remotely
     * @postparam path string Path to the project (used for SCM polling)
     * @postparam phing_path string Path to the phing project (used for build)
     * @postparam phing_target_validate string Targets for validating
     * @postparam reports_path string Path to the folder containing the generated reports during build
     * @postparam &lt;report&gt; string Name of the generated report (optional)
     */
    public function action_add()
    {
        try {
            $project                        = ORM::factory('Project');
            $project->name                  = $this->request->post('name');
            $project->scm_status            = 'void';
            $project->is_active             = $this->request->post('is_active');
            $project->scm                   = $this->request->post('scm');
            $project->scm_url               = $this->request->post('scm_url');
            $project->scm_branch            = $this->request->post('scm_branch');
            $project->is_remote             = $this->request->post('is_remote');
            $project->host                  = $this->request->post('host');
            $project->port                  = $this->request->post('port');
            $project->username              = $this->request->post('username');
            $project->privatekey_path       = $this->request->post('privatekey_path');
            $project->public_host_key       = trim($this->request->post('public_host_key'));
            $project->path                  = $this->request->post('path');
            $project->phing_path            = $this->request->post('phing_path');
            $project->phing_target_validate = $this->request->post('phing_target_validate');
            $project->reports_path          = $this->request->post('reports_path');
            $project->create();

            $this->editReports($project);
            $this->editParameters($project);
            $this->editPostactions($project);
            $this->editPostactionParameters($project);

            $this->respondOk(array('project'    => $project->id, 'scm_status' => $project->scm_status));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors('models')));
        }
    }

    /**
     * Duplicates a project and its dashboards
     * 
     * @url http://example.com/api/project/duplicate/&lt;project_id&gt;
     * @postparam name string Name of the project
     * @postparam is_active bool Indicates whether the project is active or not
     * @postparam scm string SCM used
     * @postparam scm_url string URL of distant repository for checkout
     * @postparam scm_branch string Branch for checkout
     * @postparam is_remote string SCM Indicates whether the project is build remotely or locally
     * @postparam host string Host if built remotely
     * @postparam port string SSH port if built remotely
     * @postparam username string Username if built remotely
     * @postparam privatekey_path string Path to the RSA private key if built remotely
     * @postparam public_host_key string RSA public key of the remote host if built remotely
     * @postparam path string Path to the project (used for SCM polling)
     * @postparam phing_path string Path to the phing project (used for build)
     * @postparam phing_target_validate string Targets for validating
     * @postparam reports_path string Path to the folder containing the generated reports during build
     * @postparam &lt;report&gt; string Name of the generated report (optional)
     */
    public function action_duplicate()
    {
        try {
            $project                        = ORM::factory('Project');
            $project->name                  = $this->request->post('name');
            $project->scm_status            = 'void';
            $project->is_active             = $this->request->post('is_active');
            $project->scm                   = $this->request->post('scm');
            $project->scm_url               = $this->request->post('scm_url');
            $project->scm_branch            = $this->request->post('scm_branch');
            $project->is_remote             = $this->request->post('is_remote');
            $project->host                  = $this->request->post('host');
            $project->port                  = $this->request->post('port');
            $project->username              = $this->request->post('username');
            $project->privatekey_path       = $this->request->post('privatekey_path');
            $project->public_host_key       = trim($this->request->post('public_host_key'));
            $project->path                  = $this->request->post('path');
            $project->phing_path            = $this->request->post('phing_path');
            $project->phing_target_validate = $this->request->post('phing_target_validate');
            $project->reports_path          = $this->request->post('reports_path');
            $project->create();

            $this->editReports($project);
            $this->editParameters($project);
            $this->editPostactions($project);
            $this->editPostactionParameters($project);

            Request::factory('api/dashboard/duplicate/project/' . $this->request->param('id') . '/' . $project->id)->execute();
            Request::factory('api/dashboard/duplicate/build/' . $this->request->param('id') . '/' . $project->id)->execute();

            $this->respondOk(array('project'    => $project->id, 'scm_status' => $project->scm_status));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors('models')));
        }
    }

    /**
     * Checks out a new project
     * 
     * @url http://example.com/api/project/checkout/&lt;project_id&gt;
     */
    public function action_checkout()
    {
        $projectId = $this->request->param('id');
        $project   = ORM::factory('Project', $projectId);
        if (!$project->loaded()) {
            throw new HTTP_Exception_404();
        }

        if ($project->scm_status == 'void') {
            ob_start();
            $checkout = Minion_Task::factory(array('task'    => 'Checkout', 'project' => $project));
            $checkout->execute();
            $res      = trim(ob_get_clean());
            if ($res != 'ok') {
                $this->respondError(Response::FAILURE, array('error'   => 'Error during checkout', 'details' => $res));
                return;
            }
        }

        if ($project->scm_status == 'checkedout') {
            ob_start();
            $switch = Minion_Task::factory(array('task'    => 'Switch', 'project' => $project));
            $switch->execute();
            $res    = trim(ob_get_clean());
            if ($res != 'ok') {
                $this->respondError(Response::FAILURE, array('error'   => 'Error during switch', 'details' => $res));
                return;
            }
        }

        if ($project->scm_status == 'ready' && $project->is_active) {
            ob_start();
            $queue = Minion_Task::factory(array('task'    => 'Forcequeue', 'project' => $project));
            $queue->execute();
            $res   = trim(ob_get_clean());
            // @codeCoverageIgnoreStart
            if ($res != 'ok') {
                $this->respondError(Response::FAILURE, array('error'   => 'Error during queuing', 'details' => $res));
                return;
            }
            // @codeCoverageIgnoreEnd
        }

        $this->respondOk(array('project'    => $project->id, 'scm_status' => $project->scm_status));
    }

    /**
     * Edits an existing project
     * 
     * @url http://example.com/api/project/edit/&lt;project_id&gt;
     * @postparamopt name string Name of the project
     * @postparamopt is_active bool Indicates whether the project is active or not
     * @postparamopt scm string SCM used
     * @postparamopt is_remote string SCM Indicates whether the project is build remotely or locally
     * @postparamopt host string Host if built remotely
     * @postparamopt port string SSH port if built remotely
     * @postparamopt username string Username if built remotely
     * @postparamopt privatekey_path string Path to the RSA private key if built remotely
     * @postparamopt public_host_key string RSA public key of the remote host if built remotely
     * @postparamopt path string Path to the project (used for SCM polling)
     * @postparamopt phing_path string Path to the phing project (used for build)
     * @postparamopt phing_target_validate string Targets for validating
     * @postparamopt reports_path string Path to the folder containing the generated reports during build
     * @postparamopt &lt;report&gt; string Name of the generated report (optional)
     */
    public function action_edit()
    {
        try {
            $projectId = $this->request->param('id');
            $project   = ORM::factory('Project', $projectId);
            if (!$project->loaded()) {
                throw new HTTP_Exception_404();
            }

            $post    = $this->request->post();
            $columns = array(
                'name', 'is_active', 'scm', 'scm_url', 'scm_branch', 'is_remote', 'host', 'port', 'username',
                'privatekey_path', 'public_host_key', 'path', 'phing_path', 'phing_target_validate', 'reports_path'
            );
            foreach ($columns as $_column) {
                if (array_key_exists($_column, $post)) {
                    $project->$_column = trim($post[$_column]);
                }
            }
            if ($project->changed('scm') || $project->changed('scm_url')) {
                $project->scm_status = 'void';
            } else if ($project->changed('scm_branch')) {
                $project->scm_status = 'checkedout';
            }
            $project->update();

            $this->editReports($project);
            $this->editParameters($project);
            $this->editPostactions($project);
            $this->editPostactionParameters($project);

            $this->respondOk(array('project'    => $project->id, 'scm_status' => $project->scm_status));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors('models')));
        }
    }

    /**
     * Edits reports for a project
     * 
     * @param Model_Project &$project Project
     * 
     * @return boolean
     */
    /* protected */ function editReports(Model_Project &$project)
    {
        $post       = $this->request->post();
        $processors = File::findProcessors();
        foreach ($processors as $processor) {
            $name      = str_replace('Processor_', '', $processor);
            $namelower = strtolower($name);
            foreach (array_keys($processor::$inputReports) as $key) {
                $type = $namelower . '_' . $key;
                if (array_key_exists($type, $post)) {
                    $report = ORM::factory(
                                    'Project_Report', array('project_id' => $project->id, 'type'       => $type)
                    );
                    if (empty($post[$type])) {
                        if ($report->loaded()) {
                            $report->delete();
                        }
                    } else {
                        $report->project_id = $project->id;
                        $report->type       = $type;
                        $report->value      = $post[$type];
                        $report->save();
                    }
                }
            }
        }
        return true;
    }

    /**
     * Edits processor parameters for a project
     * 
     * @param Model_Project &$project Project
     * 
     * @return boolean
     */
    /* protected */ function editParameters(Model_Project &$project)
    {
        $post       = $this->request->post();
        $processors = File::findProcessors();
        foreach ($processors as $processor) {
            $name      = str_replace('Processor_', '', $processor);
            $namelower = strtolower($name);
            foreach (array_keys($processor::$parameters) as $key) {
                $type = $namelower . '_' . $key;
                if (array_key_exists($type, $post)) {
                    $parameter = ORM::factory(
                                    'Project_Report_Parameter',
                                    array(
                                'project_id' => $project->id,
                                'processor'  => $namelower,
                                'type'       => $key
                                    )
                    );
                    if ($post[$type] == '') {
                        if ($parameter->loaded()) {
                            $parameter->delete();
                        }
                    } else {
                        $parameter->project_id = $project->id;
                        $parameter->processor  = $namelower;
                        $parameter->type       = $key;
                        $parameter->value      = $post[$type];
                        $parameter->save();
                    }
                }
            }
        }
        return true;
    }

    /**
     * Edits post actions for a project
     * 
     * @param Model_Project &$project Project
     * 
     * @return boolean
     */
    /* protected */ function editPostactions(Model_Project &$project)
    {
        $post        = $this->request->post();
        $postactions = File::findPostactions();
        foreach ($postactions as $postaction) {
            $name      = str_replace('Postaction_', '', $postaction);
            $namelower = strtolower($name);
            $report    = ORM::factory(
                            'Project_Postaction', array('project_id' => $project->id, 'postaction' => $namelower)
            );
            if (array_key_exists($namelower, $post) && $post[$namelower]) {
                $report->project_id = $project->id;
                $report->postaction = $namelower;
                $report->save();
            } else {
                if ($report->loaded()) {
                    $report->delete();
                }
            }
        }
        return true;
    }

    /**
     * Edits postaction parameters for a project
     * 
     * @param Model_Project &$project Project
     * 
     * @return boolean
     */
    /* protected */ function editPostactionParameters(Model_Project &$project)
    {
        $post        = $this->request->post();
        $postactions = File::findPostactions();
        foreach ($postactions as $postaction) {
            $name      = str_replace('Postaction_', '', $postaction);
            $namelower = strtolower($name);
            foreach (array_keys($postaction::$parameters) as $key) {
                $type = $namelower . '_' . $key;
                if (array_key_exists($type, $post)) {
                    $parameter = ORM::factory(
                                    'Project_Postaction_Parameter',
                                    array(
                                'project_id' => $project->id,
                                'postaction' => $namelower,
                                'type'       => $key
                                    )
                    );
                    if ($post[$type] == '') {
                        if ($parameter->loaded()) {
                            $parameter->delete();
                        }
                    } else {
                        $parameter->project_id = $project->id;
                        $parameter->postaction = $namelower;
                        $parameter->type       = $key;
                        $parameter->value      = $post[$type];
                        $parameter->save();
                    }
                }
            }
        }
        return true;
    }

    protected $requiredRole_trigger = Owaka::AUTH_ROLE_NONE;

    /**
     * Triggers the build of a project
     * 
     * @url http://example.com/api/project/trigger/&lt;project_id&gt;
     */
    public function action_trigger()
    {
        $projectId = $this->request->param('id');
        $project   = ORM::factory('Project', $projectId);
        if (!$project->loaded()) {
            throw new HTTP_Exception_404();
        }

        ob_start();
        $queue = Minion_Task::factory(array('task'    => 'Queue', 'project' => $project));
        $queue->execute();
        $res   = trim(ob_get_clean());
        // @codeCoverageIgnoreStart
        if ($res != 'ok') {
            $this->respondError(Response::FAILURE, array('error'   => 'Error during queuing', 'details' => $res));
            return;
        }
        // @codeCoverageIgnoreEnd

        $this->respondOk(array('project' => $project->id));
    }
}
