<?php

/**
 * Codesniffer
 */
class Controller_Processors_Codesniffer extends Controller_Processors_Base
{

    /**
     * Processes a Codesniffer XML report
     * @param int $buildId Build ID
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        $report = Owaka::getReportsPath($buildId, 'codesniffer');

        if (file_exists($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('codesniffer_globaldata');
            $global->build_id = $buildId;
            $global->warnings = 0;
            $global->errors   = 0;

            $xml = simplexml_load_file($report);
            foreach ($xml->children() as $file) {
                foreach ($file->children() as $item) {
                    $error           = ORM::factory('codesniffer_error');
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
                    }

                    if (!empty($error->severity)) {
                        $error->create();
                    }
                }
            }

            $global->create();
            return true;
        }

        return false;
    }
}
