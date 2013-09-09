<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * PHPDOC
 * 
 * @package   Processors
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Processor_Phpdoc extends Controller_Processor
{

    /**
     * Gets the input reports
     * 
     * @return array
     */
    static public function inputReports()
    {
        return array(
            'report' => array(
                'title'       => 'HTML report',
                'description' => 'PHPdoc report directory',
                'type'        => 'dir',
                'keep-as'     => '.'
            )
        );
    }

    /**
     * Processes a PHPDoc report
     * 
     * @param int $buildId Build ID
     * 
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        /* nothing to do */

        return false;
    }
}
