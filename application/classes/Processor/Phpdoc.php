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
class Processor_Phpdoc extends Processor
{

    public static $inputReports = array(
        'report' => array(
            'title'       => 'HTML report',
            'description' => 'PHPdoc report directory',
            'type'        => 'dir',
            'keep-as'     => '.'
        )
    );

    /**
     * Processes a PHPDoc report
     * 
     * @param Model_Build &$build Build
     * 
     * @return bool true if report successfully treated; false if no report available
     */
    public function process(Model_Build &$build)
    {
        /* nothing to do */

        return false;
    }
}
