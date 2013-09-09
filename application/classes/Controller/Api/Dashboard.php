<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing dashboards
 * 
 * @api
 * @package   Api
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Api_Dashboard extends Controller_Api
{

    /**
     * Duplicates a dashboard
     * 
     * @url http://example.com/api/dashboard/duplicate/project/&lt;project_id&gt;/&lt;new_project_id&gt;
     * @url http://example.com/api/dashboard/duplicate/build/&lt;project_id&gt;/&lt;new_project_id&gt;
     */
    public function action_duplicate()
    {
        switch ($this->request->param('dashboard')) {
            case "project":
                $model = ORM::factory('Project_Widget');
                break;
            case "build":
                $model = ORM::factory('Build_Widget');
                break;
            // @codeCoverageIgnoreStart
            default:
                throw new HTTP_Exception_404();
            // @codeCoverageIgnoreEnd
        }
        $widgets = $model->where('project_id', '=', $this->request->param('id'))->find_all();
        foreach ($widgets as $widget) {
            $widget->duplicate(array('project_id' => $this->request->param('data')));
        }
        $this->respondOk();
    }
}
