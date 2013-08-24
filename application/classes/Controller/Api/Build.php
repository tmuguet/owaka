<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * API entry for managing builds
 * 
 * @package Api
 */
class Controller_Api_Build extends Controller_Api
{

    /**
     * Returns the 100 latest builds of a project
     * 
     * Returns an array of objects ordered from latest to oldest:
     * {id: int, revision: string, status:ok/unstable/error/building/queued}
     * 
     * @url http://example.com/api/build/list/&lt;project_id&gt;
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
        $this->respondOk($output);
    }

    /**
     * Deletes a build an all of its data
     * 
     * @url http://example.com/api/build/delete/&lt;build_id&gt;
     */
    public function action_delete()
    {
        $build = ORM::factory('Build', $this->request->param('id'));
        if ($build->loaded()) {
            $next = $build->nextBuild()->find();
            if (!$next->loaded()) {
                $next = $build->previousBuild()->find();
            }
            $id = $build->id;
            $build->delete();
            $this->respondOk(array("build" => $id, "next_build"   => $next->id));
        } else {
            throw new HTTP_Exception_404();
        }
    }
}