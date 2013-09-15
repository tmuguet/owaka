<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Pdepend
 * 
 * @package   Processors
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Processor_Pdepend extends Controller_Processor
{

    public static $inputReports = array(
        'summary'       => array(
            'title'       => 'XML report',
            'description' => 'PhpDepend XML report (logger summary-xml). This is the report used for processing data.',
            'type'        => 'file',
            'keep-as'     => 'summary.xml',
        ),
        'jdepend_chart' => array(
            'title'       => 'jdepend chart',
            'description' => 'PhpDepend jdepend chart (logger jdepend-chart)',
            'type'        => 'file',
            'keep-as'     => 'jdepend.svg'
        ),
        'jdepend_xml'   => array(
            'title'       => 'jdepend XML',
            'description' => 'PhpDepend jdepend XML (logger jdepend-xml)',
            'type'        => 'file',
            'keep-as'     => 'jdepend.xml'
        ),
        'pyramid'       => array(
            'title'       => 'pyramid',
            'description' => 'PhpDepend pyramid (logger overview-pyramid)',
            'type'        => 'file',
            'keep-as'     => 'pyramid.svg'
        ),
        'phpunit_xml'   => array(
            'title'       => 'phpunit XML',
            'description' => 'PhpDepend phpunit XML (logger phpunit-xml)',
            'type'        => 'file',
            'keep-as'     => 'phpunit.xml'
        )
    );

    /**
     * Processes a PHPdepend XML report
     * 
     * @param int $buildId Build ID
     * 
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        $report = $this->getReportCompletePath($buildId, 'summary');

        if (!empty($report) && file_get_contents($report) != '') {
            $global           = ORM::factory('Pdepend_Globaldata');
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
