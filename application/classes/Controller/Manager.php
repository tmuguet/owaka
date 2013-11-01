<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays project managers
 * 
 * @package   Main
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Manager extends Controller
{

    /**
     * Displays form for adding a new project
     * 
     * @url http://example.com/manager/add
     */
    public function action_add()
    {
        $processors = File::findProcessors();
        $reports    = array();
        foreach ($processors as $processor) {
            $name           = str_replace('Processor_', '', $processor);
            $reports[$name] = $processor::$inputReports;
        }
        $postactions = array();
        foreach (File::findPostactions() as $postaction) {
            $name          = str_replace('Postaction_', '', $postaction);
            $postactions[] = $name;
        }

        $view = View::factory('manager')
                ->set('action', 'add')
                ->set('uri', 'api/project/add')
                ->set('project', ORM::factory('Project'))
                ->set('reports', $reports)
                ->set('postactions', $postactions);
        $this->success($view);
    }

    /**
     * Displays form for editing an existing project
     * 
     * @url http://example.com/manager/edit/&lt;project_id&gt;
     */
    public function action_edit()
    {
        $processors = File::findProcessors();
        $reports    = array();
        foreach ($processors as $processor) {
            $name           = str_replace('Processor_', '', $processor);
            $reports[$name] = $processor::$inputReports;
        }
        $postactions = array();
        foreach (File::findPostactions() as $postaction) {
            $name          = str_replace('Postaction_', '', $postaction);
            $postactions[] = $name;
        }
        $project = ORM::factory('Project', $this->request->param('id'));

        $view = View::factory('manager')
                ->set('action', 'edit')
                ->set('uri', 'api/project/edit/' . $project->id)
                ->set('project', $project)
                ->set('reports', $reports)
                ->set('postactions', $postactions);
        $this->success($view);
    }

    /**
     * Displays form for duplicating an existing project
     * 
     * @url http://example.com/manager/duplicate/&lt;project_id&gt;
     */
    public function action_duplicate()
    {
        $processors = File::findProcessors();
        $reports    = array();
        foreach ($processors as $processor) {
            $name           = str_replace('Processor_', '', $processor);
            $reports[$name] = $processor::$inputReports;
        }
        $postactions = array();
        foreach (File::findPostactions() as $postaction) {
            $name          = str_replace('Postaction_', '', $postaction);
            $postactions[] = $name;
        }
        $project = ORM::factory('Project', $this->request->param('id'));
        $project->name .= '-copy';

        $view = View::factory('manager')
                ->set('action', 'duplicate')
                ->set('uri', 'api/project/duplicate/' . $project->id)
                ->set('project', $project)
                ->set('reports', $reports)
                ->set('postactions', $postactions);
        $this->success($view);
    }
}