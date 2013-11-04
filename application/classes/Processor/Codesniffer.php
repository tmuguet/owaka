<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Codesniffer
 * 
 * @package   Processors
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Processor_Codesniffer extends Processor
{

    public static $inputReports = array(
        'xml' => array(
            'title'       => 'XML report',
            'description' => 'Code Sniffer XML report in checkstyle format',
            'type'        => 'file',
            'keep-as'     => 'index.xml'
        )
    );
    static public $parameters = array(
        'threshold_errors_error'      => array(
            'title'        => 'Errors to trigger error',
            'description'  => 'Number of errors to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_errors_unstable'   => array(
            'title'        => 'Errors to trigger unstable',
            'description'  => 'Number of errors to trigger unstable build',
            'defaultvalue' => 1
        ),
        'threshold_warnings_error'    => array(
            'title'        => 'Warnings to trigger error',
            'description'  => 'Number of warnings to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_warnings_unstable' => array(
            'title'        => 'Warnings to trigger unstable',
            'description'  => 'Number of warnings to trigger unstable build',
            'defaultvalue' => 1
        ),
        'threshold_errors_regressions_error'      => array(
            'title'        => 'Regression errors to trigger error',
            'description'  => 'Number of regression errors to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_errors_regressions_unstable'   => array(
            'title'        => 'Regression errors to trigger unstable',
            'description'  => 'Number of regression errors to trigger unstable build',
            'defaultvalue' => 1
        ),
        'threshold_warnings_regressions_error'    => array(
            'title'        => 'Regression warnings to trigger error',
            'description'  => 'Number of regression warnings to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_warnings_regressions_unstable' => array(
            'title'        => 'Regression warnings to trigger unstable',
            'description'  => 'Number of regression warnings to trigger unstable build',
            'defaultvalue' => 1
        ),
    );

    /**
     * Processes a Codesniffer XML report
     * 
     * @param Model_Build &$build Build
     * 
     * @return bool true if report successfully treated; false if no report available
     */
    public function process(Model_Build &$build)
    {
        $report = $this->getReportCompleteRealPath($build, 'xml');

        if (!empty($report) && file_get_contents($report) != '') {
            $global           = ORM::factory('Codesniffer_Globaldata');
            $global->build_id = $build->id;
            $global->warnings = 0;
            $global->errors   = 0;

            $xml = simplexml_load_file($report);
            foreach ($xml->children() as $file) {
                foreach ($file->children() as $item) {
                    $error           = ORM::factory('Codesniffer_Error');
                    $error->build_id = $build->id;
                    $error->file     = (string) $file['name'];
                    $error->message  = (string) $item['message'];
                    $error->line     = (int) $item['line'];

                    if ($item['severity'] == 'warning') {
                        $global->warnings++;
                        $error->severity = 'warning';
                    } else {
                        $global->errors++;
                        $error->severity = 'error';
                    }

                    $error->create();
                }
            }
            $this->findRegressions($build, $global);
            $global->create();
            return true;
        }

        return false;
    }

    /**
     * Finds regressions and fixes with previous build
     * 
     * @param Model_Build                  &$build Build
     * @param Model_Codesniffer_Globaldata &$data  Current data
     */
    protected function findRegressions(Model_Build &$build, Model_Codesniffer_Globaldata &$data)
    {
        $prevBuild = $build->previousBuild()->find();
        $prevData  = $prevBuild->codesniffer_globaldata;
        if ($prevData->loaded()) {
            $data->warnings_regressions = 0;
            $data->errors_regressions   = 0;
            $data->warnings_fixed       = 0;
            $data->errors_fixed         = 0;

            foreach ($build->codesniffer_errors->find_all() as $current) {
                if (!$current->hasSimilar($prevBuild)) {
                    if ($current->severity == 'warning') {
                        $data->warnings_regressions++;
                    } else {
                        $data->errors_regressions++;
                    }
                    $current->regression = 1;
                    $current->update();
                }
            }

            foreach ($prevBuild->codesniffer_errors->find_all() as $old) {
                if (!$old->hasSimilar($build)) {
                    if ($old->severity == 'warning') {
                        $data->warnings_fixed++;
                    } else {
                        $data->errors_fixed++;
                    }
                    $old->fixed = 1;
                    $old->update();
                }
            }
        }
    }

    /**
     * Analyses a build
     * 
     * @param Model_Build &$build     Build
     * @param array       $parameters Processor parameters
     * 
     * @return string Status
     */
    public function analyze(Model_Build &$build, array $parameters)
    {
        return $build->codesniffer_globaldata->buildStatus($parameters);
    }
}
