<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing builds
 */
class Controller_Api_build extends Controller
{

    /**
     * Returns the 100 latest builds of a project
     * 
     * Returns an array of objects ordered from latest to oldest:
     * {id: int, revision: string, status:ok/unstable/error/building/queued}
     * 
     * @url http://example.com/api/build/list/<project_id>
     */
    public function action_list()
    {
        $builds = ORM::factory('Build')
                ->where('project_id', '=', $this->request->param('id'))
                ->order_by('id', 'DESC')
                ->limit(100)
                ->find_all();

        $output = array();
        foreach ($builds as $build) {
            $output[] = array(
                "id"       => $build->id,
                "revision" => $build->getRevision(),
                "status"   => $build->status,
            );
        }
        $this->response->body(json_encode($output));
    }
}