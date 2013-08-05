<?php

/**
 * Coverage
 * 
 * @package Processors
 */
class Controller_Processor_Coverage extends Controller_Processor
{

    static public function getInputReports()
    {
        return array(
            'raw' => array(
                'title'       => 'Coverage raw report',
                'description' => 'Coverage XML report',
                'type'        => 'file',
                'keep-as'     => 'coverage.xml'
            ),
            'dir' => array(
                'title'       => 'Coverage report directory',
                'description' => 'Coverage HTML report directory',
                'type'        => 'dir',
                'keep-as'     => '.'
            )
        );
    }

    /**
     * Processes a coverage XML report
     * 
     * @param int $buildId Build ID
     * 
     * @return bool true if report successfully treated; false if no report available or if empty report
     */
    public function process($buildId)
    {
        $report = $this->getReportCompletePath($buildId, 'raw');

        if (!empty($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('Coverage_Globaldata');
            $global->build_id = $buildId;

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
            return true;
        }

        return false;
    }
}
