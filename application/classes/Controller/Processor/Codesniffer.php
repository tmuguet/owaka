<?php

/**
 * Codesniffer
 * 
 * @package Processors
 */
class Controller_Processor_Codesniffer extends Controller_Processor
{

    /**
     * Gets the input reports
     * 
     * @return array
     */
    static public function inputReports()
    {
        return array(
            'xml' => array(
                'title'       => 'XML report',
                'description' => 'Code Sniffer XML report in checkstyle format',
                'type'        => 'file',
                'keep-as'     => 'index.xml'
            )
        );
    }

    /**
     * Gets the processor parameters
     * 
     * @return array
     */
    static public function parameters()
    {
        return array(
            'threshold_errors_error'      => array(
                'title'        => 'Number of errors to trigger build error',
                'defaultvalue' => 1
            ),
            'threshold_errors_unstable'   => array(
                'title'        => 'Number of errors to trigger unstable build',
                'defaultvalue' => -1
            ),
            'threshold_warnings_error'    => array(
                'title'        => 'Number of warnings to trigger build error',
                'defaultvalue' => -1
            ),
            'threshold_warnings_unstable' => array(
                'title'        => 'Number of warnings to trigger unstable build',
                'defaultvalue' => 1
            )
        );
    }

    /**
     * Processes a Codesniffer XML report
     * 
     * @param int $buildId Build ID
     * 
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        $report = $this->getReportCompletePath($buildId, 'xml');

        if (!empty($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('Codesniffer_Globaldata');
            $global->build_id = $buildId;
            $global->warnings = 0;
            $global->errors   = 0;

            $xml = simplexml_load_file($report);
            foreach ($xml->children() as $file) {
                foreach ($file->children() as $item) {
                    $error           = ORM::factory('Codesniffer_Error');
                    $error->build_id = $buildId;
                    $error->file     = (string) $file['name'];
                    $error->message  = (string) $item['message'];
                    $error->line     = (int) $item['line'];

                    switch ((string) $item['severity']) {
                        case 'warning':
                            $global->warnings++;
                            $error->severity = 'warning';
                            break;

                        case 'error':
                            $global->errors++;
                            $error->severity = 'error';
                            break;

                        default:
                            // ignore
                            continue 2;
                    }

                    $error->create();
                }
            }

            $global->create();
            return true;
        }

        return false;
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
        $data = $build->codesniffer_globaldata;

        if (($parameters['threshold_errors_error'] > 0 && $data->errors >= $parameters['threshold_errors_error']) 
                || ($parameters['threshold_warnings_error'] > 0 && $data->warnings >= $parameters['threshold_warnings_error'])) {
            return Owaka::BUILD_ERROR;
        } else if (($parameters['threshold_errors_unstable'] > 0 && $data->errors >= $parameters['threshold_errors_unstable'])
                || ($parameters['threshold_warnings_unstable'] > 0 && $data->warnings >= $parameters['threshold_warnings_unstable'])) {
            return Owaka::BUILD_UNSTABLE;
        } else {
            return Owaka::BUILD_OK;
        }
    }
}
