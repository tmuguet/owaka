<?php

class Controller_Data_Pdepend extends Controller_Data_Base
{

    public function action_parse()
    {
        $build  = $this->request->param('id');
        $report = Helper_Owaka::getReportsPath($build, 'pdepend');

        if (file_exists($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('pdepend_globaldata');
            $global->build_id = $build;

            $xml            = simplexml_load_file($report);
            $global->ahh    = (double) $xml['ahh'];
            $global->andc   = (double) $xml['andc'];
            $global->calls  = (int) $xml['calls'];
            $global->ccn    = (int) $xml['ccn'];
            $global->ccn2   = (int) $xml['ccn2'];
            $global->cloc   = (int) $xml['cloc'];
            $global->clsa   = (int) $xml['clsa'];
            $global->clsc   = (int) $xml['clsc'];
            $global->eloc   = (int) $xml['eloc'];
            $global->fanout = (int) $xml['fanout'];
            $global->leafs  = (int) $xml['leafs'];
            $global->lloc   = (int) $xml['lloc'];
            $global->loc    = (int) $xml['loc'];
            $global->maxdit = (int) $xml['maxDIT'];
            $global->ncloc  = (int) $xml['ncloc'];
            $global->noc    = (int) $xml['noc'];
            $global->nof    = (int) $xml['nof'];
            $global->noi    = (int) $xml['noi'];
            $global->nom    = (int) $xml['nom'];
            $global->nop    = (int) $xml['nop'];
            $global->roots  = (int) $xml['roots'];

            $global->create();
            
            $this->response->body(true);
        }

        $this->response->body(false);
    }
}
