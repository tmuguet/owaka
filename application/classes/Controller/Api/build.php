<?php

class Controller_Api_build extends Controller
{

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
                "revision" => $build->revision,
                "status"   => $build->status,
            );
        }
        $this->response->body(json_encode($output));
    }
}