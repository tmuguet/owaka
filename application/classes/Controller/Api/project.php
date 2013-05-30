<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing projects
 * @package    Api
 */
class Controller_Api_project extends Controller
{

    /**
     * Returns all active projects
     * 
     * Returns an array of objects, orderered alphabetically by their name:
     * {id: int, name: string}
     * 
     * @url http://example.com/api/project/list/&lt;project_id&gt;
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
        $this->response->body(json_encode($output));
    }
}