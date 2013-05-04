<?php

class Controller_Data_Coverage extends Controller_Data_Base
{

    public function action_parse()
    {
        $build  = $this->request->param('id');
        $report = Owaka::getReportsPath($build, 'coverage');

        if (file_exists($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('coverage_globaldata');
            $global->build_id = $build;

            $xml                    = simplexml_load_file($report);
            $global->methodcount    = (int) $xml['methodcount'];
            $global->methodscovered = (int) $xml['methodscovered'];
            $global->methodcoverage = ($global->methodcount > 0 ?
                            ($global->methodscovered * 100 / $global->methodcount) : 100);

            $global->statementcount    = (int) $xml['statementcount'];
            $global->statementscovered = (int) $xml['statementscovered'];
            $global->statementcoverage = ($global->statementcount > 0 ?
                            ($global->statementscovered * 100 / $global->statementcount) : 100);

            $global->totalcount    = (int) $xml['totalcount'];
            $global->totalcovered  = (int) $xml['totalcovered'];
            $global->totalcoverage = ($global->totalcount > 0 ?
                            ($global->totalcovered * 100 / $global->totalcount) : 100);

            if ($global->methodcount > 0 || $global->statementcount > 0 || $global->totalcount > 0) {
                $global->create();
            }
            $this->response->body(true);
        }

        $this->response->body(false);
    }
}
