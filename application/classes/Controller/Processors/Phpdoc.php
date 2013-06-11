<?php

/**
 * PHPDOC
 * 
 * @package Processors
 */
class Controller_Processors_Phpdoc extends Controller_Processors_Base
{

    static public function getInputReports()
    {
        return array(
            'report' => array(
                'title'       => 'PHPdoc report',
                'description' => 'PHPdoc report directory',
                'type'        => 'dir',
                'keep-as'     => '.'
            )
        );
    }

    /**
     * Processes a PHPDoc report
     * @param int $buildId Build ID
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        /* nothing to do */

        return false;
    }
}
