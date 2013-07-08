<?php

/**
 * Displays project managers
 * 
 * @package Main
 */
class Controller_Manager extends Controller
{

    /**
     * Displays form for adding a new project
     * @url http://example.com/manager/add
     */
    public function action_add()
    {
        $processors = File::findProcessors();
        $reports = array();
        foreach ($processors as $processor) {
            $name = str_replace("Controller_Processors_", "", $processor);
            $reports[$name] = $processor::getInputReports();
        }
        
        $view = View::factory('manager')
                ->set('project', ORM::factory('Project'))
                ->set('reports', $reports);
        $this->response->body($view);
    }
    
    /**
     * Displays form for editing an existing project
     * @url http://example.com/manager/edit/&lt;project_id&gt;
     */
    public function action_edit()
    {
        $processors = File::findProcessors();
        $reports = array();
        foreach ($processors as $processor) {
            $name = str_replace("Controller_Processors_", "", $processor);
            $reports[$name] = $processor::getInputReports();
        }
        $project = ORM::factory('Project', $this->request->param('id'));

        $view = View::factory('manager')
                ->set('project', $project)
                ->set('reports', $reports);
        $this->response->body($view);
    }
}