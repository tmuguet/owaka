<?php

/**
 * Codesniffer
 * 
 * @package Processors
 */
class Controller_Processors_Codesniffer extends Controller_Processors_Base
{

    static public function getInputReports()
    {
        return array(
            'xml' => array(
                'title'       => 'Code Sniffer report',
                'description' => 'Code Sniffer XML report in checkstyle format',
                'type'        => 'file',
                'keep-as'     => 'index.xml'
            )
        );
    }

    /**
     * Processes a Codesniffer XML report
     * @param int $buildId Build ID
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
}
