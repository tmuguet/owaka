<?php

/**
 * PHPMD
 */
class Controller_Processors_Phpmd extends Controller_Processors_Base
{

    /**
     * Processes a PHPMD HTML report
     * @param int $buildId Build ID
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        $report = Owaka::getReportsPath($buildId, 'phpmd');

        if (file_exists($report) && file_get_contents($report) != "") {
            $content          = file_get_contents($report);
            $global           = ORM::factory('phpmd_globaldata');
            $global->build_id = $buildId;
            $global->errors   = substr_count($content, '</tr>') - 1;

            $global->create();
            return true;
        }

        return false;
    }
}
