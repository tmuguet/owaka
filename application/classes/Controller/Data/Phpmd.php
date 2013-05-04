<?php

class Controller_Data_Phpmd extends Controller_Data_Base
{

    public function action_parse()
    {
        $build  = $this->request->param('id');
        $report = Owaka::getReportsPath($build, 'phpmd');

        if (file_exists($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('phpmd_globaldata');
            $global->build_id = $build;
            
            $content = file_get_contents($report);
            $global->errors = substr_count($content, '</tr>') - 1;

            $global->create();
            $this->response->body(true);
        }

        $this->response->body(false);
    }
}
