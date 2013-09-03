<?php

class Controller_Processor_processor2
{

    static public function inputReports()
    {
        return array(
            'raw' => array(
                'title'       => 'Coverage raw report',
                'description' => 'Coverage XML report',
                'type'        => 'file',
                'keep-as'     => 'coverage.xml'
            ),
            'dir' => array(
                'title'       => 'Coverage report directory',
                'description' => 'Coverage HTML report directory',
                'type'        => 'dir',
                'keep-as'     => '.'
            )
        );
    }
    
    static public function parameters() {
          return array(
            'threshold_errors_error'      => array(
                'title'        => 'Errors to trigger error',
                'description'        => 'Number of errors to trigger build error',
                'defaultvalue' => 1
            ),
            'threshold_errors_unstable'   => array(
                'title'        => 'Errors to trigger unstable',
                'description'        => 'Number of errors to trigger unstable build',
                'defaultvalue' => -1
            ),
        );
    }

    public function analyze()
    {
        
    }
}