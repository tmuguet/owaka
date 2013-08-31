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
            $this->findRegressions($global);
            $global->create();
            return true;
        }

        return false;
    }

    /**
     * Finds regressions and fixes with previous build
     * 
     * @param Model_Codesniffer_Globaldata &$data Current data
     */
    protected function findRegressions(Model_Codesniffer_Globaldata &$data)
    {
        $build     = $data->build;
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
    /* public function analyze(Model_Build &$build)
      {
      if ($build->codesniffer_globaldata->warnings == 0 && $build->codesniffer_globaldata->errors == 0) {
      return 'ok';
      } else if ($build->codesniffer_globaldata->errors == 0) {
      return 'unstable';
      } else {
      return 'error';
      }
      } */
}
