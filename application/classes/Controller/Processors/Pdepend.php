<?php

/**
 * Pdepend
 */
class Controller_Processors_Pdepend extends Controller_Processors_Base
{

    /**
     * Processes a PHPdepend XML report
     * @param int $buildId Build ID
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        $report = Owaka::getReportsPath($buildId, 'pdepend');

        if (file_exists($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('pdepend_globaldata');
            $global->build_id = $buildId;

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

            return true;
        }

        return false;
    }
}
