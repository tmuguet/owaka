<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing projects
 * 
 * @package Api
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
                "id"   => $project->id,
                "name" => $project->name,
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
            $project->is_active             = $this->request->post('is_active');
            $project->scm                   = $this->request->post('scm');
            $project->path                  = $this->request->post('path');
            $project->phing_path            = $this->request->post('phing_path');
            $project->phing_target_validate = $this->request->post('phing_target_validate');
            $project->reports_path          = $this->request->post('reports_path');
            $project->create();

            $processors = File::findProcessors();
            $reports    = array();
            foreach ($processors as $processor) {
                $name = str_replace("Controller_Processor_", "", $processor);
                foreach ($processor::getInputReports() as $key => $reports) {
                    $report             = ORM::factory('Project_Report');
                    $report->project_id = $project->id;
                    $report->type       = strtolower($name) . '_' . $key;
                    $report->value      = $this->request->post($report->type);
                    if (!empty($report->value)) {
                        $report->create();
                    }
                }
            }

            $this->respondOk(array('project' => $project->id));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors()));
        }
    }

    /**
     * Edits an existing project
     * 
     * @url http://example.com/api/project/edit/&lt;project_id&gt;
     * @postparam name string Name of the project
     * @postparam is_active bool Indicates whether the project is active or not
     * @postparam scm string SCM used
     * @postparam path string Path to the project (used for SCM polling)
     * @postparam phing_path string Path to the phing project (used for build)
     * @postparam phing_target_validate string Targets for validating
     * @postparam reports_path string Path to the folder containing the generated reports during build
     * @postparam &lt;report&gt; string Name of the generated report (optional)
     */
    public function action_edit()
    {
        try {
            $projectId = $this->request->param('id');
            $project   = ORM::factory('Project', $projectId);
            if (!$project->loaded()) {
                throw new HTTP_Exception_404();
            }
            $project->name                  = $this->request->post('name');
            $project->is_active             = $this->request->post('is_active');
            $project->scm                   = $this->request->post('scm');
            $project->path                  = $this->request->post('path');
            $project->phing_path            = $this->request->post('phing_path');
            $project->phing_target_validate = $this->request->post('phing_target_validate');
            $project->reports_path          = $this->request->post('reports_path');
            $project->update();

            $processors = File::findProcessors();
            $reports    = array();
            $oldReports = ORM::factory('Project_Report')->where('project_id', '=', $projectId)->find_all();
            foreach ($oldReports as $oldReport) {
                $oldReport->delete();
            }

            foreach ($processors as $processor) {
                $name = str_replace("Controller_Processor_", "", $processor);
                foreach ($processor::getInputReports() as $key => $reports) {
                    $report             = ORM::factory('Project_Report');
                    $report->project_id = $project->id;
                    $report->type       = strtolower($name) . '_' . $key;
                    $report->value      = $this->request->post($report->type);
                    if (!empty($report->value)) {
                        $report->create();
                    }
                }
            }

            $this->respondOk(array('project' => $project->id));
        } catch (ORM_Validation_Exception $e) {
            $this->respondError(Response::UNPROCESSABLE, array('errors' => $e->errors()));
        }
    }
}